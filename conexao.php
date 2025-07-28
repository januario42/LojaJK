<?php
$host = "localhost"; // Altere se necessário
$usuario = "root"; // Usuário do banco
$senha = "root"; // Senha do banco
$banco = "almoxarifado"; // Nome do banco de dados

$conexao = new mysqli($host, $usuario, $senha, $banco);

// Verifica a conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}
?>
