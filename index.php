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

                $usuario = $stmt->fetch();

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
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <div class="container-center">
        <div class="card">

        <div class="text-center">
            <h1 class="card-title">
                Acesso ao Sistema
            </h1>
        </div>

        <?php if (isset($_SESSION['usuario_nome'])): ?>
            
            <div class="welcome-box">
                <p class="welcome-title">
                    Seja bem-vindo, <span class="welcome-name"><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>!
                </p>
                <p class="welcome-subtitle">
                    Clique abaixo para acessar o sistema ou logar com outro usuário.
                </p>
            </div>

            <div class="btn-group">
                <a href="produtos/listar.php" class="btn btn-access">
                    Acessar o Sistema
                </a>

                <form method="POST" action="index.php">
                    <input type="hidden" name="logout" value="1">
                    <button type="submit" class="btn btn-logout">
                        Logar com Outro Usuário
                    </button>
                </form>
            </div>

        <?php else: ?>

            <div class="text-center">
                <p class="card-subtitle">
                    Seja bem-vindo, faça login para acessar o sistema.
                </p>
            </div>

            <?php if (isset($_SESSION['aviso_login'])): ?>
                <div class="alert alert-warning">
                    <p><?php echo htmlspecialchars($_SESSION['aviso_login']); ?></p>
                </div>
                <?php unset($_SESSION['aviso_login']); ?>
            <?php endif; ?>

            <?php if (!empty($mensagem) && $mensagem['tipo'] === 'erro'): ?>
                <div class="alert alert-error">
                    <p><?php echo htmlspecialchars($mensagem['texto']); ?></p>
                </div>
            <?php endif; ?>

            <form class="space-y-6" method="POST" action="index.php">
                <div class="form-group">
                    <label for="email" class="form-label">
                        E-mail
                    </label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           value="<?php echo htmlspecialchars($email); ?>"
                           class="form-input"
                           placeholder="email@exemplo.com">
                </div>

                <div class="form-group">
                    <label for="senha" class="form-label">
                        Senha
                    </label>
                    <input id="senha" name="senha" type="password" autocomplete="current-password" required
                           class="form-input"
                           placeholder="Digite sua senha">
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">
                        Entrar
                    </button>
                </div>
            </form>
            
            <div class="register-link">
                <p>
                    Ainda não tem conta?
                    <a href="auth/cadastro.php">
                        Cadastrar novo usuário
                    </a>
                </p>
            </div>

        <?php endif; ?>

    </div>
    </div>

</body>
</html>
