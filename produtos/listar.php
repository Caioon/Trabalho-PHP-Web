<?php
require_once '../includes/verifica_login.php';
require_once '../db/conexao.php';

if (!isset($pdo)) {
}

try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT * FROM produtos ORDER BY id DESC");
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $produtos = [];
    }
} catch (Exception $e) {
    $produtos = [];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Produtos - Sistema PHP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .table-container {
            overflow-x: auto;
            border-radius: 0.75rem;
        }
        table {
            min-width: 768px;
        }
        th, td {
            white-space: nowrap;
        }
    </style>
</head>
<body class="p-4 md:p-8">

    <header class="bg-white shadow-lg rounded-xl p-4 mb-8 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        <h2 class="text-xl font-semibold text-gray-800">
            Bem-vindo, <span class="text-blue-600"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?></span>!
        </h2>
        
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 w-full md:w-auto">
            <a href="cadastrar.php" class="flex justify-center items-center py-2 px-4 rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 shadow-md transition duration-150 transform hover:scale-[1.02]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Novo Produto
            </a>
            
            <a href="../auth/logout.php" class="flex justify-center items-center py-2 px-4 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 shadow-md transition duration-150 transform hover:scale-[1.02]">
                Logout
            </a>

            <a href="../index.php" class="flex justify-center items-center py-2 px-4 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition duration-150 transform hover:scale-[1.02]">
                Voltar
            </a>
        </div>
    </header>

    <main class="bg-white shadow-xl rounded-xl p-6 md:p-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 border-b pb-2">
            Catálogo de Produtos
        </h1>

        <?php if (!empty($produtos)): ?>
            <div class="table-container shadow-md">
                <table class="w-full text-sm text-left text-gray-500 rounded-xl overflow-hidden">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="py-3 px-6">ID</th>
                            <th scope="col" class="py-3 px-6">Nome</th>
                            <th scope="col" class="py-3 px-6">Preço</th>
                            <th scope="col" class="py-3 px-6 hidden md:table-cell">Descrição</th>
                            <th scope="col" class="py-3 px-6">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $p): ?>
                            <tr class="bg-white border-b hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-medium text-gray-900"><?php echo htmlspecialchars($p['id']); ?></td>
                                <td class="py-4 px-6 font-medium text-gray-900"><?php echo htmlspecialchars($p['nome']); ?></td>
                                <td class="py-4 px-6 text-green-600 font-semibold">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                                <td class="py-4 px-6 hidden md:table-cell text-gray-600 max-w-xs overflow-hidden text-ellipsis"><?php echo htmlspecialchars(substr($p['descricao'], 0, 50)) . (strlen($p['descricao']) > 50 ? '...' : ''); ?></td>
                                <td class="py-4 px-6 text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="editar.php?id=<?php echo $p['id']; ?>" class="text-blue-600 hover:text-blue-900 font-medium">
                                            Editar
                                        </a>
                                        <a href="deletar.php?id=<?php echo $p['id']; ?>"
                                            onclick="return confirm('Tem certeza que deseja excluir o produto: <?php echo htmlspecialchars($p['nome']); ?>?');"
                                            class="text-red-600 hover:text-red-900 font-medium">
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
            <div class="text-center p-8 bg-yellow-50 border-2 border-yellow-300 rounded-lg shadow-inner">
                <p class="text-xl font-semibold text-gray-800">
                    Nenhum produto cadastrado ainda.
                </p>
                <p class="mt-2 text-gray-600">
                    Comece a adicionar produtos clicando no botão "Novo Produto" acima!
                </p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
