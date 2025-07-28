<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Falhou - LojaJK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container-box {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin: 50px auto;
            max-width: 600px;
            text-align: center;
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .btn-primary {
            background: #007bff;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: bold;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="container-box">
            <div class="error-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            
            <h2 class="text-danger mb-4">Pagamento Falhou</h2>
            
            <p class="lead mb-4">
                Infelizmente, seu pagamento não foi processado com sucesso. 
                Isso pode ter acontecido por diversos motivos:
            </p>
            
            <ul class="text-start mb-4">
                <li>Dados do cartão incorretos</li>
                <li>Saldo insuficiente</li>
                <li>Cartão bloqueado</li>
                <li>Problemas temporários no processamento</li>
            </ul>
            
            <div class="alert alert-info">
                <strong>Dica:</strong> Verifique os dados do seu cartão e tente novamente.
            </div>
            
            <div class="mt-4">
                <a href="carrinho.php" class="btn btn-primary me-3">
                    <i class="fas fa-shopping-cart"></i> Voltar ao Carrinho
                </a>
                <a href="logado.php" class="btn btn-outline-secondary">
                    <i class="fas fa-home"></i> Voltar à Loja
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 