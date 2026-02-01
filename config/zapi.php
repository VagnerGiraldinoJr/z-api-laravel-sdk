<?php

return [
    /*
    | As credenciais padrão podem ser definidas aqui ou passadas 
    | dinamicamente via método using() para casos Multitenant.
    */
    'client_token' => env('ZAPI_CLIENT_TOKEN'),
    'instance_id'  => env('ZAPI_INSTANCE_ID'),
    'instance_token' => env('ZAPI_INSTANCE_TOKEN'),
];
