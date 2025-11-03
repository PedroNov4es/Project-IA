<?php
// Inclui o arquivo ia.php que processa a pergunta enviada pelo usuário
// IMPORTANTE: ao incluir aqui, temos acesso às variáveis $textoIA (resposta da IA) e $pergunta (pergunta enviada)
include 'ia.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Q&A Bot</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="app-container">
    <header>
      <h1>Q&A Bot - Gemini</h1>
      <p>Pergunte o que quiser! A IA responderá logo abaixo </p>
    </header>

    <main>
      <!-- Formulário que envia a pergunta do usuário via POST -->
      <form action="" method="POST" class="formulario">
        <label for="pergunta">Digite sua dúvida:</label>
        <textarea 
          name="pergunta" 
          id="pergunta" 
          rows="5" 
          placeholder="Ex: Explique o que é inteligência artificial..."
          required
        ><?= htmlspecialchars($pergunta) // Evita problemas de segurança como XSS, exibindo a pergunta anterior se houver ?></textarea>

        <button type="submit">Enviar</button>
      </form>

      <!-- Mostra a resposta da IA somente se houver conteúdo -->
      <?php if (!empty($textoIA)): ?>
      <div class="resposta-container">
        <h2>Resposta da IA:</h2>
        <?= $textoIA // Aqui já vem formatada em HTML pelo ia.php, incluindo Markdown convertido ?>
      </div>
      <?php endif; ?>
    </main>

    <footer>
      <p>Desenvolvido por BitMasters © 2025</p>
    </footer>
  </div>
</body>
</html>
