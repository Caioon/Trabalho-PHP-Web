<?php
require_once '../auth/verifica_login.php';
require_once '../db/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listar.php");
    exit;
}

if (!isset($_POST['id']) || !is_numeric($_POST['id']) || $_POST['id'] <= 0) {
    header("Location: listar.php?erro=id_invalido");
    exit;
}

$id = (int) $_POST['id'];

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) {
    header("Location: listar.php?erro=produto_nao_encontrado");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
if ($stmt->execute([$id])) {
    header("Location: listar.php?msg=excluido");
    exit;
} else {
    header("Location: listar.php?erro=falha_exclusao");
    exit;
}
