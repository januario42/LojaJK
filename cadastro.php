<?php
session_start();
include("conexao.php");

// Configurações de segurança
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para gerar um token seguro
function gerarToken() {
    return bin2hex(random_bytes(32));
}

// Validação de senha
function validarSenha($senha) {
    if (strlen($senha) < 8) return "A senha deve ter pelo menos 8 caracteres.";
    if (!preg_match('/[A-Z]/', $senha)) return "A senha deve conter pelo menos uma letra maiúscula.";
    if (!preg_match('/[a-z]/', $senha)) return "A senha deve conter pelo menos uma letra minúscula.";
    if (!preg_match('/\d/', $senha)) return "A senha deve conter pelo menos um número.";
    if (!preg_match('/[\W]/', $senha)) return "A senha deve conter pelo menos um caractere especial.";
    return true;
}

// Processa o cadastro apenas via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $cpf_cnpj = $tipo_usuario == 'vendedor' ? trim($_POST['cpf_cnpj']) : NULL;
    $nome_loja = $tipo_usuario == 'vendedor' ? trim($_POST['nome_loja']) : NULL;
    $token = gerarToken();

    // Validação de senha
    $validacaoSenha = validarSenha($senha);
    if ($validacaoSenha !== true) {
        echo json_encode(["status" => "error", "mensagem" => $validacaoSenha]);
        exit();
    }

    // Hash seguro da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Upload de imagem (opcional)
    $imagem_perfil = 'default.png';
    if (!empty($_FILES['imagem']['name'])) {
        $diretorio = "uploads/";
        if (!is_dir($diretorio)) mkdir($diretorio, 0777, true);

        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo json_encode(["status" => "error", "mensagem" => "Formato de imagem inválido!"]);
            exit();
        }

        $imagem_perfil = uniqid() . "." . $extensao;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio . $imagem_perfil);
    }

    // Insere no banco de dados
    $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, cpf_cnpj, nome_loja, imagem_perfil, token) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssssssss", $nome, $email, $senha_hash, $tipo_usuario, $cpf_cnpj, $nome_loja, $imagem_perfil, $token);

    try {
        $stmt->execute();
        echo json_encode(["status" => "success", "mensagem" => "Cadastro realizado com sucesso! Redirecionando..."]);
    } catch (mysqli_sql_exception $e) {
        echo json_encode(["status" => "error", "mensagem" => "Erro ao cadastrar. Tente novamente mais tarde."]);
    }
    exit();
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - VENANCIO</title>
    <link rel="stylesheet" href="cadastro.css">
</head>
<body>

<div class="cadastro-container">
    <h2>Cadastro</h2>
    
    <form id="cadastroForm" method="POST" enctype="multipart/form-data">
        <input type="text" name="nome" placeholder="Nome Completo" required>
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" id="senha" placeholder="Senha" required onkeyup="verificarForcaSenha()">
        <span id="forcaSenha">Força da Senha</span>

        <label>Tipo de Conta:</label>
        <select name="tipo_usuario" id="tipo_usuario" required>
            <option value="comprador">Comprador</option>
            <option value="vendedor">Vendedor</option>
        </select>

        <div id="vendedorCampos" style="display: none;">
            <input type="text" name="cpf_cnpj" placeholder="CPF ou CNPJ">
            <input type="text" name="nome_loja" placeholder="Nome da Loja">
        </div>

        <label>Imagem de Perfil (opcional):</label>
        <input type="file" name="imagem" accept="image/*">

        <button type="submit">Cadastrar</button>
        
        <p id="mensagemRetorno"></p>

        <p><a href="login.php" class="voltar-login">Já tem uma conta? Faça login</a></p>
    </form>
</div>

<script>
    function verificarForcaSenha() {
        var senha = document.getElementById("senha").value;
        var forca = document.getElementById("forcaSenha");
        var nivel = "Fraca", cor = "red";

        if (senha.length >= 8 && /[A-Z]/.test(senha) && /[a-z]/.test(senha) && /\d/.test(senha) && /[\W]/.test(senha)) {
            nivel = "Forte"; cor = "green";
        } else if (senha.length >= 6) {
            nivel = "Média"; cor = "orange";
        }
        forca.innerHTML = nivel;
        forca.style.color = cor;
    }

    document.getElementById("tipo_usuario").addEventListener("change", function () {
        const isVendedor = this.value === "vendedor";
        const campos = document.getElementById("vendedorCampos");
        campos.style.display = isVendedor ? "block" : "none";

        // Adiciona ou remove o atributo required nos inputs de vendedor
        campos.querySelectorAll("input").forEach(input => {
            input.required = isVendedor;
        });
    });

    document.getElementById("cadastroForm").addEventListener("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        fetch("cadastro.php", { method: "POST", body: formData })
            .then(response => response.json())
            .then(data => {
                let mensagem = document.getElementById("mensagemRetorno");
                mensagem.textContent = data.mensagem;
                mensagem.style.color = data.status === "success" ? "green" : "red";

                if (data.status === "success") {
                    setTimeout(() => window.location.href = "login.php", 2000);
                }
            });
    });

    // Executa a função ao carregar a página para manter consistência ao editar tipo
    window.addEventListener("DOMContentLoaded", () => {
        document.getElementById("tipo_usuario").dispatchEvent(new Event("change"));
    });
</script>

</body>
</html>
