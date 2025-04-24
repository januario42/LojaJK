<?php
session_start();

if (!isset($_SESSION['favoritos'])) {
    $_SESSION['favoritos'] = [];
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id > 0) {
    if (in_array($id, $_SESSION['favoritos'])) {
        $_SESSION['favoritos'] = array_diff($_SESSION['favoritos'], [$id]);
        echo json_encode(['status' => 'removido']);
    } else {
        $_SESSION['favoritos'][] = $id;
        echo json_encode(['status' => 'favoritado']);
    }
} else {
    echo json_encode(['status' => 'erro']);
}
