<?php
session_start();
include("conexao.php");

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Processar dados do pagamento
$payment_id = $_GET['payment_id'] ?? '';
$status = $_GET['status'] ?? '';
$external_reference = $_GET['external_reference'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Pendente - LojaJK</title>
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
            padding: 40px;
            margin: 50px auto;
            text-align: center;
        }

        .pending-icon {
            font-size: 4rem;
            color: #ffc107;
            margin-bottom: 20px;
        }

        h1 {
            color: #ffc107;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .btn-warning {
            background: #ffc107;
            border: none;
            color: #212529;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: bold;
        }

        .btn-warning:hover {
            background: #e0a800;
            color: #212529;
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: bold;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .payment-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .info-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="container-box">
            <div class="pending-icon">
                <i class="fas fa-clock"></i>
            </div>
            
            <h1>Pagamento Pendente</h1>
            <p class="lead">Seu pagamento está sendo processado.</p>
            
            <div class="info-box">
                <h5><i class="fas fa-info-circle"></i> O que isso significa?</h5>
                <p>Seu pagamento foi recebido, mas ainda está sendo analisado pelo sistema de pagamento. 
                Isso pode acontecer com pagamentos via boleto bancário ou transferência.</p>
            </div>
            
            <?php if (!empty($payment_id)): ?>
                <div class="payment-details">
                    <h5>Detalhes do Pagamento</h5>
                    <p><strong>ID do Pagamento:</strong> <?php echo htmlspecialchars($payment_id); ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-warning text-dark">Pendente</span></p>
                    <?php if (!empty($external_reference)): ?>
                        <p><strong>Referência:</strong> <?php echo htmlspecialchars($external_reference); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <p>Você receberá uma confirmação por e-mail assim que o pagamento for aprovado.</p>
            <p>Seus produtos permanecerão reservados até a confirmação do pagamento.</p>
            
            <div class="mt-4">
                <a href="logado.php" class="btn btn-warning me-2">
                    <i class="fas fa-home"></i> Voltar à Loja
                </a>
                <a href="perfil.php" class="btn btn-secondary">
                    <i class="fas fa-user"></i> Meu Perfil
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 