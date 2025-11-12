<?php
session_start(); // inicia a sessão
require_once '../db/conexao.php'; // conexão com o banco

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos!";
    } else {
        // Busca o usuário pelo e-mail
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Login válido: cria variáveis de sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

            // Redireciona para a página inicial protegida
            header("Location: ../produtos/listar.php");
            exit;
        } else {
            $erro = "E-mail ou senha incorretos!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; width: 300px; }
        input { width: 100%; padding: 8px; margin: 8px 0; }
        button { background: #007bff; color: #fff; border: none; padding: 10px; cursor: pointer; width: 100%; border-radius: 5px; }
        button:hover { background: #0056b3; }
        .msg { text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Login</h2>

        <!-- 🔹 MENSAGEM DE AVISO QUANDO TENTAR ACESSAR SEM LOGIN -->
        <?php if (isset($_SESSION['aviso_login'])): ?>
            <p style="color: red; text-align: center;">
                <?php echo $_SESSION['aviso_login']; ?>
            </p>
            <?php unset($_SESSION['aviso_login']); // limpa após exibir ?>
        <?php endif; ?>

        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Entrar</button>

        <div class="msg">
            <?php if (!empty($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
        </div>

        <p style="text-align:center;">Não tem conta? <a href="register.php">Cadastre-se</a></p>
    </form>
</body>
</html>

