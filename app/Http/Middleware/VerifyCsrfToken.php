<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/pontosreferencia/all',
        'api/pontosreferencia/all',
        'api/auth/usuacodigo',
        'api/veiculos/find',
        'api/modulos/find',
        'api/alertas/getalertas',
        'api/alertas/getall_alertas',
        'api/alertas/get_count_alertas',
        'api/veiculos/getposicoes',
        'api/veiculos/find_modulo',
        'api/modulos/find_by_id',
        'api/veiculos/find_all_last_position',
        'api/pontos',
        'api/eventos/inserir_eventos_app',
        'api/eventos/inserir_eventos_app',
        'api/bilhetes/inserir_bilhetes_app',
        'api/veiculos/find_all_from_user_app',
        'api/veiculos/find_all_modulos_from_user_app',
        'veiculos/maps/carregarMarkers',
        'veiculos/maps/atualizarMarkers',
        'api/historico/posicoes',
        'api/veiculo/rota',
        'api/veiculo/velocidade',
        'api/veiculo/paradas',
        'api/veiculo/acionamento/portas',
        'api/updateAlStatus',
        'api/updateAlStatusPut',
        '/api/rotas/cadastro',
        'api/justificativas/getAll',
        'api/bilhetes/updateBilhetes',
        'api/bilhetes/updateIgnicaoVeiculo',
        'api/pontos/cadastro'
    ];
}
