<?php

namespace SuaEmpresa\ZApi\Facades;

use Illuminate\Support\Facades\Facade;
use SuaEmpresa\ZApi\Services\ZClient;

/**
 * Facade para acesso ao cliente Z-API
 * 
 * Esta facade fornece acesso estático aos métodos do ZClient,
 * permitindo uma sintaxe fluente e conveniente para enviar
 * mensagens via Z-API em qualquer parte da aplicação Laravel.
 * 
 * @package SuaEmpresa\ZApi\Facades
 * 
 * @method static \SuaEmpresa\ZApi\Services\ZClient using(string $instance, string $token, string $clientToken) Configura as credenciais da instância Z-API
 * @method static \SuaEmpresa\ZApi\Services\ZClient withDelay(int $seconds) Define um delay para o envio da mensagem
 * @method static \Illuminate\Http\Client\Response sendButtons(string $phone, string $text, array $buttons) Envia botões interativos para o WhatsApp
 * 
 * @see \SuaEmpresa\ZApi\Services\ZClient
 * 
 * @example
 * ```php
 * use SuaEmpresa\ZApi\Facades\ZApi;
 * use SuaEmpresa\ZApi\DTOs\Button;
 * 
 * // Uso simples
 * ZApi::using($instance, $token, $clientToken)
 *     ->sendButtons('5511999999999', 'Olá!', [
 *         Button::url('btn-1', 'Ver Ofertas', 'https://example.com')
 *     ]);
 * 
 * // Com method chaining e delay
 * ZApi::using($instance, $token, $clientToken)
 *     ->withDelay(10)
 *     ->sendButtons('5511999999999', 'Mensagem agendada', [
 *         Button::call('btn-2', 'Ligar', '551133334444')
 *     ]);
 * ```
 */
class ZApi extends Facade
{
    /**
     * Define o nome do binding registrado no ServiceProvider
     * 
     * Este método é usado internamente pelo Laravel para resolver
     * a instância do serviço a partir do container.
     * 
     * @return string Nome do binding no container ('zapi')
     */
    protected static function getFacadeAccessor()
    {
        return 'zapi';
    }
}
