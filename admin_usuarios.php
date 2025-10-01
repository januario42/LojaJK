<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Função: visualizar
if (isset($_GET['visualizar'])) {
    $id = intval($_GET['visualizar']);
    $stmt = $conexao->prepare("SELECT nome, email, tipo_usuario, cpf_cnpj, nome_loja, data_cadastro FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nome, $email, $tipo, $cpf_cnpj, $nome_loja, $cadastro);
    $stmt->fetch();
    $stmt->close();
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Visualizar Usuário</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-4">
            <h2>Dados do Usuário</h2>
            <ul class="list-group">
                <li class="list-group-item"><strong>Nome:</strong> <?= htmlspecialchars($nome) ?></li>
                <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($email) ?></li>
                <li class="list-group-item"><strong>Tipo:</strong> <?= ucfirst($tipo) ?></li>
                <li class="list-group-item"><strong>CPF/CNPJ:</strong> <?= htmlspecialchars($cpf_cnpj) ?></li>
                <?php if ($tipo === 'vendedor'): ?>
                <li class="list-group-item"><strong>Nome da Loja:</strong> <?= htmlspecialchars($nome_loja) ?></li>
                <?php endif; ?>
                <li class="list-group-item"><strong>Cadastro:</strong> <?= date('d/m/Y', strtotime($cadastro)) ?></li>
            </ul>
            <a href="admin_usuarios.php" class="btn btn-secondary mt-3">Voltar</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Função: editar
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $tipo = $_POST['tipo_usuario'];
        $stmt = $conexao->prepare("UPDATE usuarios SET nome=?, email=?, tipo_usuario=? WHERE id=?");
        $stmt->bind_param("sssi", $nome, $email, $tipo, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin_usuarios.php");
        exit();
    }
    $stmt = $conexao->prepare("SELECT nome, email, tipo_usuario FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nome, $email, $tipo);
    $stmt->fetch();
    $stmt->close();
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Editar Usuário</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-4">
            <h2>Editar Usuário</h2>
            <form method="post">
                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($nome) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Tipo</label>
                    <select name="tipo_usuario" class="form-select">
                        <option value="comprador" <?= $tipo === 'comprador' ? 'selected' : '' ?>>Comprador</option>
                        <option value="vendedor" <?= $tipo === 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
                        <option value="admin" <?= $tipo === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Salvar</button>
                <a href="admin_usuarios.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Função: excluir
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $stmt = $conexao->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_usuarios.php");
    exit();
}

// Listagem + filtro
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$sql = "SELECT id, nome, email, tipo_usuario, data_cadastro FROM usuarios";
if ($busca !== '') {
    $sql .= " WHERE nome LIKE ? OR email LIKE ?";
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
    <title>Admin - Usuários</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .table th, .table td { vertical-align: middle; }
        .badge-admin { background: #3949ab; }
        .badge-vendedor { background: #028a1a; }
        .badge-comprador { background: #17a2b8; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2><i class="fas fa-users-cog"></i> Gerenciar Usuários</h2>
        <form class="row g-3 mb-3" method="get">
            <div class="col-auto">
                <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou email" value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                <a href="admin_usuarios.php" class="btn btn-secondary">Limpar</a>
            </div>
        </form>
        <table class="table table-hover table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($u = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><i class="fas fa-user"></i> <?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php
                        $tipo = $u['tipo_usuario'];
                        $badge = $tipo === 'admin' ? 'badge-admin' : ($tipo === 'vendedor' ? 'badge-vendedor' : 'badge-comprador');
                        ?>
                        <span class="badge <?= $badge ?>"><?= ucfirst($tipo) ?></span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($u['data_cadastro'])) ?></td>
                    <td>
                        <a href="admin_usuarios.php?visualizar=<?= $u['id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="admin_usuarios.php?editar=<?= $u['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <a href="admin_usuarios.php?excluir=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirma exclusão?')"><i class="fas fa-trash"></i></a>
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