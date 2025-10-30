<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Carrega o .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Conecta ao banco usando as variáveis do .env
$conn = new mysqli(
    $_ENV['servername'],
    $_ENV['username'],
    $_ENV['password'],
    $_ENV['dbname']
);

// Verifica conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}
?>
