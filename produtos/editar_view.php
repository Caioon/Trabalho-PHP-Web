<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="editar.css">
</head>
<body>
    <h1>Editar Produto</h1>

    <?php if ($mensagem): ?>
        <p class="msg"><?php echo htmlspecialchars($mensagem); ?></p>
    <?php endif; ?>

    <form method="POST" action="editar.php?id=<?php echo $id; ?>">
        <label for="nome">Nome do Produto *</label>
        <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>

        <label for="preco">Preço *</label>
        <input type="text" name="preco" id="preco" value="<?php echo htmlspecialchars($produto['preco']); ?>" required>

        <label for="descricao">Descrição</label>
        <textarea name="descricao" id="descricao" rows="4"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>

        <button type="submit">Salvar Alterações</button>
    </form>

    <div class="voltar">
        <a href="listar.php">⬅ Voltar à lista</a>
    </div>
</body>
</html>

