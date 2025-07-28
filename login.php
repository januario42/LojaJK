<?php
session_start();
include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido!";
    } else {
        $sql = "SELECT id, nome, senha, tipo_usuario FROM usuarios WHERE email = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            if (password_verify($senha, $usuario['senha'])) {
                session_regenerate_id(true); // Segurança extra

                $_SESSION['usuario'] = $usuario['nome'];
                $_SESSION['id_usuario'] = $usuario['id'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
                $_SESSION['tentativas_login'] = 0;

                header("Location: logado.php");
                exit();
            } else {
                $_SESSION['tentativas_login'] = ($_SESSION['tentativas_login'] ?? 0) + 1;
                $erro = "Senha incorreta!";
            }
        } else {
            $_SESSION['tentativas_login'] = ($_SESSION['tentativas_login'] ?? 0) + 1;
            $erro = "Usuário não encontrado!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site LojaJK</title>
    <link rel="stylesheet" href="./css/login.css">
    <script>
        // Função para esconder a mensagem de erro após 3 segundos
        function hideError() {
            var errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 3000);
            }
        }
    </script>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2>Login</h2>
            <form method="POST">
                <div class="input-group">
                    <input type="email" name="email" placeholder="Digite seu e-mail" required>
                </div>
                <div class="input-group">
                    <input type="password" name="senha" placeholder="Digite sua senha" required>
                </div>
                <button type="submit">Entrar</button>
                <div class="message">
                    <p>Ainda não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
                    <p><a href="recuperar_senha.php">Esqueceu sua senha?</a></p>
                </div>
            </form>

            <?php if (isset($erro)): ?>
                <div class="error" id="error-message">
                    <span class="error-icon">⚠️</span>
                    <?php echo $erro; ?>
                </div>
                <script>hideError();</script>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
