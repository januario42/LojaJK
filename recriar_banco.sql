-- Criar o banco
CREATE DATABASE IF NOT EXISTS lojajk
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_general_ci;

USE lojajk;

-- Tabela usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('comprador','vendedor') NOT NULL,
    cpf_cnpj VARCHAR(18),
    nome_loja VARCHAR(255),
    imagem_perfil VARCHAR(255),
    token VARCHAR(255),
    expiracao_token DATETIME,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela produtos
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_vendedor INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL,
    imagem VARCHAR(255),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_vendedor) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    external_reference VARCHAR(255),
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(30) DEFAULT 'pendente',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_pagamento TIMESTAMP NULL,
    endereco TEXT,
    cidade VARCHAR(100),
    cep VARCHAR(10),
    telefone VARCHAR(20),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela itens_pedido
CREATE TABLE itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

-- Tabela vendas
CREATE TABLE vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    vendedor_id INT NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    comissao DECIMAL(10,2) NOT NULL,
    data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pendente','aprovada','paga','cancelada') DEFAULT 'pendente',
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (vendedor_id) REFERENCES usuarios(id) ON DELETE CASCADE
);


INSERT INTO usuarios (nome, email, senha, tipo_usuario, nome_loja)
VALUES ('Administrador', 'admin@lojajk.com',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Loja JK');

ALTER TABLE itens_pedido ADD usuario_id INT NOT NULL AFTER produto_id;
ALTER TABLE itens_pedido MODIFY COLUMN usuario_id INT NULL;
ALTER TABLE pedidos
ADD COLUMN data_envio TIMESTAMP NULL,
ADD COLUMN data_entrega TIMESTAMP NULL;

ALTER TABLE usuarios MODIFY tipo_usuario ENUM('comprador','vendedor','admin') NOT NULL;
INSERT INTO usuarios (nome, email, senha, tipo_usuario, nome_loja)
VALUES ('Administrador', 'admin@lojaj.com',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Loja JK');