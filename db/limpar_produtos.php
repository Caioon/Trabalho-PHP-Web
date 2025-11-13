<?php
session_start();

require_once '../auth/verifica_login.php';
require_once '../db/conexao.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produtos");
    $resultado = $stmt->fetch();
    $totalProdutos = $resultado['total'];

    if ($totalProdutos == 0) {
        $_SESSION['mensagem_mock'] = [
            'tipo' => 'warning',
            'texto' => "⚠️ Não há produtos para deletar. A tabela já está vazia."
        ];
    } else {
        $stmt = $pdo->prepare("DELETE FROM produtos");
        $stmt->execute();

        $pdo->exec("ALTER TABLE produtos AUTO_INCREMENT = 1");

        $_SESSION['mensagem_mock'] = [
            'tipo' => 'success',
            'texto' => "✅ Todos os produtos foram deletados com sucesso! Total de {$totalProdutos} produto(s) removido(s)."
        ];
    }

} catch (Exception $e) {
    error_log("Erro ao limpar produtos: " . $e->getMessage());
    
    $_SESSION['mensagem_mock'] = [
        'tipo' => 'error',
        'texto' => "❌ Erro ao deletar produtos: " . $e->getMessage()
    ];
}

header("Location: ../index.php");
exit;
?>
