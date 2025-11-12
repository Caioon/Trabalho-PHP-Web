<?php
require_once '../includes/verifica_login.php';
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
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        input:focus, textarea:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="min-h-screen flex items-start md:items-center justify-center p-4">

    <div class="w-full max-w-lg bg-white rounded-xl shadow-2xl p-8 space-y-6 transform hover:shadow-3xl transition duration-300 mt-8 md:mt-0">

        <div class="text-center border-b pb-4">
            <h1 class="text-3xl font-extrabold text-gray-900">
                Editar Produto
            </h1>
            <p class="text-xl text-blue-600 mt-2 font-semibold">
                ID #<?php echo htmlspecialchars($produto['id'] ?? 'N/A'); ?>: <?php echo htmlspecialchars($produto['nome'] ?? 'Produto Inválido'); ?>
            </p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <?php 
                $tipo = strpos($mensagem, '✅') !== false ? 'success' : (strpos($mensagem, '❌') !== false ? 'error' : 'warning');
                $cor = ['success' => 'green', 'error' => 'red', 'warning' => 'yellow'][$tipo];
            ?>
            <div class="p-4 bg-<?php echo $cor; ?>-100 border border-<?php echo $cor; ?>-400 text-<?php echo $cor; ?>-700 rounded-lg shadow-md" role="alert">
                <p class="font-medium"><?php echo htmlspecialchars($mensagem); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($produto): ?>
        <form class="space-y-6" method="POST" action="editar.php?id=<?php echo $id; ?>">
            
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">
                    Nome do Produto <span class="text-red-500">*</span>
                </label>
                <input id="nome" name="nome" type="text" required
                       value="<?php echo htmlspecialchars($produto['nome']); ?>"
                       class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150"
                       placeholder="Nome completo do produto">
            </div>

            <div>
                <label for="preco" class="block text-sm font-medium text-gray-700 mb-1">
                    Preço (R$) <span class="text-red-500">*</span>
                </label>
                <input id="preco" name="preco" type="text" inputmode="numeric" required
                       value="<?php echo htmlspecialchars(number_format($produto['preco'], 2, ',', '')); ?>"
                       class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150"
                       placeholder="Ex: 19,99">
            </div>

            <div>
                <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">
                    Descrição
                </label>
                <textarea id="descricao" name="descricao" rows="4"
                          class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150"
                          placeholder="Detalhes sobre o produto"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                
                <button type="submit" class="w-full sm:w-1/2 flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 transform hover:scale-[1.01] transition duration-200 ease-in-out">
                    Salvar Alterações
                </button>

                <a href="listar.php" class="w-full sm:w-1/2 flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-150 items-center">
                    Voltar para a Lista
                </a>
            </div>
        </form>
        <?php endif; ?>

    </div>

</body>
</html>