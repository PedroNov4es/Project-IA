<?php
//Parte principal do projeto, processa perguntas do usuário, envia para a API, armazena histórico e exibe respostas.


// Carrega as dependências instaladas pelo Composer
require __DIR__ . '/vendor/autoload.php';

// Conecta ao banco de dados
include 'includes/db.php';

// Mostra todos os erros (útil para depuração)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carrega variáveis do arquivo .env (como a chave da API)
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtém a chave da API do arquivo .env
$apiKey = $_ENV["apiKey"];

// Executa o código apenas se o formulário for enviado (método POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Pega a pergunta enviada pelo usuário
    $pergunta = $_POST["pergunta"];

    // URL da API do Gemini com a chave de acesso
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent?key=$apiKey";

    // Monta o corpo da requisição (conteúdo a ser enviado à IA)
    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $pergunta]
                ]
            ]
        ]
    ];
    
    // Inicia o cURL para fazer a requisição HTTP
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta como string
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]); // Envia como JSON
    curl_setopt($ch, CURLOPT_POST, true); // Define o método como POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Adiciona o corpo da requisição

    // Executa a requisição e guarda a resposta
    $resposta = curl_exec($ch);

    // Verifica se ocorreu erro na requisição
    if (curl_errno($ch)) {
        echo 'Erro no cURL: ' . curl_error($ch);
        curl_close($ch);
        exit;
    }

    curl_close($ch); // Encerra a sessão cURL

    // Converte a resposta JSON em array PHP
    $resultado = json_decode($resposta, true);

    // Pega o texto gerado pela IA ou mostra mensagem de erro
    $textoIA = $resultado["candidates"][0]["content"]["parts"][0]["text"] 
           ?? "Erro ao obter resposta.";

    // Insere a pergunta e resposta no banco de dados
    $stmt = $conn->prepare("INSERT INTO historico(pergunta, resposta) VALUES (?, ?)");
    $stmt->bind_param("ss", $pergunta, $textoIA);
    $stmt->execute();
    $stmt->close();

    // Exibe a pergunta e a resposta na tela
    echo "<h2>Pergunta:</h2><p>$pergunta</p>";
    echo "<h2>Resposta do Gemini:</h2><p>$textoIA</p>";
    echo '<br><a href="index.php">Voltar</a>';
}
?>
