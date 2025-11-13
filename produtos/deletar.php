<?php
require_once '../auth/verifica_login.php';
require_once '../db/conexao.php';

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ? AND usuario_id = ?");
$stmt->execute([$id, $_SESSION['usuario_id']]);
$produto = $stmt->fetch();

if (!$produto) {
    echo "<p style='text-align:center; font-family:Arial;'>Produto não encontrado ou você não tem permissão para excluí-lo.</p>";
    echo "<p style='text-align:center;'><a href='listar.php'>Voltar</a></p>";
    exit;
}

$stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ? AND usuario_id = ?");
if ($stmt->execute([$id, $_SESSION['usuario_id']])) {
    header("Location: listar.php?msg=excluido");
    exit;
} else {
    echo "<p style='text-align:center; font-family:Arial;'>Erro ao excluir produto.</p>";
    echo "<p style='text-align:center;'><a href='listar.php'>Voltar</a></p>";
}
?>
