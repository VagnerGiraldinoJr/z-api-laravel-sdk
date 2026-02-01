<?php

namespace SuaEmpresa\ZApi\Services;

use Illuminate\Support\Facades\Http;
use SuaEmpresa\ZApi\DTOs\Button;

class ZClient
{
    protected $instance;
    protected $token;
    protected $clientToken;

    // Método para setar as credenciais dinamicamente (SaaS)
    public function using(string $instance, string $token, string $clientToken)
    {
        $this->instance = $instance;
        $this->token = $token;
        $this->clientToken = $clientToken;
        return $this;
    }

    /**
     * Envia botões para o WhatsApp via Z-API
     * 
     * @param string $phone Número do telefone
     * @param string $text Mensagem de texto
     * @param array|Button[] $buttons Array de objetos Button ou arrays (para compatibilidade)
     */
    public function sendButtons(string $phone, string $text, array $buttons)
    {
        $url = "https://api.z-api.io/instances/{$this->instance}/token/{$this->token}/send-button-actions";

        // Converte objetos Button para arrays
        $buttonActions = $this->normalizeButtons($buttons);

        return Http::withHeaders([
            'Client-Token' => $this->clientToken
        ])->post($url, [
            'phone' => $phone,
            'message' => $text,
            'buttonActions' => $buttonActions
        ]);
    }

    /**
     * Normaliza os botões para o formato esperado pela API
     * Aceita tanto objetos Button quanto arrays
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
