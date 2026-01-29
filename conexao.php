<?php
// Configurações do seu banco local
$host = 'localhost';
$db   = 'farmacia_db';
$user = 'postgres'; // Usuário padrão do Postgres
$pass = '1234'; // Coloque a senha que você definiu na instalação

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conectado com sucesso!"; 
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}