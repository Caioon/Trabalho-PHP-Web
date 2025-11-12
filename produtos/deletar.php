<?php
require_once '../includes/verifica_login.php';
require_once '../db/conexao.php';

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    echo "<p style='text-align:center; font-family:Arial;'>Produto não encontrado.</p>";
    echo "<p style='text-align:center;'><a href='listar.php'>Voltar</a></p>";
    exit;
}

$stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
if ($stmt->execute([$id])) {
    header("Location: listar.php?msg=excluido");
    exit;
} else {
    echo "<p style='text-align:center; font-family:Arial;'>Erro ao excluir produto.</p>";
    echo "<p style='text-align:center;'><a href='listar.php'>Voltar</a></p>";
}
?>