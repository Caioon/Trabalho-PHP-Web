<?php
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_nome'])) {
    $_SESSION['aviso_login'] = "⚠️ Faça login para acessar essa página.";
    
    header("Location: ../index.php");
    exit;
}
?>