<?php
require_once '../includes/verifica_login.php';
require_once '../db/conexao.php';

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = (int) $_GET['id'];
$mensagem = '';

// Busca o produto atual
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    echo "Produto não encontrado.";
    exit;
}

// Se enviou o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $preco = str_replace(',', '.', $_POST['preco']);
    $descricao = trim($_POST['descricao']);

    if (!empty($nome) && !empty($preco)) {
        $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ?, descricao = ? WHERE id = ?");
        if ($stmt->execute([$nome, $preco, $descricao, $id])) {
            $mensagem = "✅ Produto atualizado com sucesso!";
            $produto['nome'] = $nome;
            $produto['preco'] = $preco;
            $produto['descricao'] = $descricao;
        } else {
            $mensagem = "❌ Erro ao atualizar produto.";
        }
    } else {
        $mensagem = "⚠️ Preencha todos os campos obrigatórios.";
    }
}

// carrega a view separada
include 'editar_view.php';

