<?php
require 'includes/conexao.php'; // Arquivo com sua conexão PDO

try {
    $login = 'novousuario';
    $senha = password_hash('senha_segura', PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("INSERT INTO usuarios (login, senha) VALUES (?, ?)");
    $stmt->execute([$login, $senha]);
    
    echo "Usuário cadastrado com sucesso!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}