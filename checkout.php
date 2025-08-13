<?php
session_start();
include("conexao.php");
require __DIR__ . '/vendor/autoload.php';
include("config_mercadopago.php");

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Verificar se há produtos no carrinho
if (empty($_SESSION['carrinho'])) {
    header("Location: carrinho.php");
    exit();
}

// Buscar dados do usuário
$stmt = $conexao->prepare("SELECT nome, email FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Buscar produtos do carrinho
$ids = array_keys($_SESSION['carrinho']);
$ids_string = implode(',', $ids);
$sql = "SELECT * FROM produtos WHERE id IN ($ids_string)";
$result = $conexao->query($sql);

$produtos = [];
$total = 0;

while ($produto = $result->fetch_assoc()) {
    $quantidade = $_SESSION['carrinho'][$produto['id']];
    $subtotal = $produto['preco'] * $quantidade;
    $total += $subtotal;
    
    $produtos[] = [
        'produto' => $produto,
        'quantidade' => $quantidade,
        'subtotal' => $subtotal
    ];
}

// Processar pagamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Salvar pedido no banco de dados
        $external_reference = "pedido_" . time() . "_" . $_SESSION['id_usuario'];
        
        $stmt = $conexao->prepare("INSERT INTO pedidos (usuario_id, external_reference, total, endereco, cidade, cep, telefone) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdssss", $_SESSION['id_usuario'], $external_reference, $total, $_POST['endereco'], $_POST['cidade'], $_POST['cep'], $_POST['telefone']);
        $stmt->execute();
        
        $pedido_id = $conexao->insert_id;
        
        // Salvar itens do pedido
   foreach ($produtos as $p) {
    $stmt_item = $conexao->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, usuario_id, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_item->bind_param(
        "iiiidd",
        $pedido_id,
        $p['produto']['id'],
        $_SESSION['id_usuario'], // <-- usuário logado
        $p['quantidade'],
        $p['produto']['preco'],
        $p['subtotal']
    );
    $stmt_item->execute();
}

        // Configurar Mercado Pago
        MercadoPagoConfig::setAccessToken(getMercadoPagoToken());
        
        // Criar itens para a preferência
        $items = [];
        foreach ($produtos as $item) {
            $preco = floatval($item['produto']['preco']);
            if ($preco <= 0) {
                throw new Exception("Preço inválido para o produto: " . $item['produto']['nome']);
            }
            
            $items[] = [
                "id" => (string)$item['produto']['id'],
                "title" => $item['produto']['nome'],
                "description" => $item['produto']['nome'],
                "currency_id" => "BRL",
                "quantity" => (int)$item['quantidade'],
                "unit_price" => $preco
            ];
        }
        
        // Configurar pagador
        $payer = [
            "name" => $usuario['nome'],
            "email" => $usuario['email']
        ];
        
        // Configurar métodos de pagamento
        $paymentMethods = [
            "excluded_payment_methods" => [],
            "installments" => 12,
            "default_installments" => 1
        ];
        
        // Configurar URLs de retorno
        $backUrls = [
            "success" => MP_SUCCESS_URL,
            "failure" => MP_FAILURE_URL,
            "pending" => MP_PENDING_URL
        ];
        
        // Criar requisição da preferência
        $request = [
            "items" => $items,
            "payer" => $payer,
            "payment_methods" => $paymentMethods,
            "back_urls" => $backUrls,
            "statement_descriptor" => MP_STORE_NAME,
            "external_reference" => $external_reference,
            "expires" => false
        ];
        
        // Criar cliente de preferência
        $client = new PreferenceClient();
        
        // Criar preferência
        $preference = $client->create($request);
        
        // Redirecionar para o Mercado Pago
        header("Location: " . $preference->init_point);
        exit();
        
    } catch (MPApiException $e) {
        // Lidar com erros de API do MercadoPago
        $erro = "Erro na API do MercadoPago: " . $e->getMessage();
        $erro .= "<br>Código: " . $e->getApiResponse()->getStatusCode();
        $erro .= "<br>Resposta: " . json_encode($e->getApiResponse()->getContent());
        echo $erro;
    } catch (Exception $e) {
        echo "Erro ao processar pagamento: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - LojaJK</title>
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
            margin: 30px auto;
        }

        h2 {
            color: #025c0e;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .produto-item {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fafafa;
        }

        .produto-imagem {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .total-box {
            background: #025c0e;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .btn-pagar {
            background: #007bff;
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 18px;
            width: 100%;
        }

        .btn-pagar:hover {
            background: #0056b3;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #025c0e;
        }

        .form-control:focus {
            border-color: #025c0e;
            box-shadow: 0 0 0 0.2rem rgba(2, 92, 14, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <!-- Resumo do Pedido -->
            <div class="col-md-8">
                <div class="container-box">
                    <h2><i class="fas fa-shopping-bag"></i> Resumo do Pedido</h2>
                    
                    <?php foreach ($produtos as $item): ?>
                        <div class="produto-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="produtos/<?php echo htmlspecialchars($item['produto']['imagem']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['produto']['nome']); ?>" 
                                         class="produto-imagem">
                                </div>
                                <div class="col-md-6">
                                    <h6><?php echo htmlspecialchars($item['produto']['nome']); ?></h6>
                                    <small class="text-muted">Quantidade: <?php echo $item['quantidade']; ?></small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <strong>R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="total-box">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Total do Pedido</h4>
                            </div>
                            <div class="col-md-6 text-end">
                                <h2>R$ <?php echo number_format($total, 2, ',', '.'); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulário de Pagamento -->
            <div class="col-md-4">
                <div class="container-box">
                    <h2><i class="fas fa-credit-card"></i> Pagamento</h2>
                    
                    <?php if (isset($erro)): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($erro); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario['nome']); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">E-mail</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Endereço de Entrega</label>
                            <input type="text" class="form-control" name="endereco" placeholder="Rua, número, bairro" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Cidade</label>
                            <input type="text" class="form-control" name="cidade" placeholder="Sua cidade" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">CEP</label>
                            <input type="text" class="form-control" name="cep" placeholder="00000-000" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Telefone</label>
                            <input type="text" class="form-control" name="telefone" placeholder="(00) 00000-0000" required>
                        </div>

                        <button type="submit" class="btn-pagar">
                            <i class="fas fa-lock"></i> Pagar com Mercado Pago
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="carrinho.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar ao Carrinho
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
