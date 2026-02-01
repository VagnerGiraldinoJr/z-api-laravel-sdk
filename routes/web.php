<?php

use Illuminate\Support\Facades\Route;
use SuaEmpresa\ZApi\Http\Controllers\ZApiWebhookController;

// Esta rota ficará disponível em todos os sistemas onde o SDK for instalado
Route::post('zapi/webhook', [ZApiWebhookController::class, 'handle'])
    ->name('zapi.webhook');
