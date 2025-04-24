<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}

$id_vendedor = $_SESSION['id_usuario'];

// Atualização do produto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editar_id"])) {
    $editar_id = $_POST["editar_id"];
    $novo_nome = $_POST["editar_nome"];
    $novo_preco = $_POST["editar_preco"];
    $novo_estoque = $_POST["editar_estoque"];
    $nova_descricao = $_POST["editar_descricao"];

    // Verifica se uma nova imagem foi enviada
    if (isset($_FILES['editar_imagem']) && $_FILES['editar_imagem']['error'] === 0) {
        $imagem_nome = uniqid() . '_' . basename($_FILES['editar_imagem']['name']);
        $imagem_caminho = 'produtos/' . $imagem_nome;
        move_uploaded_file($_FILES['editar_imagem']['tmp_name'], $imagem_caminho);

        $stmt = $conexao->prepare("UPDATE produtos SET nome = ?, preco = ?, estoque = ?, descricao = ?, imagem = ? WHERE id = ? AND id_vendedor = ?");
        $stmt->bind_param("sdiissi", $novo_nome, $novo_preco, $novo_estoque, $nova_descricao, $imagem_nome, $editar_id, $id_vendedor);
    } else {
        $stmt = $conexao->prepare("UPDATE produtos SET nome = ?, preco = ?, estoque = ?, descricao = ? WHERE id = ? AND id_vendedor = ?");
        $stmt->bind_param("sdiisi", $novo_nome, $novo_preco, $novo_estoque, $nova_descricao, $editar_id, $id_vendedor);
    }
    $stmt->execute();
}

// Exclusão com verificação de senha
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["excluir_id"])) {
    $senha = $_POST["senha_confirmar"];
    $excluir_id = $_POST["excluir_id"];

    $stmt = $conexao->prepare("SELECT senha FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_vendedor);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if (password_verify($senha, $usuario['senha'])) {
        $stmt = $conexao->prepare("DELETE FROM produtos WHERE id = ? AND id_vendedor = ?");
        $stmt->bind_param("ii", $excluir_id, $id_vendedor);
        $stmt->execute();
    } else {
        $erro_excluir = "Senha incorreta.";
    }
}

$stmt = $conexao->prepare("SELECT * FROM produtos WHERE id_vendedor = ? ORDER BY data_cadastro DESC");
$stmt->bind_param("i", $id_vendedor);
$stmt->execute();
$produtos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Gerir Produtos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
    body {
        background-color: #f4f6f9;
        font-family: 'Segoe UI', sans-serif;
    }

    h2 {
        color: #025c0e;
        font-weight: bold;
    }

    .container {
        margin-top: 40px;
    }

    .produto-card {
        background: #fff;
        border: 2px solid #025c0e;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        position: relative;
        z-index: 1;
    }

    .produto-card img {
        max-width: 150px;
        border-radius: 10px;
    }

    .produto-info {
        flex: 1;
        padding-left: 20px;
    }

    .btn-editar {
        background-color: #ffc107;
        border: none;
        transition: 0.2s;
    }

    .btn-excluir {
        background-color: #dc3545;
        border: none;
        transition: 0.2s;
    }

    .btn-voltar {
        background-color: #025c0e;
        border: none;
    }

    .btn-voltar:hover {
        background-color: #01480b;
    }

    .modal-backdrop-custom {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9998;
        display: none;
    }

    .modal-content-custom {
        background-color: #fff;
        border-radius: 10px;
        padding: 25px;
        max-width: 500px;
        margin: 100px auto;
        z-index: 9999;
        display: none;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .modal-backdrop-custom.show,
    .modal-content-custom.show {
        display: block !important;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        top: 8px;
        cursor: pointer;
        font-size: 18px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Gerir Produtos</h2>
        <a href="perfil.php" class="btn btn-voltar mb-3 text-white">⬅ Voltar ao Perfil</a>

        <?php if ($produtos->num_rows > 0): ?>
        <?php while ($produto = $produtos->fetch_assoc()): ?>
        <div class="produto-card">
            <img src="produtos/<?php echo htmlspecialchars($produto['imagem']); ?>" alt="Imagem Produto">
            <div class="produto-info">
                <h4><?php echo htmlspecialchars($produto['nome']); ?></h4>
                <p><strong>Preço:</strong> R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                <p><strong>Estoque:</strong> <?php echo $produto['estoque']; ?></p>
                <p><strong>Descrição:</strong> <?php echo htmlspecialchars($produto['descricao']); ?></p>
                <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($produto['data_cadastro'])); ?></p>
                <button class="btn btn-editar text-white me-2"
                    onclick="abrirModalEditar(<?php echo $produto['id']; ?>, '<?php echo htmlspecialchars($produto['nome']); ?>', '<?php echo $produto['preco']; ?>', '<?php echo $produto['estoque']; ?>', '<?php echo htmlspecialchars(addslashes($produto['descricao'])); ?>')">Editar</button>
                <button class="btn btn-excluir text-white"
                    onclick="abrirModalExcluir(<?php echo $produto['id']; ?>)">Excluir</button>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <div class="alert alert-warning">Você ainda não cadastrou nenhum produto.</div>
        <?php endif; ?>
    </div>

    <!-- Modal Edição -->
    <div id="modalEditar" class="modal-backdrop-custom">
        <form method="POST" class="modal-content-custom bg-white shadow" enctype="multipart/form-data">
            <h4 class="mb-3">Editar Produto</h4>
            <input type="hidden" name="editar_id" id="editar_id">
            <div class="mb-3">
                <label>Nome</label>
                <input type="text" class="form-control" name="editar_nome" id="editar_nome" required>
            </div>
            <div class="mb-3">
                <label>Preço</label>
                <input type="number" class="form-control" name="editar_preco" id="editar_preco" step="0.01" min="0" required>
            </div>
            <div class="mb-3">
                <label>Estoque</label>
                <input type="number" class="form-control" name="editar_estoque" id="editar_estoque" min="0" required>
            </div>
            <div class="mb-3">
                <label>Descrição</label>
                <textarea class="form-control" name="editar_descricao" id="editar_descricao" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label>Nova Imagem (opcional)</label>
                <input type="file" class="form-control" name="editar_imagem" accept="image/*">
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <button type="button" class="btn btn-secondary" onclick="fecharModal('modalEditar')">Cancelar</button>
            </div>
        </form>
    </div>

    <!-- Modal Exclusão -->
    <div id="modalExcluir" class="modal-backdrop-custom">
        <form method="POST" class="modal-content-custom position-relative">
            <h4 class="mb-3">Confirmar Exclusão</h4>
            <p>Digite sua senha para confirmar a exclusão:</p>
            <input type="hidden" name="excluir_id" id="excluir_id">
            <div class="mb-3 position-relative">
                <input type="password" class="form-control" name="senha_confirmar" id="senha_confirmar" required>
                <span class="password-toggle" onclick="toggleSenha(this)">👁️</span>
            </div>
            <?php if (!empty($erro_excluir)): ?>
            <div class="alert alert-danger"><?php echo $erro_excluir; ?></div>
            <?php endif; ?>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-danger">Excluir</button>
                <button type="button" class="btn btn-secondary" onclick="fecharModal('modalExcluir')">Cancelar</button>
            </div>
        </form>
    </div>

    <script>
    function abrirModalEditar(id, nome, preco, estoque, descricao) {
        document.getElementById('editar_id').value = id;
        document.getElementById('editar_nome').value = nome;
        document.getElementById('editar_preco').value = preco;
        document.getElementById('editar_estoque').value = estoque;
        document.getElementById('editar_descricao').value = descricao;
        document.getElementById('modalEditar').classList.add('show');
        document.querySelector('#modalEditar .modal-content-custom').classList.add('show');
    }

    function abrirModalExcluir(id) {
        document.getElementById('excluir_id').value = id;
        document.getElementById('senha_confirmar').value = '';
        document.getElementById('modalExcluir').classList.add('show');
        document.querySelector('#modalExcluir .modal-content-custom').classList.add('show');
    }

    function fecharModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('show');
        modal.querySelector('.modal-content-custom').classList.remove('show');
    }

    function toggleSenha(icone) {
        const campo = document.getElementById('senha_confirmar');
        if (campo.type === 'password') {
            campo.type = 'text';
            icone.textContent = '🙈';
        } else {
            campo.type = 'password';
            icone.textContent = '👁️';
        }
    }

    window.addEventListener('keydown', function(e) {
        if (e.key === "Escape") {
            fecharModal('modalEditar');
            fecharModal('modalExcluir');
        }
    });
    </script>

</body>

</html>
