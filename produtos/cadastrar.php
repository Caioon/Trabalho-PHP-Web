<?php
require_once '../includes/verifica_login.php';
require_once '../db/conexao.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $preco = str_replace(',', '.', $_POST['preco']);
    $descricao = trim($_POST['descricao']);

    if (!empty($nome) && !empty($preco)) {
        $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, descricao) VALUES (?, ?, ?)");
        if ($stmt->execute([$nome, $preco, $descricao])) {
            $mensagem = "✅ Produto cadastrado com sucesso!";
        } else {
            $mensagem = "❌ Erro ao cadastrar produto.";
        }
    } else {
        $mensagem = "⚠️ Preencha todos os campos obrigatórios.";
    }
}

// carrega a view separada (HTML)
include 'cadastrar_view.php';

