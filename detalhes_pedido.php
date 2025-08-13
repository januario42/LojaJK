<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$pedido_id = $_GET['id'] ?? 0;
$usuario_id = $_SESSION['id_usuario'];

// Buscar dados do pedido
$sql_pedido = "SELECT p.*, u.nome as nome_cliente, u.email as email_cliente 
               FROM pedidos p 
               JOIN usuarios u ON p.usuario_id = u.id 
               WHERE p.id = ? AND p.usuario_id = ?";
$stmt = $conexao->prepare($sql_pedido);
$stmt->bind_param("ii", $pedido_id, $usuario_id);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pedido) {
    header("Location: perfil.php");
    exit();
}

// Buscar itens do pedido
$sql_itens = "SELECT ip.*, p.nome as nome_produto, p.imagem as imagem_produto, u.nome as nome_vendedor
              FROM itens_pedido ip
              JOIN produtos p ON ip.produto_id = p.id
              JOIN usuarios u ON p.id_vendedor = u.id
              WHERE ip.pedido_id = ?";
$stmt = $conexao->prepare($sql_itens);
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$itens = $stmt->get_result();
$stmt->close();


// Função para obter o status em português
function getStatusText($status) {
    $status_map = [
        'pendente' => 'Pendente',
        'aprovado' => 'Aprovado',
        'preparando' => 'Preparando',
        'enviado' => 'Enviado',
        'entregue' => 'Entregue',
        'cancelado' => 'Cancelado'
    ];
    return $status_map[$status] ?? $status;
}

// Função para obter a cor do status
function getStatusColor($status) {
    $color_map = [
        'pendente' => 'warning',
        'aprovado' => 'info',
        'preparando' => 'orange',
        'enviado' => 'primary',
        'entregue' => 'success',
        'cancelado' => 'danger'
    ];
    return $color_map[$status] ?? 'secondary';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido #<?php echo $pedido_id; ?> - LojaJK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .order-header {
            background: linear-gradient(135deg, #025c0e, #038a1a);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        .order-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 14px;
        }
        .product-item {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fafafa;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #025c0e;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -22px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #025c0e;
        }
        .timeline-item.active::before {
            background: #28a745;
        }
    </style>
</head>
<body>
    <div class="order-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0">
                        <i class="fas fa-shopping-bag"></i> Pedido #<?php echo $pedido_id; ?>
                    </h1>
                    <p class="mb-0"><?php echo date('d/m/Y H:i', strtotime($pedido['data_criacao'])); ?></p>
                </div>
                <div class="text-end">
                    <span class="status-badge bg-<?php echo getStatusColor($pedido['status']); ?>">
                        <?php echo getStatusText($pedido['status']); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Informações do Pedido -->
            <div class="col-md-8">
                <div class="order-card">
                    <h4><i class="fas fa-box"></i> Produtos do Pedido</h4>
                    
                    <?php while ($item = $itens->fetch_assoc()): ?>
                        <div class="product-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="produtos/<?php echo htmlspecialchars($item['imagem_produto']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['nome_produto']); ?>" 
                                         class="product-image">
                                </div>
                                <div class="col-md-6">
                                    <h6><?php echo htmlspecialchars($item['nome_produto']); ?></h6>
                                    <small class="text-muted">Vendedor: <?php echo htmlspecialchars($item['nome_vendedor']); ?></small><br>
                                    <small class="text-muted">Quantidade: <?php echo $item['quantidade']; ?></small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <strong>R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    
                    <div class="text-end mt-3">
                        <h5>Total: R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></h5>
                    </div>
                </div>

                <!-- Endereço de Entrega -->
                <div class="order-card">
                    <h4><i class="fas fa-map-marker-alt"></i> Endereço de Entrega</h4>
                    <p><strong>Endereço:</strong> <?php echo htmlspecialchars($pedido['endereco']); ?></p>
                    <p><strong>Cidade:</strong> <?php echo htmlspecialchars($pedido['cidade']); ?></p>
                    <p><strong>CEP:</strong> <?php echo htmlspecialchars($pedido['cep']); ?></p>
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($pedido['telefone']); ?></p>
                </div>
            </div>

            <!-- Status e Timeline -->
            <div class="col-md-4">
                <div class="order-card">
                    <h4><i class="fas fa-clock"></i> Status do Pedido</h4>
                    
                    <div class="timeline">
                        <div class="timeline-item <?php echo in_array($pedido['status'], ['pendente', 'aprovado', 'preparando', 'enviado', 'entregue']) ? 'active' : ''; ?>">
                            <h6>Pedido Realizado</h6>
                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($pedido['data_criacao'])); ?></small>
                        </div>
                        
                        <div class="timeline-item <?php echo in_array($pedido['status'], ['aprovado', 'preparando', 'enviado', 'entregue']) ? 'active' : ''; ?>">
                            <h6>Pagamento Aprovado</h6>
                            <small class="text-muted">
                                <?php echo $pedido['data_pagamento'] ? date('d/m/Y H:i', strtotime($pedido['data_pagamento'])) : 'Aguardando'; ?>
                            </small>
                        </div>
                        
                        <div class="timeline-item <?php echo in_array($pedido['status'], ['preparando', 'enviado', 'entregue']) ? 'active' : ''; ?>">
                            <h6>Preparando Pedido</h6>
                            <small class="text-muted">Em preparação</small>
                        </div>
                        
                        <div class="timeline-item <?php echo in_array($pedido['status'], ['enviado', 'entregue']) ? 'active' : ''; ?>">
                            <h6>Pedido Enviado</h6>
                            <small class="text-muted">
                                <?php echo $pedido['data_envio'] ? date('d/m/Y H:i', strtotime($pedido['data_envio'])) : 'Aguardando'; ?>
                            </small>
                        </div>
                        
                        <div class="timeline-item <?php echo $pedido['status'] === 'entregue' ? 'active' : ''; ?>">
                            <h6>Pedido Entregue</h6>
                            <small class="text-muted">
                                <?php echo $pedido['data_entrega'] ? date('d/m/Y H:i', strtotime($pedido['data_entrega'])) : 'Aguardando'; ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Informações do Cliente -->
                <div class="order-card">
                    <h4><i class="fas fa-user"></i> Informações do Cliente</h4>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($pedido['nome_cliente']); ?></p>
                    <p><strong>E-mail:</strong> <?php echo htmlspecialchars($pedido['email_cliente']); ?></p>
                    <p><strong>Referência:</strong> <?php echo htmlspecialchars($pedido['external_reference']); ?></p>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 mb-4">
            <a href="perfil.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar ao Perfil
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 