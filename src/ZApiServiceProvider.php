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
     * Este método é chamado automaticamente durante a fase de registro do framework.
     * Registra o ZClient para que uma nova instância seja criada a cada resolução,
     * evitando problemas de estado compartilhado em ambientes multi-tenant.
     * 
     * @return void
     */
    public function register()
    {
        // Une as configurações para que o sistema sempre tenha os valores default
        $this->mergeConfigFrom(__DIR__.'/../config/zapi.php', 'zapi');

        // Registra como bind (não singleton) para evitar compartilhamento de estado
        // Cada resolução cria uma nova instância, ideal para multi-tenancy
        $this->app->bind('zapi', function ($app) {
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
