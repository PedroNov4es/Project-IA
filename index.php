<!--Página inicial onde o usuário escreve sua dúvida e a envia para a inteligência artificial(GEMINI).-->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Q&A Bot</title>
  <link rel="stylesheet" href="assets/style.css">

</head>
<body>
  <h1>Q&A Bot</h1>
  <form action="ia.php" method="POST">
    <label for="pergunta">Digite sua dúvida:</label>
    <textarea name="pergunta" id="pergunta" rows="5" required></textarea>
    <button type="submit">Enviar</button>
  </form>
</body>
</html>
