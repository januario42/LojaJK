-- Script para recriar o banco de dados da LojaJK
-- Execute este script no seu MySQL/MariaDB para recriar as tabelas

-- Criar o banco de dados se não existir
CREATE DATABASE IF NOT EXISTS almoxarifado;
USE almoxarifado;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('comprador', 'vendedor') NOT NULL DEFAULT 'comprador',
    cpf_cnpj VARCHAR(18) NULL,
    nome_loja VARCHAR(255) NULL,
    imagem_perfil VARCHAR(255) NULL,
    token VARCHAR(255) NULL,
    expiracao_token DATETIME NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de produtos
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_vendedor INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    imagem VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_vendedor) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Índices para melhor performance
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_usuarios_tipo ON usuarios(tipo_usuario);
CREATE INDEX idx_produtos_vendedor ON produtos(id_vendedor);
CREATE INDEX idx_produtos_data ON produtos(data_cadastro);

-- Inserir alguns dados de exemplo (opcional)

-- Usuário administrador de exemplo
INSERT INTO usuarios (nome, email, senha, tipo_usuario, nome_loja) VALUES 
('Administrador', 'admin@lojajk.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendedor', 'Loja JK');

-- Produtos de exemplo (se houver um vendedor cadastrado)
-- INSERT INTO produtos (id_vendedor, nome, descricao, preco, estoque, imagem) VALUES 
-- (1, 'Produto Exemplo', 'Descrição do produto exemplo', 99.99, 10, 'produto_exemplo.jpg');

-- Verificar se as tabelas foram criadas corretamente
SHOW TABLES;
DESCRIBE usuarios;
DESCRIBE produtos; 