<?php
session_start();

if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    if (isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id] += 1;
    } else {
        $_SESSION['carrinho'][$id] = 1;
    }
}

header('Location: ../index.php#product');
exit;
