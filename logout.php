<?php
session_start();
session_destroy();
header("Location: logado.php"); // Redireciona para a página inicial após logout
exit();
?>
