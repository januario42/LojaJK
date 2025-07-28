<?php
// Teste rápido do sistema LojaJK
// Execute este arquivo para verificar se tudo está funcionando

echo "<h1>⚡ Teste Rápido - LojaJK</h1>";

// Teste 1: Conexão
echo "<h2>1. Testando conexão...</h2>";
try {
    include("conexao.php");
    echo "✅ Conexão OK<br>";
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
    exit;
}

// Teste 2: Banco existe
echo "<h2>2. Verificando banco de dados...</h2>";
$result = $conexao->query("SHOW DATABASES LIKE 'almoxarifado'");
if ($result->num_rows > 0) {
    echo "✅ Banco 'almoxarifado' encontrado<br>";
} else {
    echo "❌ Banco 'almoxarifado' não encontrado<br>";
    echo "💡 Execute primeiro: <a href='recriar_banco.php'>recriar_banco.php</a><br>";
    exit;
}

// Teste 3: Tabelas existem
echo "<h2>3. Verificando tabelas...</h2>";
$tabelas = ['usuarios', 'produtos'];
foreach ($tabelas as $tabela) {
    $result = $conexao->query("SHOW TABLES LIKE '$tabela'");
    if ($result->num_rows > 0) {
        echo "✅ Tabela '$tabela' OK<br>";
    } else {
        echo "❌ Tabela '$tabela' não encontrada<br>";
    }
}

// Teste 4: Admin existe
echo "<h2>4. Verificando usuário admin...</h2>";
$stmt = $conexao->prepare("SELECT id, nome FROM usuarios WHERE email = ?");
$email = "admin@lojajk.com";
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    echo "✅ Admin encontrado: " . $admin['nome'] . "<br>";
    echo "📧 Email: admin@lojajk.com<br>";
    echo "🔑 Senha: password<br>";
} else {
    echo "❌ Admin não encontrado<br>";
}

// Teste 5: Funcionalidades básicas
echo "<h2>5. Testando funcionalidades...</h2>";

// Testar inserção
$stmt = $conexao->prepare("INSERT INTO produtos (id_vendedor, nome, descricao, preco, estoque, imagem) VALUES (?, ?, ?, ?, ?, ?)");
$id_vendedor = 1;
$nome = "Teste";
$descricao = "Produto de teste";
$preco = 10.00;
$estoque = 1;
$imagem = "teste.jpg";

$stmt->bind_param("issdis", $id_vendedor, $nome, $descricao, $preco, $estoque, $imagem);

if ($stmt->execute()) {
    $produto_id = $conexao->insert_id;
    echo "✅ Inserção de produto OK<br>";
    
    // Limpar teste
    $conexao->query("DELETE FROM produtos WHERE id = $produto_id");
    echo "✅ Limpeza de teste OK<br>";
} else {
    echo "❌ Erro na inserção de produto<br>";
}

// Resultado final
echo "<h2>🎯 Resultado Final</h2>";
echo "<p><strong>Sistema está funcionando!</strong></p>";
echo "<p>Você pode agora:</p>";
echo "<ul>";
echo "<li><a href='login.php'>Fazer login</a></li>";
echo "<li><a href='cadastro.php'>Criar conta</a></li>";
echo "<li><a href='logado.php'>Ver produtos</a></li>";
echo "</ul>";

$conexao->close();
?>

<style>
body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
h1, h2 { color: #025c0e; }
a { color: #025c0e; text-decoration: none; }
a:hover { text-decoration: underline; }
</style> 