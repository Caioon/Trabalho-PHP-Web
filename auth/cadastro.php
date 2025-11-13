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
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/cadastro.css">
</head>
<body>

    <div class="container-center">
        <div class="card">

        <div class="text-center">
            <h1 class="card-title">
                Criar Nova Conta
            </h1>
            <p class="cadastro-subtitle">
                Preencha os dados abaixo para se cadastrar no sistema.
            </p>
        </div>

        <?php if (!empty($sucesso)): ?>
            <div class="alert alert-success">
                <p><?php echo $sucesso; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-error">
                <p><?php echo htmlspecialchars($erro); ?></p>
            </div>
        <?php endif; ?>

        <form class="space-y-6" method="POST" action="cadastro.php">
            
            <div class="form-group">
                <label for="nome" class="form-label">
                    Nome Completo
                </label>
                <input id="nome" name="nome" type="text" autocomplete="name" required
                       class="form-input"
                       placeholder="Seu nome">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">
                    E-mail
                </label>
                <input id="email" name="email" type="email" autocomplete="email" required
                       class="form-input"
                       placeholder="email@exemplo.com">
            </div>

            <div class="form-group">
                <label for="senha" class="form-label">
                    Senha
                </label>
                <input id="senha" name="senha" type="password" autocomplete="new-password" required
                       class="form-input"
                       placeholder="Crie uma senha forte">
            </div>

            <div>
                <button type="submit" class="btn btn-indigo">
                    Cadastrar
                </button>
            </div>
        </form>

        <div class="login-link">
            <p>
                Já tem uma conta? 
                <a href="../index.php">
                    Acesse aqui.
                </a>
            </p>
        </div>

    </div>
    </div>

</body>
</html>
