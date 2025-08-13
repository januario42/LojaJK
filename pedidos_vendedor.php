<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}

$vendedor_id = $_SESSION['id_usuario'];

// -----------------------------
// Atualizar status do pedido
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id']) && isset($_POST['novo_status'])) {
    $pedido_id = $_POST['pedido_id'];
    $novo_status = $_POST['novo_status'];

    // Verificar se o pedido tem produtos desse vendedor
    $sql_verificar = "SELECT COUNT(*) as total 
                      FROM itens_pedido ip
                      JOIN produtos pr ON ip.produto_id = pr.id
                      WHERE ip.pedido_id = ? AND pr.id_vendedor = ?";
    $stmt = $conexao->prepare($sql_verificar);
    $stmt->bind_param("ii", $pedido_id, $vendedor_id);
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();

    if ($total > 0) {
        // Atualizar status do pedido
        $sql_atualizar = "UPDATE pedidos SET status = ? WHERE id = ?";
        $stmt = $conexao->prepare($sql_atualizar);
        $stmt->bind_param("si", $novo_status, $pedido_id);
        $stmt->execute();
        $stmt->close();

        // Atualizar datas baseadas no status
        $sql_data = null;
        if ($novo_status === 'preparando') {
            $sql_data = "UPDATE pedidos SET data_pagamento = NOW() WHERE id = ?";
        } elseif ($novo_status === 'enviado') {
            $sql_data = "UPDATE pedidos SET data_envio = NOW() WHERE id = ?";
        } elseif ($novo_status === 'entregue') {
            $sql_data = "UPDATE pedidos SET data_entrega = NOW() WHERE id = ?";
        }

        if ($sql_data) {
            $stmt = $conexao->prepare($sql_data);
            $stmt->bind_param("i", $pedido_id);
            $stmt->execute();
            $stmt->close();
        }

        $mensagem = "Status atualizado com sucesso!";
    }
}

// -----------------------------
// Buscar pedidos do vendedor
// -----------------------------
$sql_pedidos = "SELECT DISTINCT p.*, c.nome AS nome_cliente, c.email AS email_cliente,
                COUNT(ip.id) AS total_itens
                FROM pedidos p
                JOIN itens_pedido ip ON p.id = ip.pedido_id
                JOIN produtos pr ON ip.produto_id = pr.id
                JOIN usuarios c ON p.usuario_id = c.id
                WHERE pr.id_vendedor = ?
                GROUP BY p.id
                ORDER BY p.data_criacao DESC";

$stmt = $conexao->prepare($sql_pedidos);
$stmt->bind_param("i", $vendedor_id);
$stmt->execute();
$pedidos = $stmt->get_result();
$stmt->close();

// -----------------------------
// Estatísticas do vendedor
// -----------------------------
$sql_stats = "SELECT 
                COUNT(DISTINCT p.id) as total_pedidos,
                SUM(CASE WHEN p.status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
                SUM(CASE WHEN p.status = 'aprovado' THEN 1 ELSE 0 END) as aprovados,
                SUM(CASE WHEN p.status = 'preparando' THEN 1 ELSE 0 END) as preparando,
                SUM(CASE WHEN p.status = 'enviado' THEN 1 ELSE 0 END) as enviados,
                SUM(CASE WHEN p.status = 'entregue' THEN 1 ELSE 0 END) as entregues
              FROM pedidos p
              JOIN itens_pedido ip ON p.id = ip.pedido_id
              JOIN produtos pr ON ip.produto_id = pr.id
              WHERE pr.id_vendedor = ?";

$stmt = $conexao->prepare($sql_stats);
$stmt->bind_param("i", $vendedor_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pedidos - LojaJK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .stats-card {
            background: linear-gradient(135deg, #025c0e, #038a1a);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .order-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
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
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-shopping-bag"></i> Gerenciar Pedidos</h1>
            <a href="perfil.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar ao Perfil
            </a>
        </div>

        <?php if (isset($mensagem)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $mensagem; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="stats-card">
                    <h4><?php echo $stats['total_pedidos']; ?></h4>
                    <small>Total</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card">
                    <h4><?php echo $stats['pendentes']; ?></h4>
                    <small>Pendentes</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card">
                    <h4><?php echo $stats['aprovados']; ?></h4>
                    <small>Aprovados</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card">
                    <h4><?php echo $stats['preparando']; ?></h4>
                    <small>Preparando</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card">
                    <h4><?php echo $stats['enviados']; ?></h4>
                    <small>Enviados</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card">
                    <h4><?php echo $stats['entregues']; ?></h4>
                    <small>Entregues</small>
                </div>
            </div>
        </div>

        <!-- Lista de Pedidos -->
        <div class="row">
            <?php if ($pedidos->num_rows > 0): ?>
                <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                    <div class="col-md-6 mb-3">
                        <div class="order-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1">Pedido #<?php echo $pedido['id']; ?></h5>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo date('d/m/Y H:i', strtotime($pedido['data_criacao'])); ?>
                                    </small>
                                </div>
                                <span class="status-badge status-<?php echo $pedido['status']; ?>">
                                    <?php echo ucfirst($pedido['status']); ?>
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <p class="mb-1"><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nome_cliente']); ?></p>
                                <p class="mb-1"><strong>E-mail:</strong> <?php echo htmlspecialchars($pedido['email_cliente']); ?></p>
                                <p class="mb-1"><strong>Total:</strong> R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></p>
                                <p class="mb-0"><strong>Itens:</strong> <?php echo $pedido['total_itens']; ?> produto(s)</p>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="detalhes_pedido_vendedor.php?id=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Ver Detalhes
                                </a>
                                
                                <?php if ($pedido['status'] !== 'entregue' && $pedido['status'] !== 'cancelado'): ?>
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#statusModal<?php echo $pedido['id']; ?>">
                                        <i class="fas fa-edit"></i> Atualizar Status
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para atualizar status -->
                    <div class="modal fade" id="statusModal<?php echo $pedido['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Atualizar Status - Pedido #<?php echo $pedido['id']; ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Novo Status:</label>
                                            <select name="novo_status" class="form-select" required>
                                                <option value="">Selecione...</option>
                                                <option value="aprovado" <?php echo $pedido['status'] === 'pendente' ? '' : 'disabled'; ?>>Aprovado</option>
                                                <option value="preparando" <?php echo in_array($pedido['status'], ['pendente', 'aprovado']) ? '' : 'disabled'; ?>>Preparando</option>
                                                <option value="enviado" <?php echo in_array($pedido['status'], ['pendente', 'aprovado', 'preparando']) ? '' : 'disabled'; ?>>Enviado</option>
                                                <option value="entregue" <?php echo in_array($pedido['status'], ['pendente', 'aprovado', 'preparando', 'enviado']) ? '' : 'disabled'; ?>>Entregue</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Atualizar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum pedido encontrado</h5>
                        <p class="text-muted">Ainda não há pedidos para seus produtos.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 