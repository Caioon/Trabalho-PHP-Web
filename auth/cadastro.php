<?php
require_once '../db/conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos!";
    } else {
        try {
            if (!isset($pdo)) {
                 $erro = "Erro de configuração: Conexão com o banco de dados ($pdo) não está disponível.";
            } else {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
        
                if ($stmt->rowCount() > 0) {
                    $erro = "E-mail já cadastrado!";
                } else {
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        
                    $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha) VALUES (:nome, :email, :senha)");
                    $stmt->bindParam(':nome', $nome);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':senha', $senhaHash);
        
                    if ($stmt->execute()) {
                        $sucesso = "Usuário cadastrado com sucesso! <a href='../index.php' class='text-blue-600 hover:text-blue-800 font-semibold underline'>Fazer login</a>";
                    } else {
                        $erro = "Erro ao cadastrar usuário.";
                    }
                }
            }
        } catch (Exception $e) {
            $erro = "Erro ao conectar com o banco de dados. Tente novamente mais tarde.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8 space-y-6 transform hover:shadow-3xl transition duration-300">

        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">
                Criar Nova Conta
            </h1>
            <p class="text-md text-gray-600 mt-2">
                Preencha os dados abaixo para se cadastrar no sistema.
            </p>
        </div>

        <?php if (!empty($sucesso)): ?>
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg" role="alert">
                <p class="font-medium"><?php echo $sucesso; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($erro)): ?>
            <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg" role="alert">
                <p class="font-medium"><?php echo htmlspecialchars($erro); ?></p>
            </div>
        <?php endif; ?>

        <form class="space-y-6" method="POST" action="cadastro.php">
            
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">
                    Nome Completo
                </label>
                <input id="nome" name="nome" type="text" autocomplete="name" required
                       class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150"
                       placeholder="Seu nome">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    E-mail
                </label>
                <input id="email" name="email" type="email" autocomplete="email" required
                       class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150"
                       placeholder="email@exemplo.com">
            </div>

            <div>
                <label for="senha" class="block text-sm font-medium text-gray-700 mb-1">
                    Senha
                </label>
                <input id="senha" name="senha" type="password" autocomplete="new-password" required
                       class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150"
                       placeholder="Crie uma senha forte">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50 transform hover:scale-[1.01] transition duration-200 ease-in-out">
                    Cadastrar
                </button>
            </div>
        </form>

        <div class="text-center text-sm">
            <p class="text-gray-600">
                Já tem uma conta? 
                <a href="../index.php" class="font-medium text-blue-600 hover:text-blue-800">
                    Acesse aqui.
                </a>
            </p>
        </div>

    </div>

</body>
</html>