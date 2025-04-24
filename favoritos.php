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
    <link rel="stylesheet" href="seu-estilo.css"> <!-- troque se necessário -->
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f3f3f3;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #025c0e;
            margin-bottom: 30px;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
        }
        .produtos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .produto {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 15px;
            text-align: center;
            transition: 0.3s;
        }
        .produto:hover {
            transform: translateY(-5px);
        }
        .produto img {
            max-width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }
        .produto h3 {
            margin: 10px 0 5px;
            font-size: 1.1rem;
        }
        .produto .preco {
            color: #028a0f;
            font-size: 1rem;
            font-weight: bold;
        }
        .btn-voltar {
            display: inline-block;
            margin: 20px auto 40px;
            background-color: #025c0e;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-voltar:hover {
            background-color: #018507;
        }
        .vazio {
            text-align: center;
            color: #555;
            font-size: 1.1rem;
        }
    </style>
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
