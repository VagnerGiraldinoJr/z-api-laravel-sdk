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

### 1. Remarketing com Botões (Interatividade) - Usando DTOs ✨

Ideal para recuperação de carrinho, promoções e retenção. Agora com **DTOs validados**!

```php
use SuaEmpresa\ZApi\Facades\ZApi;
use SuaEmpresa\ZApi\DTOs\Button;

// Enviando para um cliente específico (Multi-tenancy) usando DTOs
ZApi::using($tenant->instance, $tenant->token, $tenant->cToken)
    ->sendButtons('5511999999999', 'Olá! Vimos que você esqueceu itens no carrinho. Temos um cupom de 10%!', [
        Button::url('cupom-10', 'Resgatar Desconto', 'https://loja.com/checkout'),
        Button::call('ajuda-vendedor', 'Falar com Atendente', '551133334444'),
    ]);

```

**Método alternativo com arrays (mantém compatibilidade):**

```php
// Ainda funciona com arrays simples para compatibilidade
ZApi::using($tenant->instance, $tenant->token, $tenant->cToken)
    ->sendButtons('5511999999999', 'Mensagem', [
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

## 🎯 Button DTO

O SDK utiliza **DTOs (Data Transfer Objects)** para garantir que os botões sejam validados antes de serem enviados.

### Tipos de Botões

#### Botão de URL
```php
use SuaEmpresa\ZApi\DTOs\Button;

$button = Button::url(
    id: 'btn-oferta',
    label: 'Ver Oferta',
    url: 'https://example.com/offer'
);
```

#### Botão de Chamada
```php
$button = Button::call(
    id: 'btn-ligar',
    label: 'Ligar Agora',
    phone: '551133334444'
);
```

### Validações Automáticas

O Button DTO valida automaticamente:
- ✓ Tipo de botão (URL ou CALL)
- ✓ Presença de URL para botões tipo URL
- ✓ Presença de telefone para botões tipo CALL
- ✓ Campos obrigatórios (id, type, label)

Se alguma validação falhar, uma `InvalidArgumentException` será lançada.

---

## 🧪 Testes

Este pacote inclui uma suite completa de testes usando **Pest PHP**.

### Executando os Testes

```bash
# Todos os testes
./vendor/bin/pest

# Com relatório detalhado
./vendor/bin/pest --verbose

# Apenas testes unitários
./vendor/bin/pest tests/Unit
```

### Cobertura de Testes

Os testes cobrem:
- ✓ Validação do Button DTO (tipos, campos obrigatórios)
- ✓ Factory methods (Button::url(), Button::call())
- ✓ Envio correto de JSON para Z-API com DTOs
- ✓ Backward compatibility com arrays
- ✓ Validação de headers (Client-Token)
- ✓ Tratamento de resposta de sucesso (200)
- ✓ Tratamento de erros HTTP (404, 500)
- ✓ Estrutura correta de botões (URL e CALL)
- ✓ Configuração dinâmica de instância/token
- ✓ Cenários de migração (mix de DTOs e arrays)

Para mais detalhes, consulte [tests/README.md](tests/README.md).

---

## 📄 Licença

Este SDK foi desenvolvido para uso interno e por parceiros. Todos os direitos reservados.

---

### Dica de ouro:

Para testar os webhooks localmente, use o **Expose** ou **Ngrok** para criar um túnel para o seu domínio local e coloque a URL no painel da Z-API!
