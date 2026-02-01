<?php

namespace SuaEmpresa\ZApi\Services;

use Illuminate\Support\Facades\Http;

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

    public function sendButtons(string $phone, string $text, array $buttons)
    {
        $url = "https://api.z-api.io/instances/{$this->instance}/token/{$this->token}/send-button-actions";

        return Http::withHeaders([
            'Client-Token' => $this->clientToken
        ])->post($url, [
            'phone' => $phone,
            'message' => $text,
            'buttonActions' => $buttons
        ]);
    }
}
