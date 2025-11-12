<?php
require_once '../db/conexao.php'; // inclui a conexão com o banco

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // Validação básica
    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos!";
    } else {
        // Verifica se o e-mail já existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $erro = "E-mail já cadastrado!";
        } else {
            // Criptografa a senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Insere o usuário
            $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha) VALUES (:nome, :email, :senha)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senhaHash);

            if ($stmt->execute()) {
                $sucesso = "Usuário cadastrado com sucesso! <a href='login.php'>Fazer login</a>";
            } else {
                $erro = "Erro ao cadastrar usuário.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
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
        <h2>Cadastro</h2>
        <input type="text" name="nome" placeholder="Nome completo" required>
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Cadastrar</button>
        <div class="msg">
            <?php if (!empty($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
            <?php if (!empty($sucesso)) echo "<p style='color:green;'>$sucesso</p>"; ?>
        </div>
    </form>
</body>
</html>

