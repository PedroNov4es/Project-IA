<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$apiKey = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pergunta = $_POST["pergunta"];

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateText?key=$apiKey";

    $data = [
        "prompt" => [
            "text" => $pergunta
        ],
        "temperature" => 0.7,
        "maxOutputTokens" => 256,
        "candidateCount" => 1
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $resposta = curl_exec($ch);

    if(curl_errno($ch)) {
        echo 'Erro no cURL: ' . curl_error($ch);
        curl_close($ch);
        exit;
    }

    curl_close($ch);

    $resultado = json_decode($resposta, true);

    echo "<h2>JSON retornado pelo Gemini:</h2>";
    echo "<pre>";
    print_r($resultado);
    echo "</pre>";

    $textoIA = $resultado["candidates"][0]["content"][0]["text"] ?? "Erro ao obter resposta.";

    echo "<h2>Pergunta:</h2><p>$pergunta</p>";
    echo "<h2>Resposta do Gemini:</h2><p>$textoIA</p>";
    echo '<br><a href="index.php">Voltar</a>';
}
?>
