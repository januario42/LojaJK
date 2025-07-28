<?php
include("conexao.php");

echo "<h2>Criando Sistema de Pedidos</h2>";

// SQL para criar as tabelas
$sql_pedidos = "
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    external_reference VARCHAR(255) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pendente', 'aprovado', 'preparando', 'enviado', 'entregue', 'cancelado') DEFAULT 'pendente',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_pagamento TIMESTAMP NULL,
    data_envio TIMESTAMP NULL,
    data_entrega TIMESTAMP NULL,
    endereco TEXT,
    cidade VARCHAR(100),
    cep VARCHAR(10),
    telefone VARCHAR(20),
    observacoes TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
)";

$sql_itens_pedido = "
CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    vendedor_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (vendedor_id) REFERENCES usuarios(id)
)";

$sql_vendas = "
CREATE TABLE IF NOT EXISTS vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    vendedor_id INT NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    comissao DECIMAL(10,2) NOT NULL,
    data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pendente', 'aprovada', 'paga', 'cancelada') DEFAULT 'pendente',
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (vendedor_id) REFERENCES usuarios(id)
)";

try {
    // Criar tabela de pedidos
    if ($conexao->query($sql_pedidos)) {
        echo "✅ Tabela 'pedidos' criada com sucesso!<br>";
    } else {
        echo "❌ Erro ao criar tabela 'pedidos': " . $conexao->error . "<br>";
    }
    
    // Criar tabela de itens do pedido
    if ($conexao->query($sql_itens_pedido)) {
        echo "✅ Tabela 'itens_pedido' criada com sucesso!<br>";
    } else {
        echo "❌ Erro ao criar tabela 'itens_pedido': " . $conexao->error . "<br>";
    }
    
    // Criar tabela de vendas
    if ($conexao->query($sql_vendas)) {
        echo "✅ Tabela 'vendas' criada com sucesso!<br>";
    } else {
        echo "❌ Erro ao criar tabela 'vendas': " . $conexao->error . "<br>";
    }
    
    echo "<br>🎉 Sistema de pedidos criado com sucesso!<br>";
    echo "<a href='perfil.php' class='btn btn-primary'>Voltar ao Perfil</a>";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?> 