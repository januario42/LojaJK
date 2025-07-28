<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id_usuario'];
$sql = "SELECT nome, email, senha, imagem_perfil FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($nome, $email, $senha_atual_hash, $imagem_perfil);
$stmt->fetch();
$stmt->close();

$erro = "";
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novo_nome = trim($_POST['nome']);
    $novo_email = trim($_POST['email']);
    $senha_atual = trim($_POST['senha_atual']);
    $nova_senha = !empty($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;

    // Verificação da senha atual
    if (!password_verify($senha_atual, $senha_atual_hash)) {
        $erro = "Senha atual incorreta.";
    } else {
        // Upload da imagem (método antigo, apenas na pasta "uploads/")
        if (!empty($_FILES['imagem_perfil']['name'])) {
            $extensao = strtolower(pathinfo($_FILES['imagem_perfil']['name'], PATHINFO_EXTENSION));
            $tipos_permitidos = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($extensao, $tipos_permitidos)) {
                $novo_nome_arquivo = uniqid() . "." . $extensao;
                $caminho_completo = "uploads/" . $novo_nome_arquivo;

                if (move_uploaded_file($_FILES['imagem_perfil']['tmp_name'], $caminho_completo)) {
                    $imagem_perfil = $novo_nome_arquivo; // Salva só o nome do arquivo
                } else {
                    $erro = "Erro ao fazer upload da imagem.";
                }
            } else {
                $erro = "Formato de imagem inválido.";
            }
        }

        if (!$erro) {
            // Atualização dos dados
            $sql = "UPDATE usuarios SET nome = ?, email = ?, imagem_perfil = ?";
            if ($nova_senha) {
                $sql .= ", senha = ?";
            }
            $sql .= " WHERE id = ?";

            $stmt = $conexao->prepare($sql);
            if ($nova_senha) {
                $stmt->bind_param("ssssi", $novo_nome, $novo_email, $imagem_perfil, $nova_senha, $usuario_id);
            } else {
                $stmt->bind_param("sssi", $novo_nome, $novo_email, $imagem_perfil, $usuario_id);
            }

            if ($stmt->execute()) {
                session_destroy();
                echo "<script>alert('Perfil atualizado com sucesso! Faça login novamente.'); window.location.href='login.php';</script>";
                exit();
            } else {
                $erro = "Erro ao atualizar o perfil.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="./css/editar_perfil.css">

</head>
<body>
    
<header class="editar-header">
<a href="perfil.php"><i class="fas fa-arrow-left"></i> Voltar</a> 
    <h2>Editar Perfil</h2>
</header>

<div class="editar-container">
    <?php if ($erro): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php endif; ?>
    <form action="editar_perfil.php" method="post" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control shadow-sm" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control shadow-sm" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>

        <div class="mb-3">
            <label for="senha_atual" class="form-label">Senha Atual</label>
            <input type="password" class="form-control shadow-sm" name="senha_atual" required>
        </div>

        <div class="mb-3">
            <label for="senha" class="form-label">Nova Senha (opcional)</label>
            <input type="password" class="form-control shadow-sm" name="senha">
        </div>

        <div class="mb-3">
            <label for="imagem_perfil" class="form-label">Imagem de Perfil</label>
            <input type="file" class="form-control" name="imagem_perfil">
            <?php if (!empty($imagem_perfil)): ?>
                <img src="uploads/<?php echo htmlspecialchars($imagem_perfil); ?>" alt="Imagem de Perfil" class="preview-img">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-salvar">Salvar Alterações</button>
        <a href="perfil.php" class="btn-cancelar">Cancelar</a>
    </form>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
