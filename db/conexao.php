<?php
require_once __DIR__ . '/config.php';

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            $tempPdo = new PDO('mysql:host=' . DB_HOST, DB_USER, DB_PASS);
            $tempPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` 
                            CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

            $this->pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->createTables();
        } catch (PDOException $e) {
            error_log("Erro na conexão: " . $e->getMessage());
            die("Erro ao conectar com o banco de dados.");
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function createTables()
    {
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) FROM information_schema.TABLES 
             WHERE TABLE_SCHEMA = '" . DB_NAME . "' 
             AND TABLE_NAME = 'users'"
        );
        $usersExists = $stmt->fetchColumn() > 0;

        if (!$usersExists) {
            $sqlUsers = "CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                senha VARCHAR(255) NOT NULL
            )";
            $this->pdo->exec($sqlUsers);
        }

        $stmt = $this->pdo->query(
            "SELECT COUNT(*) FROM information_schema.TABLES 
             WHERE TABLE_SCHEMA = '" . DB_NAME . "' 
             AND TABLE_NAME = 'produtos'"
        );
        $produtosExists = $stmt->fetchColumn() > 0;

        if (!$produtosExists) {
            $sqlProdutos = "CREATE TABLE produtos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(255) NOT NULL,
                preco DECIMAL(10, 2) NOT NULL,
                descricao TEXT,
                privado TINYINT(1) DEFAULT 0,
                usuario_id INT NOT NULL,
                FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE
            )";
            $this->pdo->exec($sqlProdutos);
        }
    }
}

$db = Database::getInstance();

$pdo = $db->getConnection();
