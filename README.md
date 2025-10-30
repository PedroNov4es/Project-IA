# 🎓 AcadIA - Chat Acadêmico

## 📝 Descrição
AcadIA é uma aplicação web que permite aos usuários enviar perguntas e receber respostas automáticas de uma IA (Gemini).  
Todas as perguntas e respostas são armazenadas em um **banco de dados MySQL**, permitindo consultar o histórico e acompanhar interações passadas.

O projeto foi desenvolvido em **PHP**, utilizando **Composer** para gerenciamento de dependências, seguindo boas práticas de modularização e segurança (variáveis sensíveis em `.env`).

---

## 🛠 Tecnologias
- **PHP 7.2+**
- **MySQL**
- **Composer**
- **HTML & CSS**
- **cURL**
- Biblioteca: `vlucas/phpdotenv` para gerenciamento de variáveis de ambiente

---

## 📂 Estrutura do Projeto
/assets
style.css # Estilos do front-end
/includes
db.php # Conexão com banco
/tests
teste_db.php # Teste de conexão
/vendor # Dependências do Composer
.gitignore # Arquivos ignorados pelo Git
ia.php # Script principal da IA
index.php # Interface do usuário
README.md # Documentação

---

## ⚙️ Instalação / Configuração

1. Clone o repositório:  
``bash
git clone <URL_DO_REPOSITORIO>
Instale dependências via Composer:

bash
Copiar código
composer install
Crie o arquivo .env na raiz com as variáveis:

ini
Copiar código
apiKey=SUA_CHAVE_DO_GEMINI
servername=SEU_SERVIDOR
username=SEU_USUARIO
password=SUA_SENHA
dbname=SEU_BANCO
🔒 O .env não é versionado por questões de segurança (.gitignore).

🚀 Como Usar
Abra index.php no navegador.

Digite sua pergunta no formulário.

Clique em Enviar.

A pergunta será processada pela IA e a resposta exibida na tela.

Histórico é salvo na tabela historico do banco de dados.

✅ Testes
Verifique a conexão com o banco:

bash
Copiar código
php tests/teste_db.php
Teste o envio de perguntas via formulário e confira o armazenamento no banco.

## ⚙️ Observações Técnicas
Requisições à API Gemini são feitas via cURL com JSON.

Erros são exibidos para depuração.

Perguntas e respostas são salvas usando prepared statements, evitando SQL Injection.

Estrutura modular facilita manutenção e expansão do projeto.

## 🔄 Fluxo do Sistema
mermaid
Copiar código
flowchart LR
A[Usuário] --> B[index.php]
B --> C[ia.php]
C --> D[API Gemini]
D --> C
C --> E[Banco de Dados]
C --> A
Descrição do fluxo:

Usuário envia pergunta → index.php

Pergunta é processada em ia.php

ia.php envia requisição à API Gemini usando chave do .env

Resposta da IA é recebida e salva no banco (historico)

Resposta é exibida ao usuário

