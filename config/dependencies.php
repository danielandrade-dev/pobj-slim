<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return function ($container) {
    registerDatabase($container);
    registerRepositories($container);
    registerUseCases($container);
    registerControllers($container);
};

function registerDatabase($container)
{
    $container['db'] = function ($c) {
        $settings = $c->get('settings')['db'];
        
        $capsule = new Capsule;
        
        // Se host for 'localhost', usar '127.0.0.1' para forçar conexão TCP/IP
        $host = ($settings['host'] === 'localhost') ? '127.0.0.1' : $settings['host'];
        
        $connectionConfig = [
            'driver'    => $settings['driver'],
            'host'      => $host,
            'database'  => $settings['database'],
            'username'  => $settings['username'],
            'password'  => $settings['password'],
            'charset'   => $settings['charset'],
            'collation' => $settings['collation'] ?? 'utf8_unicode_ci',
            'prefix'    => '',
        ];
        
        // Adicionar porta se estiver definida
        if (!empty($settings['port'])) {
            $connectionConfig['port'] = $settings['port'];
        }
        
        $capsule->addConnection($connectionConfig);
        
        // permite modelos usar DB globalmente
        $capsule->setAsGlobal();
        
        $capsule->bootEloquent();
        
        return $capsule;
    };
}

function registerRepositories($container)
{
    $container['App\Infrastructure\Persistence\EstruturaRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\EstruturaRepository();
    };

    $container['App\Infrastructure\Persistence\StatusIndicadoresRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\StatusIndicadoresRepository();
    };

    $container['App\Infrastructure\Persistence\RealizadosRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\RealizadosRepository();
    };

    $container['App\Infrastructure\Persistence\MetasRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\MetasRepository();
    };

    $container['App\Infrastructure\Persistence\VariavelRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\VariavelRepository();
    };

    $container['App\Infrastructure\Persistence\ProdutoRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\ProdutoRepository();
    };

    $container['App\Infrastructure\Persistence\CalendarioRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\CalendarioRepository();
    };

    $container['App\Infrastructure\Persistence\CampanhasRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\CampanhasRepository();
    };

    $container['App\Infrastructure\Persistence\DetalhesRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\DetalhesRepository();
    };

    $container['App\Infrastructure\Persistence\HistoricoRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\HistoricoRepository();
    };

    $container['App\Infrastructure\Persistence\LeadsRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\LeadsRepository();
    };

    $container['App\Infrastructure\Persistence\OmegaUsersRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\OmegaUsersRepository();
    };

    $container['App\Infrastructure\Persistence\OmegaStatusRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\OmegaStatusRepository();
    };

    $container['App\Infrastructure\Persistence\OmegaStructureRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\OmegaStructureRepository();
    };

    $container['App\Infrastructure\Persistence\OmegaTicketsRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\OmegaTicketsRepository();
    };

    $container['App\Infrastructure\Persistence\PontosRepository'] = function ($c) {
        return new \App\Infrastructure\Persistence\PontosRepository();
    };
}

function registerUseCases($container)
{
    $container['App\Application\UseCase\FiltrosUseCase'] = function ($c) {
        return new \App\Application\UseCase\FiltrosUseCase(
            $c->get('App\Infrastructure\Persistence\EstruturaRepository'),
            $c->get('App\Infrastructure\Persistence\StatusIndicadoresRepository')
        );
    };

    $container['App\Application\UseCase\StatusIndicadoresUseCase'] = function ($c) {
        return new \App\Application\UseCase\StatusIndicadoresUseCase(
            $c->get('App\Infrastructure\Persistence\StatusIndicadoresRepository')
        );
    };

    $container['App\Application\UseCase\AgentUseCase'] = function ($c) {
        return new \App\Application\UseCase\AgentUseCase();
    };

    $container['App\Application\UseCase\OmegaUsersUseCase'] = function ($c) {
        return new \App\Application\UseCase\OmegaUsersUseCase(
            $c->get('App\Infrastructure\Persistence\OmegaUsersRepository')
        );
    };

    $container['App\Application\UseCase\OmegaStatusUseCase'] = function ($c) {
        return new \App\Application\UseCase\OmegaStatusUseCase(
            $c->get('App\Infrastructure\Persistence\OmegaStatusRepository')
        );
    };

    $container['App\Application\UseCase\OmegaStructureUseCase'] = function ($c) {
        return new \App\Application\UseCase\OmegaStructureUseCase(
            $c->get('App\Infrastructure\Persistence\OmegaStructureRepository')
        );
    };

    $container['App\Application\UseCase\OmegaTicketsUseCase'] = function ($c) {
        return new \App\Application\UseCase\OmegaTicketsUseCase(
            $c->get('App\Infrastructure\Persistence\OmegaTicketsRepository')
        );
    };

    $container['App\Application\UseCase\DetalhesUseCase'] = function ($c) {
        return new \App\Application\UseCase\DetalhesUseCase(
            $c->get('App\Infrastructure\Persistence\DetalhesRepository')
        );
    };

    $container['App\Application\UseCase\EstruturaUseCase'] = function ($c) {
        return new \App\Application\UseCase\EstruturaUseCase(
            $c->get('App\Infrastructure\Persistence\EstruturaRepository')
        );
    };

    $container['App\Application\UseCase\LeadsUseCase'] = function ($c) {
        return new \App\Application\UseCase\LeadsUseCase(
            $c->get('App\Infrastructure\Persistence\LeadsRepository')
        );
    };

    $container['App\Application\UseCase\RealizadoUseCase'] = function ($c) {
        return new \App\Application\UseCase\RealizadoUseCase(
            $c->get('App\Infrastructure\Persistence\RealizadosRepository')
        );
    };

    $container['App\Application\UseCase\VariavelUseCase'] = function ($c) {
        return new \App\Application\UseCase\VariavelUseCase(
            $c->get('App\Infrastructure\Persistence\VariavelRepository')
        );
    };

    $container['App\Application\UseCase\CampanhasUseCase'] = function ($c) {
        return new \App\Application\UseCase\CampanhasUseCase(
            $c->get('App\Infrastructure\Persistence\CampanhasRepository')
        );
    };

    $container['App\Application\UseCase\ProdutoUseCase'] = function ($c) {
        return new \App\Application\UseCase\ProdutoUseCase(
            $c->get('App\Infrastructure\Persistence\ProdutoRepository')
        );
    };

    $container['App\Application\UseCase\MetaUseCase'] = function ($c) {
        return new \App\Application\UseCase\MetaUseCase(
            $c->get('App\Infrastructure\Persistence\MetasRepository')
        );
    };

    $container['App\Application\UseCase\HistoricoUseCase'] = function ($c) {
        return new \App\Application\UseCase\HistoricoUseCase(
            $c->get('App\Infrastructure\Persistence\HistoricoRepository')
        );
    };

    $container['App\Application\UseCase\CalendarioUseCase'] = function ($c) {
        return new \App\Application\UseCase\CalendarioUseCase(
            $c->get('App\Infrastructure\Persistence\CalendarioRepository')
        );
    };

    $container['App\Application\UseCase\PontosUseCase'] = function ($c) {
        return new \App\Application\UseCase\PontosUseCase(
            $c->get('App\Infrastructure\Persistence\PontosRepository')
        );
    };
}

function registerControllers($container)
{
    $container['App\Presentation\Controllers\HealthController'] = function ($c) {
        return new \App\Presentation\Controllers\HealthController();
    };

    $container['App\Presentation\Controllers\AgentController'] = function ($c) {
        return new \App\Presentation\Controllers\AgentController(
            $c->get('App\Application\UseCase\AgentUseCase')
        );
    };

    $container['App\Presentation\Controllers\CalendarioController'] = function ($c) {
        return new \App\Presentation\Controllers\CalendarioController(
            $c->get('App\Application\UseCase\CalendarioUseCase')
        );
    };

    $container['App\Presentation\Controllers\CampanhasController'] = function ($c) {
        return new \App\Presentation\Controllers\CampanhasController(
            $c->get('App\Application\UseCase\CampanhasUseCase')
        );
    };

    $container['App\Presentation\Controllers\DetalhesController'] = function ($c) {
        return new \App\Presentation\Controllers\DetalhesController(
            $c->get('App\Application\UseCase\DetalhesUseCase')
        );
    };

    $container['App\Presentation\Controllers\EstruturaController'] = function ($c) {
        return new \App\Presentation\Controllers\EstruturaController(
            $c->get('App\Application\UseCase\EstruturaUseCase')
        );
    };

    $container['App\Presentation\Controllers\FiltrosController'] = function ($c) {
        return new \App\Presentation\Controllers\FiltrosController(
            $c->get('App\Application\UseCase\FiltrosUseCase')
        );
    };

    $container['App\Presentation\Controllers\HistoricoController'] = function ($c) {
        return new \App\Presentation\Controllers\HistoricoController(
            $c->get('App\Application\UseCase\HistoricoUseCase')
        );
    };

    $container['App\Presentation\Controllers\LeadsController'] = function ($c) {
        return new \App\Presentation\Controllers\LeadsController(
            $c->get('App\Application\UseCase\LeadsUseCase')
        );
    };

    $container['App\Presentation\Controllers\MetasController'] = function ($c) {
        return new \App\Presentation\Controllers\MetasController(
            $c->get('App\Application\UseCase\MetaUseCase')
        );
    };

    $container['App\Presentation\Controllers\RealizadosController'] = function ($c) {
        return new \App\Presentation\Controllers\RealizadosController(
            $c->get('App\Application\UseCase\RealizadoUseCase')
        );
    };

    $container['App\Presentation\Controllers\ProdutosController'] = function ($c) {
        return new \App\Presentation\Controllers\ProdutosController(
            $c->get('App\Application\UseCase\ProdutoUseCase')
        );
    };

    $container['App\Presentation\Controllers\StatusIndicadoresController'] = function ($c) {
        return new \App\Presentation\Controllers\StatusIndicadoresController(
            $c->get('App\Application\UseCase\StatusIndicadoresUseCase')
        );
    };

    $container['App\Presentation\Controllers\VariavelController'] = function ($c) {
        return new \App\Presentation\Controllers\VariavelController(
            $c->get('App\Application\UseCase\VariavelUseCase')
        );
    };

    $container['App\Presentation\Controllers\PontosController'] = function ($c) {
        return new \App\Presentation\Controllers\PontosController(
            $c->get('App\Application\UseCase\PontosUseCase')
        );
    };

    $container['App\Presentation\Controllers\OmegaMesuController'] = function ($c) {
        return new \App\Presentation\Controllers\Omega\OmegaMesuController(
            $c->get('App\Application\UseCase\OmegaMesuUseCase')
        );
    };

    $container['App\Presentation\Controllers\OmegaStatusController'] = function ($c) {
        return new \App\Presentation\Controllers\Omega\OmegaStatusController(
            $c->get('App\Application\UseCase\OmegaStatusUseCase')
        );
    };

    $container['App\Presentation\Controllers\OmegaStructureController'] = function ($c) {
        return new \App\Presentation\Controllers\Omega\OmegaStructureController(
            $c->get('App\Application\UseCase\OmegaStructureUseCase')
        );
    };

    $container['App\Presentation\Controllers\OmegaTicketsController'] = function ($c) {
        return new \App\Presentation\Controllers\Omega\OmegaTicketsController(
            $c->get('App\Application\UseCase\OmegaTicketsUseCase')
        );
    };

    $container['App\Presentation\Controllers\OmegaUsersController'] = function ($c) {
        return new \App\Presentation\Controllers\Omega\OmegaUsersController(
            $c->get('App\Application\UseCase\OmegaUsersUseCase')
        );
    };
}
