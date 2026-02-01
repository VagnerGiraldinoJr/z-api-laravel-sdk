# 🚀 Z-API Laravel SDK (Bridge)

O **Z-API Laravel SDK** é um "plugin" pronto para uso que simplifica a integração com a [Z-API](https://developer.z-api.io/) em ecossistemas Laravel, com suporte nativo a **Multi-tenancy** e foco total em estratégias de **remarketing interativo**.

### O que este SDK resolve:

* **Instalação Plug-and-Play**: Instale e saia enviando em menos de 2 minutos.
* **Abstração de Botões**: Enviar botões de ação (URL/Chamada) agora é uma simples chamada de método.
* **Webhook Handler Automático**: Rota de webhook pré-configurada que dispara eventos nativos do Laravel.
* **Multi-tenant Ready**: Alterne entre instâncias de clientes diferentes dinamicamente.

---

## 🛠️ Instalação

Como este é um pacote privado/profissional, adicione o repositório ao seu `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/seu-usuario/z-api-laravel-sdk"
    }
],

```

Depois, execute:

```bash
composer require sua-empresa/z-api-laravel-sdk

```

---

## 🧙 Wizard de Configuração

Após instalar, rode o comando para publicar as configurações e ativar o SDK:

```bash
php artisan zapi:install

```

No seu `.env`, configure as credenciais padrão (se necessário):

```env
ZAPI_CLIENT_TOKEN=seu_token
ZAPI_INSTANCE_ID=sua_instancia
ZAPI_INSTANCE_TOKEN=seu_token_instancia

```

---

## 📱 Como Usar

### 1. Remarketing com Botões (Interatividade)

Ideal para recuperação de carrinho, promoções e retenção.

```php
use SuaEmpresa\ZApi\Facades\ZApi;

// Enviando para um cliente específico (Multi-tenancy)
ZApi::using($tenant->instance, $tenant->token, $tenant->cToken)
    ->sendButtons('5511999999999', 'Olá! Vimos que você esqueceu itens no carrinho. Temos um cupom de 10%!', [
        [
            "id" => "cupom-10",
            "type" => "URL",
            "url" => "https://loja.com/checkout",
            "label" => "Resgatar Desconto"
        ],
        [
            "id" => "ajuda-vendedor",
            "type" => "CALL",
            "phone" => "551133334444",
            "label" => "Falar com Atendente"
        ]
    ]);

```

### 2. Tratando Respostas (Webhooks)

O SDK registra automaticamente a rota `POST: /zapi/webhook`. Basta configurá-la no painel da Z-API. Para agir quando o cliente clica em um botão, crie um **Listener**:

```php
// app/Listeners/ProcessZApiInteraction.php

public function handle(ZApiMessageReceived $event)
{
    $payload = $event->payload;

    if ($payload['type'] == 'ButtonAction') {
        $buttonId = $payload['buttonId'];
        // Lógica de negócio aqui (ex: marcar lead no banco)
    }
}

```

---

## 📄 Licença

Este SDK foi desenvolvido para uso interno e por parceiros. Todos os direitos reservados.

---

### Dica de ouro:

Para testar os webhooks localmente, use o **Expose** ou **Ngrok** para criar um túnel para o seu domínio local e coloque a URL no painel da Z-API!
