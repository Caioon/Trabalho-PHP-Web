<?php
require_once '../auth/verifica_login.php';
require_once '../db/conexao.php';

try {
    $pdo->beginTransaction();

    $usuariosMock = [
        [
            'nome' => 'Caio',
            'email' => 'caio@email.com',
            'senha' => password_hash('123', PASSWORD_DEFAULT)
        ],
        [
            'nome' => 'Eduardo',
            'email' => 'eduardo@email.com',
            'senha' => password_hash('123', PASSWORD_DEFAULT)
        ],
        [
            'nome' => 'Felipe',
            'email' => 'felipe@email.com',
            'senha' => password_hash('123', PASSWORD_DEFAULT)
        ]
    ];

    $usuariosIds = [];

    foreach ($usuariosMock as $usuario) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$usuario['email']]);
        $existe = $stmt->fetch();

        if ($existe) {
            $usuariosIds[] = $existe['id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->execute([$usuario['nome'], $usuario['email'], $usuario['senha']]);
            $usuariosIds[] = $pdo->lastInsertId();
        }
    }

    $produtosMock = [
        ['nome' => 'Notebook Dell Inspiron 15', 'preco' => 3499.90, 'descricao' => 'Intel Core i5, 8GB RAM, SSD 256GB, Tela 15.6"', 'privado' => 0, 'usuario_id' => $usuariosIds[0]],
        ['nome' => 'Mouse Logitech MX Master 3', 'preco' => 549.90, 'descricao' => 'Mouse sem fio, ergonômico, 7 botões programáveis', 'privado' => 1, 'usuario_id' => $usuariosIds[0]],
        
        ['nome' => 'Teclado Mecânico Keychron K2', 'preco' => 699.00, 'descricao' => 'Switches Blue, RGB, Layout 75%, Bluetooth', 'privado' => 0, 'usuario_id' => $usuariosIds[1]],
        ['nome' => 'Monitor LG 27" 4K', 'preco' => 1899.00, 'descricao' => 'IPS, HDR10, 60Hz, USB-C', 'privado' => 1, 'usuario_id' => $usuariosIds[1]],
        
        ['nome' => 'Cadeira Gamer DT3 Sports', 'preco' => 1299.00, 'descricao' => 'Reclinável até 180°, suporte lombar, braços 4D', 'privado' => 0, 'usuario_id' => $usuariosIds[2]],
        ['nome' => 'Webcam Logitech C920', 'preco' => 459.90, 'descricao' => 'Full HD 1080p, 30fps, microfone estéreo', 'privado' => 1, 'usuario_id' => $usuariosIds[2]],
    ];

    $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, descricao, privado, usuario_id) VALUES (?, ?, ?, ?, ?)");

    $produtosCriados = 0;
    foreach ($produtosMock as $produto) {
        $stmt->execute([
            $produto['nome'],
            $produto['preco'],
            $produto['descricao'],
            $produto['privado'],
            $produto['usuario_id']
        ]);
        $produtosCriados++;
    }

    $pdo->commit();

    $_SESSION['mensagem_mock'] = [
        'tipo' => 'success',
        'texto' => "✅ Dados mockados com sucesso! Criados 3 usuários e " . $produtosCriados . " produtos. Senhas: 123"
    ];

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log("Erro ao mockar dados: " . $e->getMessage());
    
    $_SESSION['mensagem_mock'] = [
        'tipo' => 'error',
        'texto' => "❌ Erro ao criar dados mockados: " . $e->getMessage()
    ];
}

header("Location: ../index.php");
exit;
?>
