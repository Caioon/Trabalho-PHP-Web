<?php
session_start();
require_once 'db/conexao.php';

$mensagem = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);

        if (empty($email) || empty($senha)) {
            $mensagem = ["tipo" => "erro", "texto" => "Preencha todos os campos!"];
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($usuario && password_verify($senha, $usuario['senha'])) {
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];

                    header("Location: index.php");
                    exit;
                } else {
                    $mensagem = ["tipo" => "erro", "texto" => "E-mail ou senha incorretos!"];
                }
            } catch (Exception $e) {
                $mensagem = ["tipo" => "erro", "texto" => "Erro ao conectar com o banco de dados. Tente novamente mais tarde."];
            }
        }
    } 
    elseif (isset($_POST['logout'])) {
        $_SESSION = array();
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema PHP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s ease;
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
                Acesso ao Sistema
            </h1>
        </div>

        <?php if (isset($_SESSION['usuario_nome'])): ?>
            
            <div class="text-center p-6 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                <p class="text-xl font-semibold text-blue-800">
                    Seja bem-vindo, <span class="text-blue-600"><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>!
                </p>
                <p class="mt-2 text-gray-600">
                    Clique abaixo para acessar o sistema ou logar com outro usuário.
                </p>
            </div>

            <div class="space-y-4">
                <a href="produtos/listar.php" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-lg font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50 transition duration-150">
                    Acessar o Sistema
                </a>

                <form method="POST" action="index.php" class="w-full">
                    <input type="hidden" name="logout" value="1">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-150">
                        Logar com Outro Usuário
                    </button>
                </form>
            </div>

        <?php else: ?>

            <div class="text-center">
                <p class="text-lg text-gray-600">
                    Seja bem-vindo, faça login para acessar o sistema.
                </p>
            </div>

            <?php if (isset($_SESSION['aviso_login'])): ?>
                <div class="p-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg transition duration-300" role="alert">
                    <p class="font-medium"><?php echo htmlspecialchars($_SESSION['aviso_login']); ?></p>
                </div>
                <?php unset($_SESSION['aviso_login']); ?>
            <?php endif; ?>

            <?php if (!empty($mensagem) && $mensagem['tipo'] === 'erro'): ?>
                <div id="erro-box" class="p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg transition duration-300" role="alert">
                    <p class="font-medium"><?php echo htmlspecialchars($mensagem['texto']); ?></p>
                </div>
            <?php endif; ?>

            <form class="space-y-6" method="POST" action="index.php">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        E-mail
                    </label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           value="<?php echo htmlspecialchars($email); ?>"
                           class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150"
                           placeholder="email@exemplo.com">
                </div>

                <div>
                    <label for="senha" class="block text-sm font-medium text-gray-700 mb-1">
                        Senha
                    </label>
                    <input id="senha" name="senha" type="password" autocomplete="current-password" required
                           class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150"
                           placeholder="Digite sua senha">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 transform hover:scale-[1.01] transition duration-200 ease-in-out">
                        Entrar
                    </button>
                </div>
            </form>
            
            <div class="text-center text-sm pt-4">
                <p class="text-gray-600">
                    Ainda não tem conta?
                    <a href="auth/cadastro.php" class="font-semibold text-indigo-600 hover:text-indigo-800 transition duration-150 underline">
                        Cadastrar novo usuário
                    </a>
                </p>
            </div>

        <?php endif; ?>

    </div>

</body>
</html>
