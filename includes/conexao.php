<?php
$host = 'localhost';
$dbname = 'projettaexpress'; // Nome do banco esperado
$user = 'admin';          // Usuário padrão do XAMPP
$pass = 'Sam@88860374';              // Senha vazia (comum em XAMPP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}
?>