<?php

$host = 'localhost';
$dbname = 'sistema_php';
$usuario = 'root';     
$senha = '123456';           

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexão bem-sucedida!"; 
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

