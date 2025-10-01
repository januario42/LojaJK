<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id_usuario'];
$sql = "SELECT nome, email, tipo_usuario, nome_loja, imagem_perfil FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($nome, $email, $tipo_usuario, $nome_loja, $imagem_perfil);
$stmt->fetch();
$stmt->close();

// Buscar pedidos do usuário
$sql_pedidos = "SELECT id, external_reference, total, status, data_criacao, data_pagamento FROM pedidos WHERE usuario_id = ? ORDER BY data_criacao DESC LIMIT 10";
$stmt = $conexao->prepare($sql_pedidos);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$pedidos = $stmt->get_result();
$stmt->close();

// Buscar estatísticas para vendedor
if ($tipo_usuario === 'vendedor') {
    // Total de produtos
    $sql_produtos = "SELECT COUNT(*) as total FROM produtos WHERE id_vendedor = ?";
    $stmt = $conexao->prepare($sql_produtos);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($total_produtos);
    $stmt->fetch();
    $stmt->close();

    // Total de vendas (pedidos entregues)
    $sql_vendas = "SELECT COUNT(DISTINCT p.id) AS total_vendas
                   FROM itens_pedido ip
                   JOIN pedidos p ON ip.pedido_id = p.id
                   JOIN produtos pr ON ip.produto_id = pr.id
                   WHERE pr.id_vendedor = ? AND p.status = 'entregue'";
    $stmt = $conexao->prepare($sql_vendas);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($total_vendas);
    $stmt->fetch();
    $stmt->close();

    // Faturamento total (somente pedidos entregues)
    $sql_faturamento = "SELECT SUM(ip.subtotal) AS faturamento_total
                        FROM itens_pedido ip
                        JOIN pedidos p ON ip.pedido_id = p.id
                        JOIN produtos pr ON ip.produto_id = pr.id
                        WHERE pr.id_vendedor = ? AND p.status = 'entregue'";
    $stmt = $conexao->prepare($sql_faturamento);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($faturamento_total);
    $stmt->fetch();
    $stmt->close();
    $faturamento_total = $faturamento_total ?: 0;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - LojaJK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/perfil.css">
    <style>
        .stats-card {
            background: linear-gradient(135deg, #025c0e, #038a1a);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stats-card-admin {
            background: linear-gradient(135deg, #1a237e, #3949ab);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pendente { background: #ffc107; color: #000; }
        .status-aprovado { background: #17a2b8; color: white; }
        .status-preparando { background: #fd7e14; color: white; }
        .status-enviado { background: #007bff; color: white; }
        .status-entregue { background: #28a745; color: white; }
        .status-cancelado { background: #dc3545; color: white; }
        .order-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .order-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <header class="profile-header">
        <a href="logado.php"><i class="fas fa-arrow-left"></i> Voltar</a>
        <h1>Minha Conta</h1>
    </header>

    <div class="profile-container">
        <div class="profile-sidebar">
            <img src="uploads/<?php echo htmlspecialchars($imagem_perfil); ?>" alt="Imagem de Perfil">
            <h4><?php echo htmlspecialchars($nome); ?></h4>
            <p><?php echo htmlspecialchars($email); ?></p>

            <?php if ($tipo_usuario === 'vendedor'): ?>
                <h5>Loja: <?php echo htmlspecialchars($nome_loja); ?></h5>
                <div class="stats-card">
                    <h6><i class="fas fa-box"></i> Produtos</h6>
                    <h3><?php echo $total_produtos; ?></h3>
                </div>
                <div class="stats-card">
                    <h6><i class="fas fa-shopping-cart"></i> Vendas</h6>
                    <h3><?php echo $total_vendas; ?></h3>
                </div>
                <div class="stats-card">
                    <h6><i class="fas fa-dollar-sign"></i> Faturamento</h6>
                    <h3>R$ <?php echo number_format($faturamento_total, 2, ',', '.'); ?></h3>
                </div>
                <a href="editar_perfil.php" class="btn btn-edit-profile">Editar Perfil</a>
                <a href="adicionar_produto.php" class="btn btn-custom">Adicionar Produto</a>
                <a href="meus_produtos.php" class="btn btn-custom">Gerenciar Produtos</a>
                <a href="dashboard_vendedor.php" class="btn btn-custom">Dashboard</a>
                <a href="pedidos_vendedor.php" class="btn btn-custom">Meus Pedidos</a>
            <?php elseif ($tipo_usuario === 'admin'): ?>
                <h5>Administrador</h5>
                <div class="stats-card-admin">
                    <h6><i class="fas fa-users"></i> Gerenciar Usuários</h6>
                    <a href="admin_usuarios.php" class="btn btn-light btn-sm">Usuários</a>
                </div>
                <div class="stats-card-admin">
                    <h6><i class="fas fa-boxes"></i> Gerenciar Produtos</h6>
                    <a href="admin_produtos.php" class="btn btn-light btn-sm">Produtos</a>
                </div>
                <div class="stats-card-admin">
                    <h6><i class="fas fa-shopping-basket"></i> Gerenciar Pedidos</h6>
                    <a href="admin_pedidos.php" class="btn btn-light btn-sm">Pedidos</a>
                </div>
                <a href="editar_perfil.php" class="btn btn-edit-profile">Editar Perfil</a>
            <?php else: ?>
                <a href="editar_perfil.php" class="btn btn-edit-profile">Editar Perfil</a>
                <a href="carrinho.php" class="btn btn-custom">Meu Carrinho</a>
                <a href="favoritos.php" class="btn btn-custom">Favoritos</a>
            <?php endif; ?>
        </div>

        <div class="profile-content">
            <h2><i class="fas fa-shopping-bag"></i> Meus Pedidos</h2>
            <?php if ($pedidos->num_rows > 0): ?>
                <div class="row">
                    <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                        <div class="col-md-6 mb-3">
                            <div class="order-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">Pedido #<?php echo $pedido['id']; ?></h6>
                                    <span class="status-badge status-<?php echo $pedido['status']; ?>">
                                        <?php echo ucfirst($pedido['status']); ?>
                                    </span>
                                </div>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-calendar"></i> 
                                    <?php echo date('d/m/Y H:i', strtotime($pedido['data_criacao'])); ?>
                                </p>
                                <p class="mb-2">
                                    <strong>Total: R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></strong>
                                </p>
                                <a href="detalhes_pedido.php?id=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Ver Detalhes
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum pedido encontrado</h5>
                    <p class="text-muted">Faça sua primeira compra para ver seus pedidos aqui!</p>
                    <a href="logado.php" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Fazer Compras
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>