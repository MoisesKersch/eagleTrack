@extends('layouts.app')
@section('title')
Listagem de jornada de trabalho @parent
@stop
@section('content')
<div id="pageHome">
    <div id="mapaPrincipal"></div>
</div>
<div id="divOpcoesVeiculo" >

</div>
<div id="divOpcoesVeiculoListaPosicoes">
    <div id='divResultadoOpcoesVeiculos'>
        <!-- <div class='load reqListaLoading'></div> -->
        <!-- <div id='divResultadoOpcoesVeiculosParametros'></div> -->
        <div class="tituloListaPosicoes">
            <span id='tituloResultadosOpcoesVeiculos' class='infoVeiculoTitulo'>
                <span class='glyphicon glyphicon-map-marker'></span>
                    Lista de Posições
            </span>
            <button class="btn btn-xs btn-danger" class="fechaOpcoesVeiculo" id="fechaOpcoesVeiculoListaPos"><span class="glyphicon glyphicon-remove"></span></button>
        </div>
        <hr>
        <div id='legenda-resultado-veiculo'>
        </div>
        <div id='divResultadoDentroOpcoesVeiculos'></div>
    </div>
</div>
    <div id="painelControle" class="navbar-fixed-bottom">
        <div class="alert alert-danger center-block message-min motorista-erro" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
            <strong>Ops!</strong> Não foi possivel atualizar o motorista!
        </div>
        @foreach($perfis as $perfil)
            @if($perfil->piid == 'mappainelcontrole' && $perfil->ppvisualizar)
                <button id="botaoPainelControle" class="btn btn-primary btn-sm btn-painel-mapa" type="button"
                    data-toggle="collapse" data-target="#painelControleCollapse" aria-expanded="false" data-parent="painelControle">
                    Painel de Controle <span class="fa fa-chevron-right"> <span>
                </button>
            @endif
        @endforeach
        <button id="rotasPC" class="btn btn-dark btn-sm btn-painel-mapa" type="button"
        data-toggle="collapse" data-target="#rotasPainelCollapse" aria-expanded="false" data-parent="#painelControle">
        Rotas <span class="fa fa-chevron-right"> <span>
        </button>
        <div class="paramRotas">
            <div id="painelControleTabela1">
                <strong> <label>Rota:</label></strong>
                <input class="filters" type="checkbox" id="checkIniciado" checked> Iniciada
                <input class="filters" type="checkbox" id="checkPendente" checked> Pendente
                <input class="filters" type="checkbox" id="checkFinalizado" checked> Finalizada
                <input type="text" name="data_inicio" value="{{date('d/m/Y')}}" id="endDate">
            </div>
        </div>
        <div class="accordion-group">
            <div class="collapse" id="painelControleCollapse">
            <div id="filtro-painel-controle">
                <div class="row">
                    <div class="col-sm-4 select-painel">
                        <select class="form-control select-empresa-painel " multiple name="empresa[]">
                            <option class="option-todos" value="0">Selecionar todos</option>
                            @foreach($clientes as $cliente)
                            <option {{ (Auth::user()->usumaster == 'N') ? 'selected' : '' }} value="{{ $cliente->clcodigo }}">{{ $cliente->clfantasia ?: $cliente->clnome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="divFiltroIgnicao">
                        <label class="labelIgnicao">Ignição:</label>
                        <div>
                            <input id="iptIgnicaoLig" class="filtroIgnicaoPC" type="checkbox">
                            <label for="iptIgnicaoLig">Ligado</label>
                        </div>
                        <div>
                            <input id="iptIgnicaoDeslg" class="filtroIgnicaoPC" type="checkbox">
                            <label for="iptIgnicaoDeslg">Desligado</label>
                        </div>
                    </div>
                </div>
            </div>
            <div id="painelControleTabela">
                <div>
                   <table id="tabelaPainelControle" class="table table-striped">
                        <thead>
                            <tr>
                                <th><input id="checkboxTbPc" type="checkbox" checked> Localizar</th>
                                <th>Placa</th>
                                <th>Prefixo</th>
                                <th>Descrição</th>
                                <th>Ignição</th>
                                <th>Ult. Posição</th>
                                <th>Próximo</th>
                                <th>Alertas</th>
                                <th class="text-center" >Motorista | Ajudante</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="collapse" id="rotasPainelCollapse" style="background: white">
            <div class="col-sm-12" id="backWhite">
                <div class="row">
                    <div class="col-sm-4 select-painel" id="teste">
                        <select class="form-control rotas-empresas-select-painel" multiple name="empresaRota[]">
                            <option class="option-todos" value="0">Selecionar todos</option>
                            @foreach($clientes as $cliente)
                            <option {{ (Auth::user()->usumaster == 'N') ? 'selected' : '' }} value="{{ $cliente->clcodigo }}">{{ $cliente->clfantasia ?: $cliente->clnome }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div>
                    <table id="tableRotas" class="table hover">
                        <thead>
                            <tr>
                                <th>Vizualizar</th>
                                <th>Placa</th>
                                <th>Prefixo</th>
                                <th>Data/Hora de início</th>
                                <th>Realizados</th>
                                <th>Km</th>
                                <th>Tempo</th>
                                <th>Motorista | Ajudante</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
