<?php

namespace SuaEmpresa\ZApi\Services;

use Illuminate\Support\Facades\Http;
use SuaEmpresa\ZApi\DTOs\Button;

/**
 * Cliente para integração com a API Z-API
 * 
 * Esta classe fornece métodos para interagir com a Z-API,
 * permitindo o envio de mensagens com botões interativos.
 * Suporta method chaining para configuração fluente.
 * 
 * @package SuaEmpresa\ZApi\Services
 */
class ZClient
{
    /**
     * ID da instância Z-API
     */
    protected ?string $instance = null;

    /**
     * Token da instância Z-API
     */
    protected ?string $token = null;

    /**
     * Token do cliente para autenticação
     */
    protected ?string $clientToken = null;

    /**
     * Delay em segundos para envio da mensagem
     */
    protected ?int $delay = null;

    /**
     * Configura as credenciais da instância Z-API dinamicamente
     * 
     * Ideal para ambientes multi-tenant onde cada cliente possui
     * suas próprias credenciais de API.
     * 
     * @param string $instance ID da instância Z-API
     * @param string $token Token da instância
     * @param string $clientToken Token do cliente para autenticação
     * @return self Retorna a própria instância para method chaining
     * 
     * @throws \InvalidArgumentException Se algum parâmetro estiver vazio
     * 
     * @example
     * ```php
     * $client->using($instance, $token, $clientToken)
     *        ->withDelay(5)
     *        ->sendButtons(...);
     * ```
     */
    public function using(string $instance, string $token, string $clientToken): self
    {
        if (empty($instance) || empty($token) || empty($clientToken)) {
            throw new \InvalidArgumentException(
                'Instance, token and clientToken cannot be empty'
            );
        }

        $this->instance = $instance;
        $this->token = $token;
        $this->clientToken = $clientToken;
        return $this;
    }

    /**
     * Define um delay para o envio da mensagem
     * 
     * Configura o parâmetro delayMessage da Z-API para agendar
     * o envio da mensagem após um determinado número de segundos.
     * 
     * @param int $seconds Número de segundos para atrasar o envio (deve ser positivo)
     * @return self Retorna a própria instância para method chaining
     * 
     * @example
     * ```php
     * $client->withDelay(10)->sendButtons(...); // Envia após 10 segundos
     * ```
     */
    public function withDelay(int $seconds): self
    {
        $this->delay = $this->validateDelay($seconds);
        return $this;
    }

    /**
     * Valida o valor do delay
     * 
     * @param int $seconds Número de segundos
     * @return int|null Retorna o valor se válido, ou null se inválido
     */
    protected function validateDelay(int $seconds): ?int
    {
        return $seconds > 0 ? $seconds : null;
    }

    /**
     * Envia botões interativos para o WhatsApp via Z-API
     * 
     * Envia uma mensagem com botões de ação (URL ou CALL) para um número
     * de WhatsApp. Suporta tanto objetos Button (recomendado) quanto arrays
     * simples para compatibilidade.
     * 
     * @param string $phone Número do telefone no formato internacional (ex: 5511999999999)
     * @param string $text Mensagem de texto a ser enviada junto com os botões
     * @param array<Button|array> $buttons Array de objetos Button ou arrays associativos
     * @return \Illuminate\Http\Client\Response Resposta da requisição HTTP
     * 
     * @throws \RuntimeException Se as credenciais não foram configuradas via using()
     * 
     * @example
     * ```php
     * use SuaEmpresa\ZApi\DTOs\Button;
     * 
     * $response = $client->sendButtons(
     *     '5511999999999',
     *     'Confira nossas ofertas!',
     *     [
     *         Button::url('btn-1', 'Ver Ofertas', 'https://example.com'),
     *         Button::call('btn-2', 'Ligar', '551133334444')
     *     ]
     * );
     * ```
     */
    public function sendButtons(string $phone, string $text, array $buttons): \Illuminate\Http\Client\Response
    {
        // Valida se as credenciais foram configuradas
        if ($this->instance === null || $this->token === null || $this->clientToken === null) {
            throw new \RuntimeException(
                'Credentials not configured. Call using() method before sendButtons().'
            );
        }

        $url = "https://api.z-api.io/instances/{$this->instance}/token/{$this->token}/send-button-actions";

        // Converte objetos Button para arrays
        $buttonActions = $this->normalizeButtons($buttons);

        // Prepara o payload base
        $payload = [
            'phone' => $phone,
            'message' => $text,
            'buttonActions' => $buttonActions
        ];

        // Adiciona delay se configurado
        if ($this->delay !== null) {
            $payload['delayMessage'] = $this->delay;
        }

        $response = Http::withHeaders([
            'Client-Token' => $this->clientToken
        ])->post($url, $payload);

        // Limpa o delay após o envio para não afetar próximas chamadas
        $this->delay = null;

        return $response;
    }

    /**
     * Normaliza os botões para o formato esperado pela API
     * 
     * Converte objetos Button em arrays associativos. Se o item já for
     * um array, mantém como está para garantir compatibilidade retroativa.
     * 
     * @param array<Button|array> $buttons Array de botões a serem normalizados
     * @return array<array> Array de botões no formato esperado pela API
     */
    protected function normalizeButtons(array $buttons): array
    {
        return array_map(function ($button) {
            if ($button instanceof Button) {
                return $button->toArray();
            }
            
            // Mantém compatibilidade com arrays simples
            return $button;
        }, $buttons);
    }
}

