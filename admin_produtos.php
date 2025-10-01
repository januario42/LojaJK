<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Visualizar produto
if (isset($_GET['visualizar'])) {
    $id = intval($_GET['visualizar']);
    $stmt = $conexao->prepare("SELECT p.nome, p.preco, p.estoque, p.descricao, u.nome AS vendedor FROM produtos p JOIN usuarios u ON p.id_vendedor = u.id WHERE p.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nome, $preco, $estoque, $descricao, $vendedor);
    $stmt->fetch();
    $stmt->close();
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Visualizar Produto</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-4">
            <h2>Dados do Produto</h2>
            <ul class="list-group">
                <li class="list-group-item"><strong>Nome:</strong> <?= htmlspecialchars($nome) ?></li>
                <li class="list-group-item"><strong>Preço:</strong> R$ <?= number_format($preco,2,',','.') ?></li>
                <li class="list-group-item"><strong>Estoque:</strong> <?= $estoque ?></li>
                <li class="list-group-item"><strong>Vendedor:</strong> <?= htmlspecialchars($vendedor) ?></li>
                <li class="list-group-item"><strong>Descrição:</strong> <?= htmlspecialchars($descricao) ?></li>
            </ul>
            <a href="admin_produtos.php" class="btn btn-secondary mt-3">Voltar</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Editar produto
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome']);
        $preco = floatval($_POST['preco']);
        $estoque = intval($_POST['estoque']);
        $descricao = trim($_POST['descricao']);
        $stmt = $conexao->prepare("UPDATE produtos SET nome=?, preco=?, estoque=?, descricao=? WHERE id=?");
        $stmt->bind_param("sdisi", $nome, $preco, $estoque, $descricao, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin_produtos.php");
        exit();
    }
    $stmt = $conexao->prepare("SELECT nome, preco, estoque, descricao FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nome, $preco, $estoque, $descricao);
    $stmt->fetch();
    $stmt->close();
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Editar Produto</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-4">
            <h2>Editar Produto</h2>
            <form method="post">
                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($nome) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Preço</label>
                    <input type="number" step="0.01" name="preco" class="form-control" value="<?= htmlspecialchars($preco) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Estoque</label>
                    <input type="number" name="estoque" class="form-control" value="<?= htmlspecialchars($estoque) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Descrição</label>
                    <textarea name="descricao" class="form-control"><?= htmlspecialchars($descricao) ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Salvar</button>
                <a href="admin_produtos.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Excluir produto
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $stmt = $conexao->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_produtos.php");
    exit();
}

// Listagem + filtro
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$sql = "SELECT p.id, p.nome, p.preco, p.estoque, u.nome AS vendedor FROM produtos p JOIN usuarios u ON p.id_vendedor = u.id";
if ($busca !== '') {
    $sql .= " WHERE p.nome LIKE ? OR u.nome LIKE ?";
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
    <title>Admin - Produtos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .table th, .table td { vertical-align: middle; }
        .badge-estoque { background: #025c0e; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2><i class="fas fa-boxes"></i> Gerenciar Produtos</h2>
        <form class="row g-3 mb-3" method="get">
            <div class="col-auto">
                <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou vendedor" value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                <a href="admin_produtos.php" class="btn btn-secondary">Limpar</a>
            </div>
        </form>
        <table class="table table-hover table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Vendedor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($p = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><i class="fas fa-box"></i> <?= htmlspecialchars($p['nome']) ?></td>
                    <td>R$ <?= number_format($p['preco'],2,',','.') ?></td>
                    <td><span class="badge badge-estoque"><?= $p['estoque'] ?></span></td>
                    <td><i class="fas fa-store"></i> <?= htmlspecialchars($p['vendedor']) ?></td>
                    <td>
                        <a href="admin_produtos.php?visualizar=<?= $p['id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="admin_produtos.php?editar=<?= $p['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <a href="admin_produtos.php?excluir=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirma exclusão?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="perfil.php" class="btn btn-secondary mb-3">
    <i class="fas fa-arrow-left"></i> Voltar
</a>
    </div>
</body>
</html>