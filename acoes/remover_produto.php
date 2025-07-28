<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    if (isset($_SESSION['carrinho'][$id])) {
        unset($_SESSION['carrinho'][$id]);
        echo json_encode(['status' => 'success', 'mensagem' => 'Produto removido do carrinho']);
    } else {
        echo json_encode(['status' => 'error', 'mensagem' => 'Produto não encontrado no carrinho']);
    }
} else {
    echo json_encode(['status' => 'error', 'mensagem' => 'Dados inválidos']);
}
?> 