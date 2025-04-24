<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id_usuario']) || ($_SESSION['tipo_usuario'] ?? '') !== 'vendedor') {
    header("Location: login.php");
    exit();
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_produto = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
    $preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
    $estoque = filter_input(INPUT_POST, 'estoque', FILTER_VALIDATE_INT);
    $id_vendedor = $_SESSION['id_usuario'];

    if (!$nome_produto || !$descricao || $preco === false || $estoque === false || $preco < 0 || $estoque < 0) {
        $mensagem = "Preencha todos os campos corretamente.";
    } else {
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $imagem_temp = $_FILES['imagem']['tmp_name'];
            $imagem_nome = basename($_FILES['imagem']['name']);
            $imagem_tipo = mime_content_type($imagem_temp);
            $imagem_extensao = strtolower(pathinfo($imagem_nome, PATHINFO_EXTENSION));
            $imagem_tamanho = $_FILES['imagem']['size'];

            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
            $tipos_permitidos = ['image/jpeg', 'image/png', 'image/webp'];

            if (in_array($imagem_extensao, $extensoes_permitidas) && in_array($imagem_tipo, $tipos_permitidos)) {
                if ($imagem_tamanho <= 2 * 1024 * 1024) {
                    $novo_nome = uniqid('produto_', true) . "." . $imagem_extensao;
                    $caminho = "produtos/" . $novo_nome;

                    if (move_uploaded_file($imagem_temp, $caminho)) {
                        $sql = "INSERT INTO produtos (id_vendedor, nome, descricao, preco, estoque, imagem) VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("issdis", $id_vendedor, $nome_produto, $descricao, $preco, $estoque, $novo_nome);

                        if ($stmt->execute()) {
                            $mensagem = "✅ Produto adicionado com sucesso!";
                        } else {
                            $mensagem = "❌ Erro ao inserir no banco de dados.";
                        }
                        $stmt->close();
                    } else {
                        $mensagem = "❌ Erro ao mover a imagem.";
                    }
                } else {
                    $mensagem = "❌ A imagem excede 2MB.";
                }
            } else {
                $mensagem = "❌ Formato de imagem inválido. Use JPG, PNG ou WEBP.";
            }
        } else {
            $mensagem = "❌ Erro no envio da imagem.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-box {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.05);
            padding: 40px;
            max-width: 700px;
            margin: 50px auto;
        }

        h2 {
            color: #025c0e;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #025c0e;
        }

        .form-control:focus {
            border-color: #025c0e;
            box-shadow: 0 0 0 0.2rem rgba(2, 92, 14, 0.25);
        }

        .btn-success {
            background-color: #025c0e;
            border: none;
        }

        .btn-success:hover {
            background-color: #018a11;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .alert-info {
            background-color: #e6f7ed;
            color: #025c0e;
            border-left: 5px solid #025c0e;
        }

        #preview {
            max-width: 200px;
            margin-top: 10px;
            border-radius: 10px;
            border: 2px solid #025c0e;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container-box">
        <h2><i class="fas fa-box"></i> Adicionar Novo Produto</h2>

        <?php if ($mensagem): ?>
            <div class="alert alert-info"> <?php echo htmlspecialchars($mensagem); ?> </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Produto</label>
                <input type="text" name="nome" id="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea name="descricao" id="descricao" rows="3" class="form-control" required></textarea>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="preco" class="form-label">Preço (R$)</label>
                    <input type="number" name="preco" id="preco" step="0.01" min="0" class="form-control" required>
                </div>
                <div class="mb-3 col-md-6">
                    <label for="estoque" class="form-label">Estoque</label>
                    <input type="number" name="estoque" id="estoque" min="0" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="imagem" class="form-label">Imagem do Produto</label>
                <input type="file" name="imagem" id="imagem" accept=".jpg,.jpeg,.png,.webp" class="form-control" onchange="previewImagem(event)" required>
                <img id="preview" class="img-thumbnail mt-3" />
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Adicionar Produto
                </button>
                <a href="perfil.php" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </div>

    <script>
        function previewImagem(event) {
            const preview = document.getElementById('preview');
            const file = event.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
                preview.src = "";
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
