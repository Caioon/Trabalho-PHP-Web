<?php
require_once '../includes/verifica_login.php'; // protege a página
require_once '../db/conexao.php'; // conexão com banco

// Busca todos os produtos
$stmt = $pdo->query("SELECT * FROM produtos ORDER BY id DESC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Produtos</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; padding: 20px; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; }
        .btn { padding: 6px 10px; background: #007bff; color: white; border-radius: 5px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="top-bar">
        <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</h2>
        <div>
            <a href="cadastrar.php" class="btn">Novo Produto</a>
            <a href="../auth/logout.php" class="btn" style="background:#dc3545;">Sair</a>
        </div>
    </div>

    <h1>Lista de Produtos</h1>

    <?php if (count($produtos) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['nome']); ?></td>
                        <td>R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($p['descricao']); ?></td>
                        <td>
                            <a href="editar.php?id=<?php echo $p['id']; ?>">Editar</a> |
                            <a href="deletar.php?id=<?php echo $p['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">Nenhum produto cadastrado ainda.</p>
    <?php endif; ?>
</body>
</html>

