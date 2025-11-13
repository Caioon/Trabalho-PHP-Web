<?php
require_once '../auth/verifica_login.php';
require_once '../db/conexao.php';

$mensagem = '';
$nome = '';
$preco = '';
$descricao = '';
$privado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $preco = str_replace(',', '.', $_POST['preco']);
    $descricao = trim($_POST['descricao']);
    $privado = isset($_POST['privado']) ? 1 : 0;

    if (!empty($nome) && is_numeric($preco)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, descricao, privado, usuario_id) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$nome, $preco, $descricao, $privado, $_SESSION['usuario_id']])) {
                $mensagem = "✅ Produto cadastrado com sucesso!";
                $nome = '';
                $preco = '';
                $descricao = '';
                $privado = false;
            } else {
                $mensagem = "❌ Erro ao cadastrar produto.";
            }
        } catch (Exception $e) {
            $mensagem = "❌ Erro de execução no banco de dados. Tente novamente mais tarde.";
        }
    } else {
        $mensagem = "⚠️ Preencha o nome e o preço com valores válidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Novo Produto</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/produtos-form.css">
</head>
<body>

    <div class="container-produtos">
        <div class="card-produtos">

        <div class="produtos-header">
            <h1 class="produtos-title">
                Novo Produto
            </h1>
            <p class="produtos-subtitle">
                Insira os detalhes do item que deseja adicionar ao catálogo.
            </p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <?php 
                $tipo = strpos($mensagem, '✅') !== false ? 'success' : (strpos($mensagem, '❌') !== false ? 'error' : 'warning');
                $corClass = ['success' => 'alert-green', 'error' => 'alert-red', 'warning' => 'alert-yellow'][$tipo];
            ?>
            <div class="alert-produtos <?php echo $corClass; ?>">
                <p><?php echo htmlspecialchars($mensagem); ?></p>
            </div>
        <?php endif; ?>

        <form class="space-y-6" method="POST" action="cadastrar.php">
            
            <div class="form-group">
                <label for="nome" class="form-label">
                    Nome do Produto <span class="text-required">*</span>
                </label>
                <input id="nome" name="nome" type="text" required
                       value="<?php echo htmlspecialchars($nome); ?>"
                       class="form-input form-input-green"
                       placeholder="Ex: Tênis Esportivo">
            </div>

            <div class="form-group">
                <label for="preco" class="form-label">
                    Preço (R$) <span class="text-required">*</span>
                </label>
                <input id="preco" name="preco" type="text" inputmode="numeric" required
                       value="<?php echo htmlspecialchars($preco); ?>"
                       class="form-input form-input-green"
                       placeholder="Ex: 99,90">
            </div>

            <div class="form-group">
                <label for="descricao" class="form-label">
                    Descrição
                </label>
                <textarea id="descricao" name="descricao" rows="4"
                          class="form-textarea form-input-green"
                          placeholder="Detalhes sobre o produto..."><?php echo htmlspecialchars($descricao); ?></textarea>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="privado" id="privado" value="1" <?php echo $privado ? 'checked' : ''; ?>>
                    <span>Tornar produto privado. Isso impede que outros usuários vejam seus produtos cadastrados</span>
                </label>
            </div>

            <div class="btn-actions">
                
                <button type="submit" class="btn btn-green">
                    Cadastrar Produto
                </button>

                <a href="listar.php" class="btn btn-back">
                    Voltar para a Lista
                </a>
            </div>
        </form>

    </div>
    </div>

</body>
</html>
