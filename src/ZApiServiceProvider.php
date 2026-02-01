<?php

namespace SuaEmpresa\ZApi;

use Illuminate\Support\ServiceProvider;
use SuaEmpresa\ZApi\Console\InstallZApi;
use SuaEmpresa\ZApi\Services\ZClient;

/**
 * Service Provider para o Z-API Laravel SDK
 * 
 * Este provider registra os serviços do SDK no container do Laravel,
 * publica configurações e rotas, e registra comandos artisan.
 * 
 * @package SuaEmpresa\ZApi
 */
class ZApiServiceProvider extends ServiceProvider
{
    /**
     * Registra serviços no container do Laravel
     * 
     * Este método é chamado automaticamente durante o boot do framework.
     * Registra o ZClient como singleton para que a mesma instância seja
     * reutilizada durante todo o ciclo de vida da requisição.
     * 
     * @return void
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
     * Executa ações durante a inicialização (boot) do Laravel
     * 
     * Carrega rotas, publica configurações e registra comandos artisan.
     * As publicações só são registradas quando executado via CLI.
     * 
     * @return void
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
