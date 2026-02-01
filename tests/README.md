# Z-API Laravel SDK - Test Suite

Este diretório contém a suite de testes completa para o pacote Z-API Laravel SDK, utilizando **Pest PHP** como framework de testes.

## 🧪 Estrutura de Testes

### Testes Unitários

#### `ButtonTest.php`

Testes para o **Button DTO** com validações:

1. **Factory Methods**
   - Testa `Button::url()` para criar botões de URL
   - Testa `Button::call()` para criar botões de chamada

2. **Conversão para Array**
   - Verifica serialização correta de botões URL
   - Verifica serialização correta de botões CALL

3. **Validações**
   - Rejeita tipos de botão inválidos
   - Rejeita botão URL sem parâmetro `url`
   - Rejeita botão CALL sem parâmetro `phone`

4. **Constructor Direto**
   - Permite criação via construtor para casos avançados

#### `ZClientTest.php`

Testes abrangentes para o método `sendButtons` da classe `ZClient`:

1. **Teste de Payload JSON Correto com DTOs**
   - Verifica se o método envia o JSON correto usando Button DTOs
   - Valida headers (Client-Token)
   - Confirma que phone, message e buttonActions estão corretos

2. **Backward Compatibility**
   - Garante que arrays simples ainda funcionam
   - Permite migração gradual para DTOs

3. **Teste de Resposta de Sucesso (200)**
   - Simula uma resposta bem-sucedida da API
   - Verifica status 200 e dados de resposta

4. **Teste de Erro 404**
   - Simula erro de "Instance not found"
   - Confirma que o service detecta corretamente o erro 404
   - Valida as propriedades `failed()` e `clientError()`

5. **Teste de Erro 500**
   - Simula erro interno do servidor
   - Verifica que o service identifica erro 500
   - Valida as propriedades `failed()` e `serverError()`

6. **Teste de URL Dinâmica**
   - Confirma que instância e token são corretamente inseridos na URL
   - Verifica Client-Token customizado

7. **Teste de Botão tipo URL com DTO**
   - Valida estrutura correta para botões de URL usando DTOs
   - Confirma id, type, url e label

8. **Teste de Botão tipo CALL com DTO**
   - Valida estrutura correta para botões de chamada usando DTOs
   - Confirma id, type, phone e label

9. **Teste de Migração**
   - Permite misturar DTOs e arrays no mesmo envio
   - Facilita migração gradual

## 🚀 Executando os Testes

### Todos os testes
```bash
./vendor/bin/pest
```

### Apenas testes unitários
```bash
./vendor/bin/pest tests/Unit
```

### Teste específico
```bash
./vendor/bin/pest tests/Unit/ZClientTest.php
```

### Com cobertura de código
```bash
./vendor/bin/pest --coverage
```

## 📦 Dependências de Teste

- **Pest PHP** (^4.3): Framework de testes moderno para PHP
- **Orchestra Testbench** (^9.16): Ferramenta para testar pacotes Laravel
- **Laravel Http Fake**: Simulação de requisições HTTP

## 🎯 Mocking da API

Todos os testes utilizam o `Http::fake()` do Laravel para simular as respostas da Z-API, garantindo:
- Testes rápidos e confiáveis
- Sem dependência de serviços externos
- Controle total sobre respostas (sucesso e erro)

## 📝 Padrões de Teste

### Estrutura AAA (Arrange-Act-Assert)

Cada teste segue o padrão:
1. **Arrange**: Configuração do mock e dados
2. **Act**: Execução do método testado
3. **Assert**: Verificação dos resultados

### Exemplo de Teste com Button DTO

```php
it('sends buttons with correct JSON payload to Z-API using Button DTOs', function () {
    // Arrange
    Http::fake([
        '*' => Http::response(['success' => true, 'messageId' => 'ABC123'], 200)
    ]);

    $buttons = [
        Button::url('btn-1', 'Ver Oferta', 'https://example.com/offer'),
        Button::call('btn-2', 'Ligar', '551133334444'),
    ];

    // Act
    $response = $this->client->sendButtons('5511999999999', 'Olá!', $buttons);

    // Assert
    Http::assertSent(function ($request) {
        $buttonActions = $request['buttonActions'];
        return $buttonActions[0]['type'] === 'URL'
            && $buttonActions[1]['type'] === 'CALL';
    });
    
    expect($response->status())->toBe(200);
});
```

### Exemplo de Teste com Arrays (Backward Compatibility)

```php
it('handles 404 error response from API', function () {
    // Arrange
    Http::fake([
        '*' => Http::response([
            'error' => true,
            'message' => 'Instance not found'
        ], 404)
    ]);

    // Act
    $response = $this->client->sendButtons(
        '5511988887777',
        'Mensagem de teste',
        [['id' => 'test', 'type' => 'URL', 'url' => 'https://test.com', 'label' => 'Teste']]
    );

    // Assert
    expect($response->status())->toBe(404)
        ->and($response->failed())->toBeTrue()
        ->and($response->clientError())->toBeTrue();
});
```

## ✅ Cobertura de Testes

Os testes cobrem:
- ✓ **Button DTO**: Validação, factory methods, serialização
- ✓ Envio correto de JSON para Z-API com DTOs
- ✓ Backward compatibility com arrays
- ✓ Headers corretos (Client-Token)
- ✓ Tratamento de resposta de sucesso
- ✓ Tratamento de erro 404
- ✓ Tratamento de erro 500
- ✓ Validação de estrutura de botões URL
- ✓ Validação de estrutura de botões CALL
- ✓ Configuração dinâmica de instância/token

## 🔍 Assertivas Utilizadas

- `expect()->toBe()`: Igualdade exata
- `expect()->toBeTrue()`: Valor verdadeiro
- `expect()->toHaveKey()`: Presença de chave em array
- `Http::assertSent()`: Verificação de requisição HTTP enviada

## 🛠️ Manutenção

Ao adicionar novos métodos ao `ZClient`, lembre-se de:
1. Criar testes correspondentes
2. Mockar as respostas da API
3. Testar tanto sucesso quanto falhas
4. Seguir o padrão AAA
5. Usar expectativas Pest expressivas
