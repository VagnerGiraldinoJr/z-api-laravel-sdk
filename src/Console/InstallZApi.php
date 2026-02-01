<?php

namespace SuaEmpresa\ZApi\Console;

use Illuminate\Console\Command;

class InstallZApi extends Command
{
    protected $signature = 'zapi:install';
    protected $description = 'Configura o SDK da Z-API no projeto';

    public function handle()
    {
        $this->info('Iniciando instalação do Z-API SDK...');
        
        $this->call('vendor:publish', [
            '--provider' => "SuaEmpresa\ZApi\ZApiServiceProvider",
            '--tag' => "config"
        ]);

        $this->info('✅ SDK instalado com sucesso!');
        $this->warn('Não esqueça de configurar a URL de Webhook no painel Z-API: ' . url('/zapi/webhook'));
    }
}
