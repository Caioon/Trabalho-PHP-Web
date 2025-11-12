<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Produto</title>
    <link rel="stylesheet" href="cadastrar.css">
</head>
<body>
    <h1>Novo Produto</h1>

    <?php if ($mensagem): ?>
        <p class="msg"><?php echo htmlspecialchars($mensagem); ?></p>
    <?php endif; ?>

    <form method="POST" action="cadastrar.php">
        <label for="nome">Nome do Produto *</label>
        <input type="text" name="nome" id="nome" required>

        <label for="preco">Preço (ex: 49.90) *</label>
        <input type="text" name="preco" id="preco" required>

        <label for="descricao">Descrição</label>
        <textarea name="descricao" id="descricao" rows="4"></textarea>

        <button type="submit">Cadastrar</button>
    </form>

    <div class="voltar">
        <a href="listar.php">⬅ Voltar à lista</a>
    </div>
</body>
</html>

