<?php
session_start();
include("conexao.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carregar o autoloader do Composer
require 'vendor/autoload.php'; 

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    // Verifica se o e-mail está cadastrado
    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $codigo = rand(100000, 999999); // Código de 6 dígitos
        $expiracao = date("Y-m-d H:i:s", strtotime("+15 minutes")); // Código válido por 15 minutos

        // Salva o código no banco de dados
        $sql = "UPDATE usuarios SET token = ?, expiracao_token = ? WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssi", $codigo, $expiracao, $usuario['id']);
        $stmt->execute();

        // Enviar e-mail com o código
        $assunto = "Código de Verificação - Redefinição de Senha";
        $mensagemEmail = "
        Olá,<br><br>
        Seu código de verificação para redefinição de senha é: <strong>$codigo</strong>.<br><br>
        Este código é válido por 15 minutos. Se você não solicitou essa mudança, ignore este e-mail.<br><br>
        Atenciosamente,<br>
        Equipe [Nome do Seu Site]
        ";

        // Configuração do PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'teste342233@gmail.com';
            $mail->Password = 'pfpr qpkz ellm ifcm';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('no-reply@seusite.com', 'Sua Loja');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body    = $mensagemEmail;

            $mail->send();
            $_SESSION['email_verificacao'] = $email; // Salva o e-mail na sessão para verificar depois
            header("Location: verificar_codigo.php"); // Redireciona para a página de verificação
            exit();
        } catch (Exception $e) {
            $mensagem = "Erro ao enviar e-mail. Tente novamente.";
        }
    } else {
        $mensagem = "E-mail não encontrado.";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Recuperação de Senha</title>
  <link rel="stylesheet" href="./css/recuperar_senha.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
  <script>
    function esconderMensagem() {
      setTimeout(() => {
        const msgDiv = document.getElementById("mensagem");
        if (msgDiv) msgDiv.style.display = "none";
      }, 3000);
    }
  </script>
</head>
<body onload="esconderMensagem()">
  <div class="background">
    <div class="container">
      <div class="header">
        <h2>🔒 Recuperação de Senha</h2>
        <p>Insira seu e-mail para receber seu código de verificação.</p>
      </div>
      <?php if (!empty($mensagem)): ?>
        <div id="mensagem" class="mensagem"> 
          <?php echo $mensagem; ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="formulario">
        <div class="input-group">
          <input type="email" name="email" placeholder="Digite seu e-mail" required>
          <span class="icon">📧</span>
        </div>
        <button type="submit">Enviar Código</button>
      </form>
      
      <div class="footer">
        <a href="login.php">Voltar para o Login</a>
      </div>
    </div>
  </div>
</body>
</html>