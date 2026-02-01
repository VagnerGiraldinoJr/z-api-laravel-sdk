<?php

use SuaEmpresa\ZApi\DTOs\Button;

describe('Button DTO', function () {
    it('creates a URL button using factory method', function () {
        $button = Button::url('btn-1', 'Ver Oferta', 'https://example.com/offer');

        expect($button->getId())->toBe('btn-1')
            ->and($button->getType())->toBe('URL')
            ->and($button->getLabel())->toBe('Ver Oferta')
            ->and($button->getUrl())->toBe('https://example.com/offer')
            ->and($button->getPhone())->toBeNull();
    });

    it('creates a CALL button using factory method', function () {
        $button = Button::call('btn-2', 'Ligar', '551133334444');

        expect($button->getId())->toBe('btn-2')
            ->and($button->getType())->toBe('CALL')
            ->and($button->getLabel())->toBe('Ligar')
            ->and($button->getPhone())->toBe('551133334444')
            ->and($button->getUrl())->toBeNull();
    });

    it('converts URL button to array correctly', function () {
        $button = Button::url('btn-1', 'Ver Oferta', 'https://example.com/offer');
        $array = $button->toArray();

        expect($array)->toBe([
            'id' => 'btn-1',
            'type' => 'URL',
            'label' => 'Ver Oferta',
            'url' => 'https://example.com/offer'
        ]);
    });

    it('converts CALL button to array correctly', function () {
        $button = Button::call('btn-2', 'Ligar', '551133334444');
        $array = $button->toArray();

        expect($array)->toBe([
            'id' => 'btn-2',
            'type' => 'CALL',
            'label' => 'Ligar',
            'phone' => '551133334444'
        ]);
    });

    it('throws exception for invalid button type', function () {
        new Button('btn-1', 'INVALID', 'Test', null, null);
    })->throws(InvalidArgumentException::class, "Invalid button type 'INVALID'");

    it('throws exception when URL button is missing url parameter', function () {
        new Button('btn-1', 'URL', 'Test', null, null);
    })->throws(InvalidArgumentException::class, "Button of type 'URL' requires a 'url' parameter");

    it('throws exception when CALL button is missing phone parameter', function () {
        new Button('btn-1', 'CALL', 'Test', null, null);
    })->throws(InvalidArgumentException::class, "Button of type 'CALL' requires a 'phone' parameter");

    it('allows creating URL button with constructor', function () {
        $button = new Button('btn-1', 'URL', 'Test', 'https://test.com', null);

        expect($button->getType())->toBe('URL')
            ->and($button->getUrl())->toBe('https://test.com');
    });

    it('allows creating CALL button with constructor', function () {
        $button = new Button('btn-1', 'CALL', 'Test', null, '5511999999999');

        expect($button->getType())->toBe('CALL')
            ->and($button->getPhone())->toBe('5511999999999');
    });
});
