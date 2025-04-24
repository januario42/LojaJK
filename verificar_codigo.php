<?php
session_start();
include("conexao.php");

$mensagem = "";

// Verifica se a sessão com o email existe
if (!isset($_SESSION['email_verificacao'])) {
    header("Location: recuperar_senha.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['email_verificacao'];
    $codigo_digitado = trim($_POST['codigo']);

    // Verifica o código no banco
    $sql = "SELECT id, expiracao_token FROM usuarios WHERE email = ? AND token = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ss", $email, $codigo_digitado); // Corrigido para "ss" já que ambos são strings
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (strtotime($usuario['expiracao_token']) > time()) {
            $_SESSION['usuario_redefinir'] = $usuario['id']; // Salva o ID para redefinir a senha
            header("Location: redefinir_senha.php");
            exit();
        } else {
            $mensagem = "Código expirado. Solicite um novo.";
        }
    } else {
        $mensagem = "Código inválido. Tente novamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verificação de Código</title>
<link rel="stylesheet" href="verificar_codigo.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>🔒 Verificação de Código</h2>
            <p>Insira o código de 6 dígitos enviado para o seu e-mail.</p>
            
            <?php if (!empty($mensagem)) { echo "<div class='mensagem'>$mensagem</div>"; } ?>

            <form method="post">
                <label for="codigo">Código de Verificação</label>
                <input type="text" id="codigo" name="codigo" required maxlength="6" pattern="[0-9]{6}" placeholder="000000">
                <button type="submit">Verificar Código</button>
            </form>

            <a href="recuperar_senha.php" class="voltar">← Voltar</a>
        </div>
    </div>
</body>
</html>
