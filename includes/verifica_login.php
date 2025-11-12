<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_nome'])) {
    // Define uma mensagem de aviso na sessão
    $_SESSION['aviso_login'] = "⚠️ Faça login para acessar essa página.";
    
    // Redireciona para o login
    header("Location: ../auth/login.php");
    exit;
}
?>

