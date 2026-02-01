<?php

use SuaEmpresa\ZApi\Services\ZClient;
use SuaEmpresa\ZApi\DTOs\Button;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->client = new ZClient();
    $this->client->using('test-instance', 'test-token', 'test-client-token');
});

it('sends buttons with correct JSON payload to Z-API using Button DTOs', function () {
    // Arrange
    Http::fake([
        'https://api.z-api.io/instances/test-instance/token/test-token/send-button-actions' => Http::response([
            'success' => true,
            'messageId' => 'ABC123'
        ], 200)
    ]);

    $phone = '5511999999999';
    $text = 'Olá! Temos uma oferta especial para você!';
    $buttons = [
        Button::url('btn-1', 'Ver Oferta', 'https://example.com/offer'),
        Button::call('btn-2', 'Ligar', '551133334444'),
    ];

    // Act
    $response = $this->client->sendButtons($phone, $text, $buttons);

    // Assert - Verifica se a requisição foi feita com os dados corretos
    Http::assertSent(function ($request) use ($phone, $text) {
        $buttonActions = $request['buttonActions'];
        return $request->url() === 'https://api.z-api.io/instances/test-instance/token/test-token/send-button-actions'
            && $request->hasHeader('Client-Token', 'test-client-token')
            && $request['phone'] === $phone
            && $request['message'] === $text
            && $buttonActions[0]['id'] === 'btn-1'
            && $buttonActions[0]['type'] === 'URL'
            && $buttonActions[0]['url'] === 'https://example.com/offer'
            && $buttonActions[1]['id'] === 'btn-2'
            && $buttonActions[1]['type'] === 'CALL'
            && $buttonActions[1]['phone'] === '551133334444';
    });

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKey('success')
        ->and($response->json('success'))->toBeTrue();
});

it('maintains backward compatibility with array buttons', function () {
    // Arrange
    Http::fake([
        '*' => Http::response([
            'success' => true,
            'messageId' => 'ABC123'
        ], 200)
    ]);

    $buttons = [
        [
            'id' => 'btn-1',
            'type' => 'URL',
            'url' => 'https://example.com/offer',
            'label' => 'Ver Oferta'
        ],
        [
            'id' => 'btn-2',
            'type' => 'CALL',
            'phone' => '551133334444',
            'label' => 'Ligar'
        ]
    ];

    // Act
    $response = $this->client->sendButtons('5511999999999', 'Test', $buttons);

    // Assert
    Http::assertSent(function ($request) use ($buttons) {
        return $request['buttonActions'] === $buttons;
    });

    expect($response->status())->toBe(200);
});

it('handles successful API response (200) with Button DTOs', function () {
    // Arrange
    Http::fake([
        '*' => Http::response([
            'success' => true,
            'messageId' => 'MSG456',
            'timestamp' => 1234567890
        ], 200)
    ]);

    $buttons = [
        Button::url('test', 'Teste', 'https://test.com')
    ];

    // Act
    $response = $this->client->sendButtons(
        '5511988887777',
        'Mensagem de teste',
        $buttons
    );

    // Assert
    expect($response->successful())->toBeTrue()
        ->and($response->status())->toBe(200)
        ->and($response->json('success'))->toBeTrue()
        ->and($response->json('messageId'))->toBe('MSG456');
});

it('handles 404 error response from API with Button DTOs', function () {
    // Arrange
    Http::fake([
        '*' => Http::response([
            'error' => true,
            'message' => 'Instance not found'
        ], 404)
    ]);

    $buttons = [
        Button::url('test', 'Teste', 'https://test.com')
    ];

    // Act
    $response = $this->client->sendButtons(
        '5511988887777',
        'Mensagem de teste',
        $buttons
    );

    // Assert
    expect($response->status())->toBe(404)
        ->and($response->failed())->toBeTrue()
        ->and($response->clientError())->toBeTrue()
        ->and($response->json('error'))->toBeTrue()
        ->and($response->json('message'))->toBe('Instance not found');
});

it('handles 500 error response from API with Button DTOs', function () {
    // Arrange
    Http::fake([
        '*' => Http::response([
            'error' => true,
            'message' => 'Internal server error'
        ], 500)
    ]);

    $buttons = [
        Button::url('test', 'Teste', 'https://test.com')
    ];

    // Act
    $response = $this->client->sendButtons(
        '5511988887777',
        'Mensagem de teste',
        $buttons
    );

    // Assert
    expect($response->status())->toBe(500)
        ->and($response->failed())->toBeTrue()
        ->and($response->serverError())->toBeTrue()
        ->and($response->json('error'))->toBeTrue()
        ->and($response->json('message'))->toBe('Internal server error');
});

it('sends correct instance and token in URL with Button DTOs', function () {
    // Arrange
    $customInstance = 'custom-instance-123';
    $customToken = 'custom-token-xyz';
    $customClientToken = 'custom-client-token-abc';
    
    $client = new ZClient();
    $client->using($customInstance, $customToken, $customClientToken);

    Http::fake([
        '*' => Http::response(['success' => true], 200)
    ]);

    $buttons = [
        Button::url('test', 'Test', 'https://test.com')
    ];

    // Act
    $client->sendButtons('5511999999999', 'Test', $buttons);

    // Assert
    Http::assertSent(function ($request) use ($customInstance, $customToken, $customClientToken) {
        $expectedUrl = "https://api.z-api.io/instances/{$customInstance}/token/{$customToken}/send-button-actions";
        return $request->url() === $expectedUrl
            && $request->hasHeader('Client-Token', $customClientToken);
    });
});

it('sends correct button structure for URL type using Button DTO', function () {
    // Arrange
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $buttons = [
        Button::url('url-button', 'Finalizar Compra', 'https://loja.com/checkout')
    ];

    // Act
    $this->client->sendButtons('5511999999999', 'Finalize sua compra!', $buttons);

    // Assert
    Http::assertSent(function ($request) {
        $sentButtons = $request['buttonActions'];
        return $sentButtons[0]['id'] === 'url-button'
            && $sentButtons[0]['type'] === 'URL'
            && $sentButtons[0]['url'] === 'https://loja.com/checkout'
            && $sentButtons[0]['label'] === 'Finalizar Compra';
    });
});

it('sends correct button structure for CALL type using Button DTO', function () {
    // Arrange
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $buttons = [
        Button::call('call-button', 'Ligar para SAC', '551133334444')
    ];

    // Act
    $this->client->sendButtons('5511999999999', 'Precisa de ajuda?', $buttons);

    // Assert
    Http::assertSent(function ($request) {
        $sentButtons = $request['buttonActions'];
        return $sentButtons[0]['id'] === 'call-button'
            && $sentButtons[0]['type'] === 'CALL'
            && $sentButtons[0]['phone'] === '551133334444'
            && $sentButtons[0]['label'] === 'Ligar para SAC';
    });
});

it('can mix Button DTOs and arrays (for migration scenarios)', function () {
    // Arrange
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $buttons = [
        Button::url('btn-1', 'DTO Button', 'https://example.com'),
        ['id' => 'btn-2', 'type' => 'CALL', 'phone' => '551199999999', 'label' => 'Array Button']
    ];

    // Act
    $response = $this->client->sendButtons('5511999999999', 'Test', $buttons);

    // Assert
    Http::assertSent(function ($request) {
        $sentButtons = $request['buttonActions'];
        return count($sentButtons) === 2
            && $sentButtons[0]['id'] === 'btn-1'
            && $sentButtons[0]['type'] === 'URL'
            && $sentButtons[1]['id'] === 'btn-2'
            && $sentButtons[1]['type'] === 'CALL';
    });

    expect($response->status())->toBe(200);
});


it('supports method chaining with using()', function () {
    // Arrange
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    // Act - Method chaining
    $result = $this->client->using('chain-instance', 'chain-token', 'chain-client-token');

    // Assert - Should return self for chaining
    expect($result)->toBe($this->client);
});

it('supports method chaining with withDelay()', function () {
    // Arrange
    $client = new ZClient();

    // Act - Method chaining
    $result = $client->withDelay(10);

    // Assert - Should return self for chaining
    expect($result)->toBe($client);
});

it('sends message with delay when withDelay is used', function () {
    // Arrange
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $buttons = [Button::url('btn-1', 'Test', 'https://example.com')];

    // Act
    $this->client->withDelay(15)->sendButtons('5511999999999', 'Test message', $buttons);

    // Assert
    Http::assertSent(function ($request) {
        return isset($request['delayMessage'])
            && $request['delayMessage'] === 15;
    });
});

it('does not include delay parameter when withDelay is not used', function () {
    // Arrange
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $buttons = [Button::url('btn-1', 'Test', 'https://example.com')];

    // Act
    $this->client->sendButtons('5511999999999', 'Test message', $buttons);

    // Assert
    Http::assertSent(function ($request) {
        return !isset($request['delayMessage']);
    });
});

it('supports full method chaining: using -> withDelay -> sendButtons', function () {
    // Arrange
    Http::fake(['*' => Http::response(['success' => true, 'messageId' => 'XYZ789'], 200)]);

    $client = new ZClient();
    $buttons = [
        Button::url('btn-offer', 'Ver Oferta', 'https://example.com'),
        Button::call('btn-call', 'Ligar', '551133334444')
    ];

    // Act - Full chaining
    $response = $client
        ->using('chained-instance', 'chained-token', 'chained-client-token')
        ->withDelay(5)
        ->sendButtons('5511999999999', 'Chained message', $buttons);

    // Assert
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.z-api.io/instances/chained-instance/token/chained-token/send-button-actions'
            && $request->hasHeader('Client-Token', 'chained-client-token')
            && $request['delayMessage'] === 5
            && $request['phone'] === '5511999999999'
            && $request['message'] === 'Chained message'
            && count($request['buttonActions']) === 2;
    });

    expect($response->status())->toBe(200)
        ->and($response->json('messageId'))->toBe('XYZ789');
});

it('resets delay after sending message', function () {
    // Arrange
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $buttons = [Button::url('btn-1', 'Test', 'https://example.com')];

    // Act - First message with delay
    $this->client->withDelay(10)->sendButtons('5511999999999', 'First message', $buttons);

    // Second message without calling withDelay again
    $this->client->sendButtons('5511999999999', 'Second message', $buttons);

    // Assert - Second message should not have delay
    Http::assertSent(function ($request) {
        return $request['message'] === 'Second message'
            && !isset($request['delayMessage']);
    });
});

it('ignores negative delay values', function () {
    // Arrange
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $buttons = [Button::url('btn-1', 'Test', 'https://example.com')];

    // Act
    $this->client->withDelay(-5)->sendButtons('5511999999999', 'Test message', $buttons);

    // Assert - Should not include delay parameter
    Http::assertSent(function ($request) {
        return !isset($request['delayMessage']);
    });
});

it('ignores zero delay values', function () {
    // Arrange
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $buttons = [Button::url('btn-1', 'Test', 'https://example.com')];

    // Act
    $this->client->withDelay(0)->sendButtons('5511999999999', 'Test message', $buttons);

    // Assert - Should not include delay parameter
    Http::assertSent(function ($request) {
        return !isset($request['delayMessage']);
    });
});
