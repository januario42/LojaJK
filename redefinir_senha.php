<?php
session_start();
include("conexao.php");

// Validação de senha
function validarSenha($senha) {
    if (strlen($senha) < 8) return "A senha deve ter pelo menos 8 caracteres.";
    if (!preg_match('/[A-Z]/', $senha)) return "A senha deve conter pelo menos uma letra maiúscula.";
    if (!preg_match('/[a-z]/', $senha)) return "A senha deve conter pelo menos uma letra minúscula.";
    if (!preg_match('/\d/', $senha)) return "A senha deve conter pelo menos um número.";
    if (!preg_match('/[\W]/', $senha)) return "A senha deve conter pelo menos um caractere especial.";
    return true;
}

// Processa a redefinição de senha
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['usuario_redefinir'])) {
        echo json_encode(["status" => "error", "mensagem" => "Sessão expirada. Faça login novamente."]);
        exit();
    }

    $nova_senha = trim($_POST['nova_senha']);
    $confirmar_senha = trim($_POST['confirmar_senha']);
    $usuario_id = $_SESSION['usuario_redefinir'];

    if ($nova_senha !== $confirmar_senha) {
        echo json_encode(["status" => "error", "mensagem" => "As senhas não coincidem."]);
        exit();
    }

    // Validação de senha
    $validacaoSenha = validarSenha($nova_senha);
    if ($validacaoSenha !== true) {
        echo json_encode(["status" => "error", "mensagem" => $validacaoSenha]);
        exit();
    }

    // Hash seguro da nova senha
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

    // Atualiza a senha no banco de dados
    $sql = "UPDATE usuarios SET senha=? WHERE id=?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("si", $senha_hash, $usuario_id);

    if ($stmt->execute()) {
        session_destroy();
        echo json_encode(["status" => "success", "mensagem" => "Senha redefinida com sucesso! Redirecionando para a tela de login..."]);
    } else {
        echo json_encode(["status" => "error", "mensagem" => "Erro ao redefinir a senha. Tente novamente mais tarde."]);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>🔐 Redefinir Senha - VENANCIO</title>
  <link rel="stylesheet" href="./css/redefinir_senha.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" />
</head>
<body>
  <div class="container">
    <div class="form-container">
      <h1>🔒 Redefina Sua Senha</h1>
      <p>Escolha uma nova senha forte para proteger sua conta.</p>
      
      <form id="redefinirSenhaForm">
        <label for="nova_senha">🔑 Nova Senha</label>
        <input type="password" name="nova_senha" id="nova_senha" placeholder="Nova Senha" required>
        
        <label for="confirmar_senha">🔁 Confirmar Nova Senha</label>
        <input type="password" name="confirmar_senha" id="confirmar_senha" placeholder="Confirmar Nova Senha" required>

        <button type="submit">Redefinir Senha</button>
        <p id="mensagemRetorno"></p>
      </form>
    </div>
  </div>

  <script>
    const senhaInput = document.getElementById('nova_senha');
    document.getElementById('redefinirSenhaForm').addEventListener('submit', function(event) {
      event.preventDefault();
      const formData = new FormData(this);

      fetch('redefinir_senha.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        const mensagem = document.getElementById('mensagemRetorno');
        mensagem.textContent = data.mensagem;
        mensagem.style.color = data.status === 'success' ? 'green' : 'red';
        if (data.status === 'success') {
          setTimeout(() => window.location.href = 'login.php', 3000);
        }
      })
      .catch(error => {
        document.getElementById('mensagemRetorno').textContent = '⚠️ Erro ao processar a solicitação. Tente novamente mais tarde.';
        document.getElementById('mensagemRetorno').style.color = 'red';
      });
    });
  </script>
</body>
</html>
