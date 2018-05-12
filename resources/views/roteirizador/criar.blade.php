@extends('layouts.eagle')
@section('title')
Rota Automática @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active">Rota automática</li>
    </ul>
    <form id="formCadastroParametrizacao" method="POST" action="{{url('painel/roteirizador/rota/automatica')}}" class="form-horizontal">
        <div id="cadastroParametrizacao" class="container">
            <div class="tab-content col-sm-12">
                <div class="page-title">
                    <h2>
                        <span class="flaticon-icon027"></span> Rota Automática
                    </h2>
                </div>
                <ul class="nav nav-tabs nav-eagle">
                </ul>
                <div id="homeCadCliente" class="tab-pane fade in active pane-eagle">
                    <div id="formCadastro" class="panel panel-default">
                        {{ csrf_field() }}
                        <div class="col-sm-12">
                            <div class="col-sm-12">
                                <div class="col-sm-2" >
                                    <h5 >Data Saída</h5>
                                    <input class="form-control data-data-min-today " value="{{ date('d/m/Y') }}" id="prDataSaida" type="text" name="data_inicio">
                                </div>
                                <div class="col-sm-4">
                                    <h5>Empresa</h5>
                                    <select id="prProprietario" name="prproprietario" class="form-control">
                                        @foreach($clientes as $cliente)
                                            @if(\Auth::user()->usucliente == $cliente->clcodigo)
                                                <option selected="selected" value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                            @else
                                                <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <h5>Ponto de Saída</h5>
                                    <select id="prPontoSaida" name="prpontosaida" class="form-control">
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <h5>Ponto de Retorno</h5>
                                    <select id="prPontoRetorno" name="prpontoretorno" class="form-control">
                                    </select>
                                </div>
                                <div class="col-sm-8">
                                    <h5>Regiões</h5>
                                    <select id="prRegioes" multiple name="prregioes" class="form-control">
                                    </select>
                                </div>
                                <div class="row col-sm-4">
                                    <a id="carregarParametrosRoterizacao" class=" bt-gerar btn btn-lg btn-info bt_carregar_dados"><span class="glyphicon glyphicon-thumbs-up"></span>Carregar Dados</a>
                                </div>
                            </div>
                            <hr class="col-xs-12" ></hr>
                            <div class="block-pedidos hidden">
                                <h5>Lista de Pedidos</h5>
                                <table id="tableRListarPedidos" class="table datatable table-hover table-condensed table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input checked="checked" disabled="disabled" type="checkbox" name="incluir" id="incluirTodos">
                                            </th>
                                            <th class="th-pedido-automatico">Ponto</th>
                                            <th>Pedido</th>
                                            <th>Volumes</th>
                                            <th>Cubagem</th>
                                            <th>Quilos</th>
                                            <th class="hidden">Valor</th>
                                            <th>Valor</th>
                                            <th>Tipo</th>
                                            <th class="th-acoes" >Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="totais">
                                <th class="">
                                    <span>Volumes: <span class="ped-vol"></span></span>
                                    <span>Cubagem: <span class="ped-cub"></span></span>
                                    <span>Quilos: <span class="ped-kg"></span></span>
                                    <span>Valores: <span class="ped-val"></span></span>
                                </tr>
                            </div>
                            <hr class="col-xs-12"></hr>
                            <div class="block-pedidos hidden">
                                <h5>Lista de Veículos</h5>
                                <table id="tableRListarVeiculos" class="table datatable table-hover table-condensed table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input checked="checked" disabled="disabled" type="checkbox" name="incluir" id="incluirTodosVeiculos">
                                            </th>
                                            <th>Placa</th>
                                            <th>Prefixo</th>
                                            <th>Capacidade KG</th>
                                            <th>Cubagem</th>
                                            <th>Max. Entregas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <!-- <tfoot><tr></tr></tfoot> -->
                                </table>
                            </div>
                            <div class="totais">
                                <th class="">
                                    <span>Capacidade KG: <span class="veic-capacidade"></span></span>
                                    <span>Cubagem: <span class="veic-cubagem"></span></span>
                                    <span>Max. Entregas: <span class="veic-max-entregas"></span></span>
                                </tr>
                            </div>
                        </div>
                        <div class="block-salvar col-sm-12 text-right">
                            <div class="col-sm-12">
                                <button id="montarCargas" type="submit" value="save" class="btn hidden btn-lg btn-primary"><span class="glyphicon glyphicon-ok"></span>Montar Cargas</button>
                                <a href="{{url('painel/roteirizador/criar')}}" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop
