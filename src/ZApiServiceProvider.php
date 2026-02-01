<?php

namespace SuaEmpresa\ZApi;

use Illuminate\Support\ServiceProvider;
use SuaEmpresa\ZApi\Console\InstallZApi;
use SuaEmpresa\ZApi\Services\ZClient;

class ZApiServiceProvider extends ServiceProvider
{
    /**
     * Registra serviços no container.
     */
    public function register()
    {
        // Une as configurações para que o sistema sempre tenha os valores default
        $this->mergeConfigFrom(__DIR__.'/../config/zapi.php', 'zapi');

        // Registra o Singleton para uso via Facade ou Injeção de Dependência
        $this->app->singleton('zapi', function ($app) {
            return new ZClient();
        });
    }

    /**
     * Executa ações durante a inicialização (boot) do Laravel.
     */
    public function boot()
    {
        // 1. Carrega as rotas de Webhook automaticamente
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // 2. Ações exclusivas para quando rodar via Terminal (CLI)
        if ($this->app->runningInConsole()) {
            
            // Publicação do arquivo de configuração
            $this->publishes([
                __DIR__.'/../config/zapi.php' => config_path('zapi.php'),
            ], 'config');

            // Registra o comando de instalação (Wizard)
            $this->commands([
                InstallZApi::class,
            ]);
        }
    }
}
