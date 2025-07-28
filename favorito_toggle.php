<?php
session_start();

if (!isset($_SESSION['favoritos'])) {
    $_SESSION['favoritos'] = [];
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id > 0) {
    if (in_array($id, $_SESSION['favoritos'])) {
        $_SESSION['favoritos'] = array_values(array_diff($_SESSION['favoritos'], [$id]));
        $status = 'removido';
    } else {
        $_SESSION['favoritos'][] = $id;
        $status = 'favoritado';
    }

    echo json_encode([
        'status' => $status,
        'total' => count($_SESSION['favoritos'])
    ]);
} else {
    echo json_encode(['status' => 'erro', 'total' => count($_SESSION['favoritos'])]);
}
