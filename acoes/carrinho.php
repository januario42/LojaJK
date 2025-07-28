<?php
session_start();

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    if (isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id]++;
    } else {
        $_SESSION['carrinho'][$id] = 1;
    }
}

$total = array_sum($_SESSION['carrinho']);
echo json_encode(['status' => 'ok', 'total' => $total]);
