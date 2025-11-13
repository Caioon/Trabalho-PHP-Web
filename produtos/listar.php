<?php
require_once '../auth/verifica_login.php';
require_once '../db/conexao.php';

if (!isset($pdo)) {
}

try {
    if (isset($pdo)) {
        // Buscar produtos do usuário logado (públicos e privados)
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE usuario_id = ? ORDER BY id DESC");
        $stmt->execute([$_SESSION['usuario_id']]);
        $meusProdutos = $stmt->fetchAll();

        // Buscar produtos públicos de outros usuários
        $stmt = $pdo->prepare("
            SELECT p.*, u.nome as usuario_nome 
            FROM produtos p 
            INNER JOIN users u ON p.usuario_id = u.id 
            WHERE p.usuario_id != ? AND p.privado = 0 
            ORDER BY p.id DESC
        ");
        $stmt->execute([$_SESSION['usuario_id']]);
        $produtosOutros = $stmt->fetchAll();
    } else {
        $meusProdutos = [];
        $produtosOutros = [];
    }
} catch (Exception $e) {
    $meusProdutos = [];
    $produtosOutros = [];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Produtos - Sistema PHP</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/listar.css">
</head>
<body>

    <header class="header-listar">
        <h2 class="header-welcome">
            Bem-vindo, <span class="user-name"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?></span>!
        </h2>
        
        <div class="header-buttons">
            <a href="cadastrar.php" class="btn-header btn-new-product">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Novo Produto
            </a>
            
            <a href="../auth/logout.php" class="btn-header btn-logout">
                Logout
            </a>

            <a href="../index.php" class="btn-header btn-voltar">
                Voltar
            </a>
        </div>
    </header>

    <main class="main-content">
        <h1 class="main-title">
            Catálogo de Produtos
        </h1>

        <!-- TABELA A: Meus Produtos -->
        <section class="section-produtos">
            <h2 class="section-subtitle">Produtos cadastrados por você</h2>

            <?php if (!empty($meusProdutos)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th class="hidden-mobile">Descrição</th>
                                <th>Produto Privado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($meusProdutos as $p): ?>
                                <tr>
                                    <td class="id-col"><?php echo htmlspecialchars($p['id']); ?></td>
                                    <td class="nome-col"><?php echo htmlspecialchars($p['nome']); ?></td>
                                    <td class="preco-col">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                                    <td class="descricao-col hidden-mobile"><?php echo htmlspecialchars(substr($p['descricao'], 0, 50)) . (strlen($p['descricao']) > 50 ? '...' : ''); ?></td>
                                    <td class="privado-col"><?php echo $p['privado'] ? 'Sim' : 'Não'; ?></td>
                                    <td class="actions-cell">
                                        <div class="actions-cell-content">
                                            <a href="editar.php?id=<?php echo $p['id']; ?>" class="link-edit">
                                                Editar
                                            </a>
                                            <a href="deletar.php?id=<?php echo $p['id']; ?>"
                                                onclick="return confirm('Tem certeza que deseja excluir o produto: <?php echo htmlspecialchars($p['nome']); ?>?');"
                                                class="link-delete">
                                                Excluir
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p class="empty-state-title">
                        Você ainda não cadastrou nenhum produto.
                    </p>
                    <p class="empty-state-text">
                        Comece a adicionar produtos clicando no botão "Novo Produto" acima!
                    </p>
                </div>
            <?php endif; ?>
        </section>

        <!-- TABELA B: Produtos de Outros Usuários -->
        <section class="section-produtos">
            <h2 class="section-subtitle">Produtos públicos cadastrados por outros usuários</h2>

            <?php if (!empty($produtosOutros)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th class="hidden-mobile">Descrição</th>
                                <th>Usuário Criador</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtosOutros as $p): ?>
                                <tr>
                                    <td class="id-col"><?php echo htmlspecialchars($p['id']); ?></td>
                                    <td class="nome-col"><?php echo htmlspecialchars($p['nome']); ?></td>
                                    <td class="preco-col">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                                    <td class="descricao-col hidden-mobile"><?php echo htmlspecialchars(substr($p['descricao'], 0, 50)) . (strlen($p['descricao']) > 50 ? '...' : ''); ?></td>
                                    <td class="usuario-col"><?php echo htmlspecialchars($p['usuario_nome']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p class="empty-state-title">
                        Não existem outros produtos cadastrados.
                    </p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
