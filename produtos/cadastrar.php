<?php
require_once '../includes/verifica_login.php';
require_once '../db/conexao.php';

$mensagem = '';
$nome = '';
$preco = '';
$descricao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $preco = str_replace(',', '.', $_POST['preco']);
    $descricao = trim($_POST['descricao']);

    if (!empty($nome) && is_numeric($preco)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, descricao) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$nome, $preco, $descricao])) {
                $mensagem = "✅ Produto cadastrado com sucesso!";
                $nome = '';
                $preco = '';
                $descricao = '';
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
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        input:focus, textarea:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.5);
        }
    </style>
</head>
<body class="min-h-screen flex items-start md:items-center justify-center p-4">

    <div class="w-full max-w-lg bg-white rounded-xl shadow-2xl p-8 space-y-6 transform hover:shadow-3xl transition duration-300 mt-8 md:mt-0">

        <div class="text-center border-b pb-4">
            <h1 class="text-3xl font-extrabold text-gray-900">
                Novo Produto
            </h1>
            <p class="text-md text-gray-600 mt-2">
                Insira os detalhes do item que deseja adicionar ao catálogo.
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

        <form class="space-y-6" method="POST" action="cadastrar.php">
            
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">
                    Nome do Produto <span class="text-red-500">*</span>
                </label>
                <input id="nome" name="nome" type="text" required
                       value="<?php echo htmlspecialchars($nome); ?>"
                       class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm transition duration-150"
                       placeholder="Ex: Tênis Esportivo">
            </div>

            <div>
                <label for="preco" class="block text-sm font-medium text-gray-700 mb-1">
                    Preço (R$) <span class="text-red-500">*</span>
                </label>
                <input id="preco" name="preco" type="text" inputmode="numeric" required
                       value="<?php echo htmlspecialchars($preco); ?>"
                       class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm transition duration-150"
                       placeholder="Ex: 99,90">
            </div>

            <div>
                <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">
                    Descrição
                </label>
                <textarea id="descricao" name="descricao" rows="4"
                          class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm transition duration-150"
                          placeholder="Detalhes sobre o produto..."><?php echo htmlspecialchars($descricao); ?></textarea>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                
                <button type="submit" class="w-full sm:w-1/2 flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50 transform hover:scale-[1.01] transition duration-200 ease-in-out">
                    Cadastrar Produto
                </button>

                <a href="listar.php" class="w-full sm:w-1/2 flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-150 items-center">
                    Voltar para a Lista
                </a>
            </div>
        </form>

    </div>

</body>
</html>