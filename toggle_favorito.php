<?php
session_start();

if (!isset($_SESSION['favoritos'])) {
    $_SESSION['favoritos'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    if (in_array($id, $_SESSION['favoritos'])) {
        // Remover dos favoritos
        $_SESSION['favoritos'] = array_diff($_SESSION['favoritos'], [$id]);
        $status = 'removido';
    } else {
        // Adicionar aos favoritos
        $_SESSION['favoritos'][] = $id;
        $status = 'adicionado';
    }

    echo json_encode(['success' => true, 'status' => $status]);
    exit;
}

echo json_encode(['success' => false]);
exit;
