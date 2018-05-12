@extends('layouts.eagle')
@section('title')
Finalização de Rotas @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active">Finalização de Rotas</li>
    </ul>

    <div id="finalizacaoRota">
        <div class="tab-content col-sm-12">
            <div class="page-title">
                <h2>
                    <span class="flaticon-icon025"></span> Finalização de Rotas
                </h2>
            </div>
            <div class="messagens"></div>
            <div class="form-finalizacao-rota">
                <form id="formFinalizacaoRota" method="POST" action="{{url('painel/roteirizador/finalizacao/rota/regioes')}}" class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="col-sm-5">
                        <select name="clientes" class="form-control" id="finalizacaoClientes">
                            <option value="">Selecione a empresa</option>
                            @foreach($clientes as $cliente)
                                @if($cliente->clcodigo == $clienteRota)
                                    <option selected value="{{$cliente->clcodigo}}">{{$cliente->clnome}}</option>
                                @else
                                    <option value="{{$cliente->clcodigo}}">{{$cliente->clnome}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <input type="text" id="dataRoteirizacao" name="data_saida" value="{{ $data }}" placeholder="Data saída" class="form-control data-data">
                    </div>
                    <button data-busca="{{ $buscar }}" type="button" id="submitFinalizaRota" class="btn btn-info">Buscar Rotas</button>
                </form>
            </div>
            <div class="col-sm-12">
                <div id="finalizaRota" class="col-sm-12 mapa-finalizacao-rota">
                    <div id="mapaPrincipal"></div>
                </div>
            </div>
            <div class="col-sm-12 tableListaRotasFinalizacao">
                <div class="nomargin nopadding divTitleListaRota">
                    <h2 title='Clique aqui para esconder a tabela de rotas' data-toggle="collapse" aria-expanded="true" data-target=".to-collapse-finalizacao" class='collapse-table' ><a><span class='fa fa-chevron-down'></span></a>Lista de Rotas</h2>
                </div>
                <div class="divLegendaListaRota">
                    <div style="display: table;float: left;width: 50%;">
                        <div class="divLegendaListaRotaItem">
                            <span class="sucesso"></span>
                            <div>90% à 100% Carregado</div>
                        </div>
                        <div class="divLegendaListaRotaItem">
                            <span class="padao"></span>
                            <div>Menos de 70% Carregado</div>
                        </div>
                    </div>
                    <div style="display: table;float: left;width: 50%;">
                        <div class="divLegendaListaRotaItem">
                            <span class="alerta"></span>
                            <div>70% à 90% Carregado</div>
                        </div>
                        <div class="divLegendaListaRotaItem">
                            <span class="perigo"></span>
                            <div>Limite Ultrapassado</div>
                        </div>
                    </div>
                </div>
                <div class="block-opcoes-finalizacao">
                    <!-- <div class='col-sm-19 class to-float'> -->
                    <div class='class'>
                        <div class='col-sm-1 '></div>
                        <div class="col-sm-5 input-search-finalizacao">
                            <div class="inner-addon left-addon">
                                <i class="glyphicon glyphicon-search"></i>
                                <input type="text" value="" class="flt-busca-finalizacao flt-change-busca form-control">
                            </div>
                        </div>
                        <section id="sectionListaRotas">
                            <button disabled id='btnMesclaRota' type="button" class="btn btn-primary bt-mesclar-rotas col-md-3">
                                <span class="fa fa-compress"></span>
                                Mesclar rotas
                            </button>
                            <a class="btn btn-primary bt-imprimir-rotas">
                                <span class="fa fa-print"></span>
                                Imprimir
                            </a>
                        </section>
                        <section id="sectionConfirmarRotasVeiculos">
                            <button disabled type="button" data-toggle="modal" data-target="#modalAlerta" class="btn btn-success bt-mesclar-rotas-confirmacao col-md-3">
                                <span class="fa fa-check"></span>
                                Confirmar
                            </button>
                            <button type="button" class="btn btn-danger bt-mesclar-rotas">
                                <span class="glyphicon glyphicon-remove"></span>
                                Cancelar
                            </button>
                        </section>
                    </div>
                </div>
                <div class="nopadding col-sm-12 collapse in to-collapse-finalizacao ">
                    <table class="table" id="headerTableFinalizacao">
                        <thead>
                            <tr class="stl-table" >
                                <th>Cor</th>
                                <th>Código</th>
                                <th>Placa</th>
                                <th>Prefixo</th>
                                <th>Peso</th>
                                <th>Cubagem</th>
                                <th>Volume</th>
                                <th>Distância</th>
                                <th>Tempo</th>
                                <th>Valor</th>
                                <th>Custo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="table-responsive">
                        <table class="table " id="tableFinalizacao">
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

@stop
