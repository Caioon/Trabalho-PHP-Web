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
    elseif (isset($_POST['mockar_dados'])) {
        header("Location: db/mockar_dados.php");
        exit;
    }
    elseif (isset($_POST['limpar_produtos'])) {
        header("Location: db/limpar_produtos.php");
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

            <?php if (isset($_SESSION['mensagem_mock'])): ?>
                <div class="alert alert-<?php echo $_SESSION['mensagem_mock']['tipo']; ?>">
                    <p><?php echo htmlspecialchars($_SESSION['mensagem_mock']['texto']); ?></p>
                </div>
                <?php unset($_SESSION['mensagem_mock']); ?>
            <?php endif; ?>

            <div class="btn-group">
                <a href="produtos/listar.php" class="btn btn-access">
                    Acessar o Sistema
                </a>

                <div class="btn-mock-group" />
                    <form method="POST" action="index.php" onsubmit="return confirm('⚠️ Tem certeza que deseja criar dados de teste? Veja mais informações clicando no botão ao lado do botão de mockar dados');" style="flex: 1;">
                        <input type="hidden" name="mockar_dados" value="1">
                        <button type="submit" class="btn btn-mock">
                            🎲 Mockar Dados para Testar
                        </button>
                    </form>

                    <a href="#modal-info" class="btn btn-mock btn-info" title="Informações sobre o mock" style="display: flex; align-items: center; justify-content: center;">
                        ℹ️
                    </a>
                </div>
                  

                <form method="POST" action="index.php" onsubmit="return confirm('⚠️ ATENÇÃO! Tem certeza que deseja deletar TODOS os produtos do banco de dados? Esta ação não pode ser desfeita!');">
                    <input type="hidden" name="limpar_produtos" value="1">
                    <button type="submit" class="btn btn-danger">
                        🗑️ Apagar Todos os Produtos
                    </button>
                </form>

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

    <div id="modal-info" class="modal">
        <div class="modal-content">
            <h2>ℹ️ Informações do Mock</h2>
            <p>O script incluirá 3 novos usuários com 2 produtos cada:</p>
            <ul>
                <li><strong>caio@email.com</strong> — senha <code>123</code></li>
                <li><strong>eduardo@email.com</strong> — senha <code>123</code></li>
                <li><strong>felipe@email.com</strong> — senha <code>123</code></li>
            </ul>

            <a href="#" class="btn btn-secondary modal-close">Voltar</a>
        </div>
    </div>
    

</body>
</html>
