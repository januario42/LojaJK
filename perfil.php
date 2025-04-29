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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/perfil.css">
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
                <a href="editar_perfil.php" class="btn btn-edit-profile">Editar Perfil</a>
                <a href="adicionar_produto.php" class="btn btn-custom">Adicionar Produto</a>
                <a href="meus_produtos.php" class="btn btn-custom">Gerenciar Produtos</a>
                <a href="dashboard_vendedor.php" class="btn btn-custom">Dashboard</a>
            <?php else: ?>
                <a href="editar_perfil.php" class="btn btn-edit-profile">Editar Perfil</a>
                <a href="loja.php" class="btn btn-custom">Ver Produtos</a>
                <a href="carrinho.php" class="btn btn-custom">Meu Carrinho</a>
                <a href="favoritos.php" class="btn btn-custom">Favoritos</a>
            <?php endif; ?>
        </div>

        <div class="profile-content">
            <h2>Histórico de Pedidos</h2>
            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#1234</td>
                            <td>10/03/2025</td>
                            <td>Enviado</td>
                            <td>R$ 150,00</td>
                            <td><a href="detalhes_pedido.php?id=1234" class="nav-link">Ver Detalhes</a></td>
                        </tr>
                        <tr>
                            <td>#1235</td>
                            <td>12/03/2025</td>
                            <td>Entregue</td>
                            <td>R$ 200,00</td>
                            <td><a href="detalhes_pedido.php?id=1235" class="nav-link">Ver Detalhes</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
