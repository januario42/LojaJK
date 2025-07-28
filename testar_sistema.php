<?php
// Script de teste automatizado para o sistema LojaJK
// Este script testa todas as funcionalidades principais

session_start();
include("conexao.php");

echo "<h1>🧪 Teste Automatizado do Sistema LojaJK</h1>";

$testes = [];
$erros = [];

// Função para adicionar resultado de teste
function adicionarTeste($nome, $sucesso, $mensagem = "") {
    global $testes;
    $testes[] = [
        'nome' => $nome,
        'sucesso' => $sucesso,
        'mensagem' => $mensagem
    ];
}

// Função para adicionar erro
function adicionarErro($erro) {
    global $erros;
    $erros[] = $erro;
}

// Teste 1: Conexão com o banco de dados
echo "<h2>🔌 Teste de Conexão</h2>";
try {
    if ($conexao->ping()) {
        adicionarTeste("Conexão com MySQL", true, "Conexão estabelecida com sucesso");
    } else {
        adicionarTeste("Conexão com MySQL", false, "Falha na conexão");
        adicionarErro("Não foi possível conectar ao MySQL");
    }
} catch (Exception $e) {
    adicionarTeste("Conexão com MySQL", false, $e->getMessage());
    adicionarErro("Erro de conexão: " . $e->getMessage());
}

// Teste 2: Verificar se o banco existe
echo "<h2>🗄️ Teste do Banco de Dados</h2>";
try {
    $result = $conexao->query("SHOW DATABASES LIKE 'almoxarifado'");
    if ($result->num_rows > 0) {
        adicionarTeste("Banco de dados 'almoxarifado'", true, "Banco encontrado");
    } else {
        adicionarTeste("Banco de dados 'almoxarifado'", false, "Banco não encontrado");
        adicionarErro("O banco 'almoxarifado' não existe. Execute primeiro o script de recuperação.");
    }
} catch (Exception $e) {
    adicionarTeste("Banco de dados 'almoxarifado'", false, $e->getMessage());
    adicionarErro("Erro ao verificar banco: " . $e->getMessage());
}

// Teste 3: Verificar tabelas
echo "<h2>📋 Teste das Tabelas</h2>";
$tabelas_esperadas = ['usuarios', 'produtos'];

foreach ($tabelas_esperadas as $tabela) {
    try {
        $result = $conexao->query("SHOW TABLES LIKE '$tabela'");
        if ($result->num_rows > 0) {
            adicionarTeste("Tabela '$tabela'", true, "Tabela encontrada");
            
            // Verificar estrutura da tabela
            $result = $conexao->query("DESCRIBE $tabela");
            $colunas = $result->num_rows;
            adicionarTeste("Estrutura da tabela '$tabela'", true, "$colunas colunas encontradas");
        } else {
            adicionarTeste("Tabela '$tabela'", false, "Tabela não encontrada");
            adicionarErro("A tabela '$tabela' não existe");
        }
    } catch (Exception $e) {
        adicionarTeste("Tabela '$tabela'", false, $e->getMessage());
        adicionarErro("Erro ao verificar tabela '$tabela': " . $e->getMessage());
    }
}

// Teste 4: Verificar usuário administrador
echo "<h2>👤 Teste do Usuário Administrador</h2>";
try {
    $stmt = $conexao->prepare("SELECT id, nome, email, tipo_usuario FROM usuarios WHERE email = ?");
    $email_admin = "admin@lojajk.com";
    $stmt->bind_param("s", $email_admin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        adicionarTeste("Usuário administrador", true, "Admin encontrado: " . $admin['nome']);
        adicionarTeste("Tipo de usuário admin", $admin['tipo_usuario'] === 'vendedor', "Tipo: " . $admin['tipo_usuario']);
    } else {
        adicionarTeste("Usuário administrador", false, "Admin não encontrado");
        adicionarErro("Usuário administrador não foi criado");
    }
} catch (Exception $e) {
    adicionarTeste("Usuário administrador", false, $e->getMessage());
    adicionarErro("Erro ao verificar admin: " . $e->getMessage());
}

// Teste 5: Testar login do administrador
echo "<h2>🔐 Teste de Login</h2>";
try {
    $stmt = $conexao->prepare("SELECT id, nome, senha, tipo_usuario FROM usuarios WHERE email = ?");
    $email_admin = "admin@lojajk.com";
    $stmt->bind_param("s", $email_admin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $senha_teste = "password";
        
        if (password_verify($senha_teste, $admin['senha'])) {
            adicionarTeste("Login do administrador", true, "Senha correta");
        } else {
            adicionarTeste("Login do administrador", false, "Senha incorreta");
            adicionarErro("A senha do administrador não está correta");
        }
    } else {
        adicionarTeste("Login do administrador", false, "Usuário não encontrado");
    }
} catch (Exception $e) {
    adicionarTeste("Login do administrador", false, $e->getMessage());
    adicionarErro("Erro no teste de login: " . $e->getMessage());
}

// Teste 6: Verificar funcionalidades básicas
echo "<h2>⚙️ Teste de Funcionalidades</h2>";

// Testar inserção de produto
try {
    $stmt = $conexao->prepare("INSERT INTO produtos (id_vendedor, nome, descricao, preco, estoque, imagem) VALUES (?, ?, ?, ?, ?, ?)");
    $id_vendedor = 1; // ID do admin
    $nome = "Produto Teste";
    $descricao = "Produto criado para teste";
    $preco = 99.99;
    $estoque = 10;
    $imagem = "teste.jpg";
    
    $stmt->bind_param("issdis", $id_vendedor, $nome, $descricao, $preco, $estoque, $imagem);
    
    if ($stmt->execute()) {
        $produto_id = $conexao->insert_id;
        adicionarTeste("Inserção de produto", true, "Produto criado com ID: $produto_id");
        
        // Limpar produto de teste
        $conexao->query("DELETE FROM produtos WHERE id = $produto_id");
        adicionarTeste("Limpeza de dados de teste", true, "Produto de teste removido");
    } else {
        adicionarTeste("Inserção de produto", false, "Erro ao inserir produto");
        adicionarErro("Não foi possível inserir produto de teste");
    }
} catch (Exception $e) {
    adicionarTeste("Inserção de produto", false, $e->getMessage());
    adicionarErro("Erro na inserção de produto: " . $e->getMessage());
}

// Teste 7: Verificar arquivos importantes
echo "<h2>📁 Teste de Arquivos</h2>";
$arquivos_importantes = [
    'conexao.php',
    'login.php',
    'cadastro.php',
    'logado.php',
    'adicionar_produto.php',
    'perfil.php'
];

foreach ($arquivos_importantes as $arquivo) {
    if (file_exists($arquivo)) {
        adicionarTeste("Arquivo '$arquivo'", true, "Arquivo encontrado");
    } else {
        adicionarTeste("Arquivo '$arquivo'", false, "Arquivo não encontrado");
        adicionarErro("Arquivo importante '$arquivo' não existe");
    }
}

// Teste 8: Verificar diretórios
echo "<h2>📂 Teste de Diretórios</h2>";
$diretorios_importantes = [
    'css',
    'produtos',
    'uploads',
    'images'
];

foreach ($diretorios_importantes as $diretorio) {
    if (is_dir($diretorio)) {
        adicionarTeste("Diretório '$diretorio'", true, "Diretório encontrado");
    } else {
        adicionarTeste("Diretório '$diretorio'", false, "Diretório não encontrado");
        adicionarErro("Diretório importante '$diretorio' não existe");
    }
}

// Exibir resultados
echo "<h2>📊 Resultados dos Testes</h2>";

$sucessos = 0;
$falhas = 0;

foreach ($testes as $teste) {
    $status = $teste['sucesso'] ? "✅" : "❌";
    $cor = $teste['sucesso'] ? "green" : "red";
    echo "<p style='color: $cor;'>$status <strong>{$teste['nome']}</strong>: {$teste['mensagem']}</p>";
    
    if ($teste['sucesso']) {
        $sucessos++;
    } else {
        $falhas++;
    }
}

echo "<h3>📈 Resumo</h3>";
echo "<p><strong>Total de testes:</strong> " . count($testes) . "</p>";
echo "<p style='color: green;'><strong>Sucessos:</strong> $sucessos</p>";
echo "<p style='color: red;'><strong>Falhas:</strong> $falhas</p>";

if ($falhas == 0) {
    echo "<h2 style='color: green;'>🎉 Todos os testes passaram! Sistema funcionando perfeitamente!</h2>";
} else {
    echo "<h2 style='color: orange;'>⚠️ Alguns testes falharam. Verifique os erros abaixo:</h2>";
    
    if (!empty($erros)) {
        echo "<h3>🚨 Erros encontrados:</h3>";
        echo "<ul>";
        foreach ($erros as $erro) {
            echo "<li style='color: red;'>$erro</li>";
        }
        echo "</ul>";
    }
}

// Links úteis
echo "<h2>🔗 Links Úteis</h2>";
echo "<ul>";
echo "<li><a href='login.php' target='_blank'>Página de Login</a></li>";
echo "<li><a href='cadastro.php' target='_blank'>Página de Cadastro</a></li>";
echo "<li><a href='logado.php' target='_blank'>Página Principal</a></li>";
echo "<li><a href='recriar_banco.php' target='_blank'>Recriar Banco de Dados</a></li>";
echo "</ul>";

$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste do Sistema - LojaJK</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1, h2, h3 {
            color: #025c0e;
        }
        ul {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        a {
            color: #025c0e;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        .warning {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <!-- O conteúdo PHP será exibido aqui -->
</body>
</html> 