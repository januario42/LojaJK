<?php
session_start();

if (!isset($_SESSION['favoritos'])) {
    $_SESSION['favoritos'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    if (in_array($id, $_SESSION['favoritos'])) {
        // Já favoritado, então remove
        $_SESSION['favoritos'] = array_diff($_SESSION['favoritos'], [$id]);
    } else {
        // Ainda não favoritado, adiciona
        $_SESSION['favoritos'][] = $id;
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
