<?php

namespace SuaEmpresa\ZApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SuaEmpresa\ZApi\Events\ZApiMessageReceived;

class ZApiWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Log para debug (opcional)
        \Log::info('Z-API Webhook:', $request->all());

        // Dispara um evento interno do Laravel com os dados da Z-API
        event(new ZApiMessageReceived($request->all()));

        return response()->json(['status' => 'success']);
    }
}
