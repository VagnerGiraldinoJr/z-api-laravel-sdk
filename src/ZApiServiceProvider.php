<?php

namespace SuaEmpresa\ZApi;

use Illuminate\Support\ServiceProvider;

class ZApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registra a classe principal no container do Laravel
        $this->app->singleton('zapi', function ($app) {
            return new \SuaEmpresa\ZApi\Services\ZClient();
        });
    }

    public function boot()
    {
        // Publica o arquivo de configuração quando o cliente rodar o comando de publish
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/zapi.php' => config_path('zapi.php'),
            ], 'config');
        }
    }
}
