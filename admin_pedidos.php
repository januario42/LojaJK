<?php

session_start();
include("conexao.php");

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Visualizar pedido
if (isset($_GET['visualizar'])) {
    $id = intval($_GET['visualizar']);
    $stmt = $conexao->prepare("SELECT p.id, p.total, p.status, p.data_criacao, u.nome AS cliente FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($pid, $total, $status, $data, $cliente);
    $stmt->fetch();
    $stmt->close();
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Visualizar Pedido</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-4">
            <h2>Dados do Pedido</h2>
            <ul class="list-group">
                <li class="list-group-item"><strong>ID:</strong> <?= $pid ?></li>
                <li class="list-group-item"><strong>Cliente:</strong> <?= htmlspecialchars($cliente) ?></li>
                <li class="list-group-item"><strong>Total:</strong> R$ <?= number_format($total,2,',','.') ?></li>
                <li class="list-group-item"><strong>Status:</strong> <?= ucfirst($status) ?></li>
                <li class="list-group-item"><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($data)) ?></li>
            </ul>
            <a href="admin_pedidos.php" class="btn btn-secondary mt-3">Voltar</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Editar pedido
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $status = $_POST['status'];
        $stmt = $conexao->prepare("UPDATE pedidos SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin_pedidos.php");
        exit();
    }
    $stmt = $conexao->prepare("SELECT status FROM pedidos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Editar Pedido</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-4">
            <h2>Editar Pedido</h2>
            <form method="post">
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="pendente" <?= $status === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                        <option value="aprovado" <?= $status === 'aprovado' ? 'selected' : '' ?>>Aprovado</option>
                        <option value="preparando" <?= $status === 'preparando' ? 'selected' : '' ?>>Preparando</option>
                        <option value="enviado" <?= $status === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                        <option value="entregue" <?= $status === 'entregue' ? 'selected' : '' ?>>Entregue</option>
                        <option value="cancelado" <?= $status === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Salvar</button>
                <a href="admin_pedidos.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Excluir pedido
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $stmt = $conexao->prepare("DELETE FROM pedidos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_pedidos.php");
    exit();
}

// Listagem + filtro
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$sql = "SELECT p.id, p.total, p.status, p.data_criacao, u.nome AS cliente FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id";
if ($busca !== '') {
    $sql .= " WHERE u.nome LIKE ? OR p.status LIKE ?";
    $stmt = $conexao->prepare($sql);
    $like = "%$busca%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = $conexao->query($sql);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - Pedidos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .table th, .table td { vertical-align: middle; }
        .badge-pendente { background: #ffc107; color: #000; }
        .badge-aprovado { background: #17a2b8; color: #fff; }
        .badge-preparando { background: #fd7e14; color: #fff; }
        .badge-enviado { background: #007bff; color: #fff; }
        .badge-entregue { background: #28a745; color: #fff; }
        .badge-cancelado { background: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2><i class="fas fa-shopping-basket"></i> Gerenciar Pedidos</h2>
        <form class="row g-3 mb-3" method="get">
            <div class="col-auto">
                <input type="text" name="busca" class="form-control" placeholder="Buscar por cliente ou status" value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                <a href="admin_pedidos.php" class="btn btn-secondary">Limpar</a>
            </div>
        </form>
        <table class="table table-hover table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($p = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><i class="fas fa-user"></i> <?= htmlspecialchars($p['cliente']) ?></td>
                    <td>R$ <?= number_format($p['total'],2,',','.') ?></td>
                    <td>
                        <?php
                        $status = strtolower($p['status']);
                        $badge = "badge-$status";
                        ?>
                        <span class="badge <?= $badge ?>"><?= ucfirst($status) ?></span>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($p['data_criacao'])) ?></td>
                    <td>
                        <a href="admin_pedidos.php?visualizar=<?= $p['id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="admin_pedidos.php?editar=<?= $p['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <a href="admin_pedidos.php?excluir=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirma exclusão?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="perfil.php" class="btn btn-secondary mb-3">
    <i class="fas fa-arrow-left"></i> Voltar
</a>