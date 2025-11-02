<?php
use Dotenv\Dotenv;

// Inicializa variáveis que vão armazenar a resposta da IA e a pergunta do usuário.
//elas precisam ser inicializadas fora do bloco POST para que possam ser usadas na página mesmo antes do envio.
$textoIA = '';
$pergunta = '';

// Checa se o formulário foi enviado via POST e se há uma pergunta.
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["pergunta"])) {

    // Recebe a pergunta enviada pelo usuário
    $pergunta = $_POST["pergunta"];

    // Inclui autoload do Composer e a conexão com o banco de dados
    // IMPORTANTE: o db.php já carrega o .env, não recarregamos aqui
    require_once __DIR__ . '/vendor/autoload.php';
    include_once 'includes/db.php';

    // Chave da API Gemini carregada do arquivo .env
    $apiKey = $_ENV["API_KEY"];

    // URL da API Gemini 2.5 Pro com chave embutida na query string
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent?key=$apiKey";

    // Monta os dados da requisição para a API
    // A API espera um array "contents" com "parts", cada part é um bloco de texto
    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $pergunta]
                ]
            ]
        ]
    ];

    // Inicializa cURL para fazer a requisição HTTP
    curl_setopt_array($ch = curl_init($url), [
        CURLOPT_RETURNTRANSFER => true,            // Retorna a resposta como string
        CURLOPT_HTTPHEADER     => ["Content-Type: application/json"], // Cabeçalho JSON
        CURLOPT_POST           => true,            // Método POST
        CURLOPT_POSTFIELDS     => json_encode($data) // Converte dados para JSON
    ]);

    // Executa a requisição
    $resposta = curl_exec($ch);

    // Checa se houve erro no cURL
    if (curl_errno($ch)) {
        $textoIA = 'Erro no cURL: ' . curl_error($ch);
    } else {
        // Decodifica a resposta JSON da API
        $resultado = json_decode($resposta, true);

        // Checa se houve erro na decodificação do JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            // IMPORTANTE: salvar a resposta crua ajuda a diagnosticar problemas de API ou formatação
            $textoIA = "Erro ao decodificar JSON: " . json_last_error_msg() . "\nResposta crua: $resposta";
        } else {
            // Checa se a API retornou erro
            if (isset($resultado['error'])) {
                $textoIA = "Erro da API: " . json_encode($resultado['error'], JSON_PRETTY_PRINT);
            }
            // Verifica se a resposta esperada existe no JSON
            elseif (isset($resultado['candidates'][0]['content']['parts'][0]['text'])) {
                $textoIA = $resultado['candidates'][0]['content']['parts'][0]['text'];

                // ========================
                // BLOCO DE CONVERSÃO DE MARKDOWN PARA HTML
                // ========================
                $textoIA = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $textoIA);
                $textoIA = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $textoIA);
                $textoIA = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $textoIA);
                $textoIA = preg_replace('/^- (.+)$/m', '<li>$1</li>', $textoIA);

                // Se houver listas, adiciona <ul> ao redor
                if (strpos($textoIA, '<li>') !== false) {
                    $textoIA = "<ul>$textoIA</ul>";
                }

                // Agrupa blocos de texto separados por linhas duplas em <p>
                $textoIA = "<div class='resposta-ia'>" .
                    "<p>" . preg_replace("/\n{2,}/", "</p><p>", $textoIA) . "</p>" .
                    "</div>";
            } else {
                // Caso a resposta da API não esteja no formato esperado
                $textoIA = "Resposta inesperada da API:\n" . json_encode($resultado, JSON_PRETTY_PRINT);
            }
        }
    }

    // Fecha a conexão cURL
    curl_close($ch);

    // ========================
    // BLOCO DE SALVAMENTO NO BANCO DE DADOS
    // ========================
    if (!empty($textoIA)) {
        if ($conn && $conn->connect_errno === 0) {
            // Cria o statement preparado para evitar SQL Injection
            $stmt = $conn->prepare("INSERT INTO historico (pergunta, resposta) VALUES (?, ?)");
            if ($stmt) {
                // Vincula parâmetros
                $stmt->bind_param("ss", $pergunta, $textoIA);
                // Executa e fecha statement
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}
?>
