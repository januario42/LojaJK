<?php
session_start();
require_once 'conexao.php';

function escapar($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$ids = $_SESSION['favoritos'] ?? [];
$lista = [];

if (!empty($ids)) {
    $ids_string = implode(",", array_map('intval', $ids));
    $sql = "SELECT * FROM produtos WHERE id IN ($ids_string)";
    $result = $conexao->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $lista[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Favoritos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./css/favoritos.css"> <!-- troque se necessário -->
</head>
<body>

<div class="container">
    <h2><i class="fas fa-heart"></i> Meus Favoritos</h2>

    <?php if (empty($lista)): ?>
        <p class="vazio">Você ainda não adicionou nenhum produto aos favoritos.</p>
    <?php else: ?>
        <div class="produtos">
            <?php foreach ($lista as $p): ?>
                <?php
                    $imagem = "produtos/" . escapar($p['imagem']);
                    if (!file_exists($imagem) || empty($p['imagem'])) {
                        $imagem = "images/default.png";
                    }
                ?>
                <div class="produto">
                    <img src="<?= $imagem ?>" alt="<?= escapar($p['nome']) ?>">
                    <h3><?= escapar($p['nome']) ?></h3>
                    <div class="preco">R$<?= number_format($p['preco'], 2, ',', '.') ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div style="text-align: center;">
        <a href="logado.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar à loja</a>
    </div>
</div>

</body>
</html>
