<?php

include 'includes/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$apiKey = "AIzaSyB4bfh8FbUGIcUmBc0L3sZAkDagpl_z32o";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pergunta = $_POST["pergunta"];

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent?key=$apiKey";
;

    $data = [
        "contents" => [
            [
                "parts" =>[
                    ["text" => $pergunta]
                ]
            ]
        ]
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

   $textoIA = $resultado["candidates"][0]["content"]["parts"][0]["text"] 
           ?? "Erro ao obter resposta.";


    $stmt = $conn -> prepare("INSERT INTO historico(pergunta,resposta) VALUES (?, ?)");
    $stmt -> bind_param("ss", $pergunta, $textoIA);
    $stmt->execute();
    $stmt -> close();


    echo "<h2>Pergunta:</h2><p>$pergunta</p>";
    echo "<h2>Resposta do Gemini:</h2><p>$textoIA</p>";
    echo '<br><a href="index.php">Voltar</a>';
}


?>
