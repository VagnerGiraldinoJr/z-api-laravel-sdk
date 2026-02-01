<?php

namespace SuaEmpresa\ZApi\Facades;

use Illuminate\Support\Facades\Facade;

class ZApi extends Facade
{
    /**
     * Define o nome que foi registrado no ServiceProvider.
     */
    protected static function getFacadeAccessor()
    {
        return 'zapi';
    }
}
