<?php

$host = 'localhost';
$dbname = 'sistema_php';
$usuario = 'root';     
$senha = '123456';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erro na conexão: " . $e->getMessage());
    die("Erro ao conectar com o banco de dados.");
}
