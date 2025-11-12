<?php
session_start();

// Remove todas as variáveis da sessão
session_unset();

// Destroi a sessão atual
session_destroy();

// Redireciona de volta para a tela de login
header("Location: login.php");
exit;
?>

