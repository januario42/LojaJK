<?php
session_start();
include("conexao.php");

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Inicializar carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$mensagem = "";
$total = 0;
$produtos_carrinho = [];

// Buscar produtos do carrinho
if (!empty($_SESSION['carrinho'])) {
    $ids = array_keys($_SESSION['carrinho']);
    $ids_string = implode(',', $ids);
    
    $sql = "SELECT * FROM produtos WHERE id IN ($ids_string)";
    $result = $conexao->query($sql);
    
    while ($produto = $result->fetch_assoc()) {
        $quantidade = $_SESSION['carrinho'][$produto['id']];
        $subtotal = $produto['preco'] * $quantidade;
        $total += $subtotal;
        
        $produtos_carrinho[] = [
            'produto' => $produto,
            'quantidade' => $quantidade,
            'subtotal' => $subtotal
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Carrinho - LojaJK</title>
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
            padding: 20px;
            margin-bottom: 20px;
            background: #fafafa;
        }

        .produto-imagem {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }

        .quantidade-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-quantidade {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: none;
            background: #025c0e;
            color: white;
            font-weight: bold;
        }

        .btn-quantidade:hover {
            background: #018a11;
        }

        .btn-remover {
            background: #dc3545;
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .btn-remover:hover {
            background: #c82333;
        }

        .total-box {
            background: #025c0e;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .btn-finalizar {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 18px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-finalizar:hover {
            background: linear-gradient(45deg, #218838, #1ea085);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .btn-checkout {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            color: white;
            padding: 18px 40px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .btn-checkout:hover {
            background: linear-gradient(45deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
            color: white;
            text-decoration: none;
        }

        .carrinho-vazio {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }

        .carrinho-vazio i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="container-box">
            <h2><i class="fas fa-shopping-cart"></i> Meu Carrinho</h2>

            <?php if (empty($produtos_carrinho)): ?>
                <div class="carrinho-vazio">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Seu carrinho está vazio</h3>
                    <p>Adicione alguns produtos para começar suas compras!</p>
                    <a href="logado.php" class="btn btn-success">
                        <i class="fas fa-arrow-left"></i> Continuar Comprando
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($produtos_carrinho as $item): ?>
                    <div class="produto-item">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="produtos/<?php echo htmlspecialchars($item['produto']['imagem']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['produto']['nome']); ?>" 
                                     class="produto-imagem">
                            </div>
                            <div class="col-md-4">
                                <h5><?php echo htmlspecialchars($item['produto']['nome']); ?></h5>
                                <p class="text-muted"><?php echo htmlspecialchars($item['produto']['descricao']); ?></p>
                            </div>
                            <div class="col-md-2">
                                <strong>R$ <?php echo number_format($item['produto']['preco'], 2, ',', '.'); ?></strong>
                            </div>
                            <div class="col-md-2">
                                <div class="quantidade-control">
                                    <button class="btn-quantidade" onclick="alterarQuantidade(<?php echo $item['produto']['id']; ?>, -1)">-</button>
                                    <span class="quantidade"><?php echo $item['quantidade']; ?></span>
                                    <button class="btn-quantidade" onclick="alterarQuantidade(<?php echo $item['produto']['id']; ?>, 1)">+</button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <strong>R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></strong>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button class="btn-remover" onclick="removerProduto(<?php echo $item['produto']['id']; ?>)">
                                    <i class="fas fa-trash"></i> Remover
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="total-box">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Total do Pedido</h4>
                            <h2>R$ <?php echo number_format($total, 2, ',', '.'); ?></h2>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="checkout.php" class="btn-checkout">
                                <i class="fas fa-credit-card"></i> Finalizar Compra
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="logado.php" class="btn btn-outline-success">
                        <i class="fas fa-arrow-left"></i> Continuar Comprando
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function alterarQuantidade(id, delta) {
            fetch('acoes/alterar_quantidade.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + id + '&delta=' + delta
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert('Erro ao alterar quantidade: ' + data.mensagem);
                }
            });
        }

        function removerProduto(id) {
            if (confirm('Tem certeza que deseja remover este produto do carrinho?')) {
                fetch('acoes/remover_produto.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert('Erro ao remover produto: ' + data.mensagem);
                    }
                });
            }
        }
    </script>
</body>
</html> 