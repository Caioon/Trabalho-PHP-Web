<?php
require_once '../auth/verifica_login.php';
require_once '../db/conexao.php';

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = (int) $_GET['id'];
$mensagem = '';

try {
    if (!isset($pdo)) {
        throw new Exception("Erro de configuração: Conexão com o banco de dados ($pdo) não está disponível.");
    }
    
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        header("Location: listar.php");
        exit;
    }
} catch (Exception $e) {
    $mensagem = "❌ Erro ao carregar o produto: " . $e->getMessage();
    $produto = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $produto) {
    $nome = trim($_POST['nome']);
    $preco = str_replace(',', '.', $_POST['preco']);
    $descricao = trim($_POST['descricao']);

    if (!empty($nome) && is_numeric($preco)) {
        try {
            $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ?, descricao = ? WHERE id = ?");
            if ($stmt->execute([$nome, $preco, $descricao, $id])) {
                $mensagem = "✅ Produto atualizado com sucesso!";
                
                $produto['nome'] = $nome;
                $produto['preco'] = $preco;
                $produto['descricao'] = $descricao;
            } else {
                $mensagem = "❌ Erro ao atualizar produto.";
            }
        } catch (Exception $e) {
            $mensagem = "❌ Erro ao executar a atualização: " . $e->getMessage();
        }
    } else {
        $mensagem = "⚠️ Preencha o nome e um preço válido.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto: <?php echo htmlspecialchars($produto['nome'] ?? 'Erro'); ?></title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/produtos-form.css">
</head>
<body>

    <div class="container-produtos">
        <div class="card-produtos">

        <div class="produtos-header">
            <h1 class="produtos-title">
                Editar Produto
            </h1>
            <p class="produto-id-info">
                ID #<?php echo htmlspecialchars($produto['id'] ?? 'N/A'); ?>: <?php echo htmlspecialchars($produto['nome'] ?? 'Produto Inválido'); ?>
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

        <?php if ($produto): ?>
        <form class="space-y-6" method="POST" action="editar.php?id=<?php echo $id; ?>">
            
            <div class="form-group">
                <label for="nome" class="form-label">
                    Nome do Produto <span class="text-required">*</span>
                </label>
                <input id="nome" name="nome" type="text" required
                       value="<?php echo htmlspecialchars($produto['nome']); ?>"
                       class="form-input form-input-green"
                       placeholder="Nome completo do produto">
            </div>

            <div class="form-group">
                <label for="preco" class="form-label">
                    Preço (R$) <span class="text-required">*</span>
                </label>
                <input id="preco" name="preco" type="text" inputmode="numeric" required
                       value="<?php echo htmlspecialchars(number_format($produto['preco'], 2, ',', '')); ?>"
                       class="form-input form-input-green"
                       placeholder="Ex: 19,99">
            </div>

            <div class="form-group">
                <label for="descricao" class="form-label">
                    Descrição
                </label>
                <textarea id="descricao" name="descricao" rows="4"
                          class="form-textarea form-input-green"
                          placeholder="Detalhes sobre o produto"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
            </div>

            <div class="btn-actions">
                
                <button type="submit" class="btn btn-blue">
                    Salvar Alterações
                </button>

                <a href="listar.php" class="btn btn-back">
                    Voltar para a Lista
                </a>
            </div>
        </form>
        <?php endif; ?>

    </div>
    </div>

</body>
</html>
