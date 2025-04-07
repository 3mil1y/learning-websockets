# Chat WebSocket em PHP

Este é um simples aplicativo de chat usando WebSockets em PHP, utilizando a biblioteca Ratchet.

## Requisitos

- PHP 7.4 ou superior
- Composer
- Extensão PHP Sockets

## Instalação

1. Clone este repositório
2. Execute o comando para instalar as dependências:
   ```bash
   composer install
   ```

## Executando o Servidor

1. Inicie o servidor WebSocket:
   ```bash
   php server.php
   ```

2. Abra o navegador e acesse:
   ```
   http://localhost/public/index.html
   ```

3. Abra a mesma URL em outra aba ou navegador para testar o chat entre múltiplos usuários.

## Funcionalidades

- Chat em tempo real usando WebSockets
- Interface simples e responsiva
- Suporte a múltiplos usuários
- Envio de mensagens com Enter ou botão
- Histórico de mensagens visível para todos os usuários conectados

## Notas

- O servidor WebSocket roda na porta 8080
- Certifique-se de que a porta 8080 esteja disponível
- Para uso em produção, considere adicionar autenticação e outras medidas de segurança 