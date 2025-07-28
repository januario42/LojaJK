<?php
session_start();

// Limpar carrinho após pagamento aprovado
if (isset($_SESSION['carrinho'])) {
    unset($_SESSION['carrinho']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Aprovado - LojaJK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.05);
            padding: 40px;
            margin: 50px auto;
            text-align: center;
            max-width: 600px;
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        .btn-primary {
            background: #025c0e;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: bold;
        }
        .btn-primary:hover {
            background: #024a0b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="text-success mb-4">Pagamento Aprovado!</h2>
            <p class="lead mb-4">Seu pedido foi processado com sucesso. Obrigado por escolher a LojaJK!</p>
            
            <div class="alert alert-success">
                <i class="fas fa-info-circle"></i>
                <strong>Próximos passos:</strong><br>
                • Você receberá um e-mail de confirmação<br>
                • Seu pedido será processado e enviado<br>
                • Acompanhe o status pelo seu perfil
            </div>
            
            <div class="mt-4">
                <a href="perfil.php" class="btn btn-primary me-3">
                    <i class="fas fa-user"></i> Meu Perfil
                </a>
                <a href="logado.php" class="btn btn-outline-primary">
                    <i class="fas fa-home"></i> Voltar à Loja
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 