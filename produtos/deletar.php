<?php
require_once '../includes/verifica_login.php';
require_once '../db/conexao.php';

// Verifica se veio o ID pela URL
if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = (int) $_GET['id'];

// Verifica se o produto existe
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    echo "<p style='text-align:center; font-family:Arial;'>Produto não encontrado.</p>";
    echo "<p style='text-align:center;'><a href='listar.php'>Voltar</a></p>";
    exit;
}

// Realiza a exclusão
$stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
if ($stmt->execute([$id])) {
    // Redireciona para a lista com uma flag de sucesso
    header("Location: listar.php?msg=excluido");
    exit;
} else {
    echo "<p style='text-align:center; font-family:Arial;'>Erro ao excluir produto.</p>";
    echo "<p style='text-align:center;'><a href='listar.php'>Voltar</a></p>";
}

