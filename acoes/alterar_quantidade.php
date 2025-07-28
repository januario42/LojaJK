<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['delta'])) {
    $id = (int)$_POST['id'];
    $delta = (int)$_POST['delta'];
    
    if (isset($_SESSION['carrinho'][$id])) {
        $nova_quantidade = $_SESSION['carrinho'][$id] + $delta;
        
        if ($nova_quantidade <= 0) {
            unset($_SESSION['carrinho'][$id]);
        } else {
            $_SESSION['carrinho'][$id] = $nova_quantidade;
        }
        
        echo json_encode(['status' => 'success', 'mensagem' => 'Quantidade alterada']);
    } else {
        echo json_encode(['status' => 'error', 'mensagem' => 'Produto não encontrado no carrinho']);
    }
} else {
    echo json_encode(['status' => 'error', 'mensagem' => 'Dados inválidos']);
}
?> 