@extends('layouts.eagle')
@section('title')
Parametrização @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active">Rota manual</li>
</ul>
<div id="roteirizadorRotaManual">
    <div class="tab-content col-sm-12">
        <div class="page-title">
            <h2>
                <span class="flaticon-icon015"></span> Rota Manual
            </h2>
        </div>
        <div class="row-rota-manual">
            <div class="col-sm-4">
                <h3>Entregas/Coletas</h3>
            </div>
            <div class="col-sm-8">
                <h3>
                    Mapa de rotas
                    <span class="block-buttons">
                        <a href="#" class="btn btn-lg btn-primary bt-imprime-rota" disabled>Imprimir <span class="fa fa-print"></span></a>
                        <!-- <button id="btnIrFinalizacaoRota" title="Ir para finalização de rota" class="btn btn-lg btn-success">Finalizar rota <span class="fa fa-check"></span></button> -->
                        <a href="#" class="btn btn-lg btn-info bt-confirma-rota" disabled>Finalizar rota  <span class="fa fa-check"></span></a>
                    </span>
                </h3>
            </div>
        </div>
        <div class="messagens"></div>
        <div class="col-sm-4">
            <div class="form-rota-manual">
                <form id="formCadastroParametrizacao" method="POST" action="{{url('painel/roteirizador/rota/manual')}}" class="form-horizontal" enctype="multipart/form-data">
                    <h3>{{-- Entregas/Coletas --}}</h3>
                    <div class="ip-data-regiao">
                        <select class="form-control disabled-bt select-empresa-rota-manual" name="regiao">
                            <option value="">Selecione a empresa</option>
                            @foreach($empresas as $empresa)
                            @if(Auth::user()->cliente->clcodigo == $empresa->clcodigo)
                                    <option selected value="{{$empresa->clcodigo}}">{{$empresa->clnome}}</option>
                                @else
                                    <option value="{{$empresa->clcodigo}}">{{$empresa->clnome}}</option>
                                @endif
                            @endforeach
                        </select>
                        <select id="pontoInicio" class="inicio-fim disabled-bt form-control" name="ponto_inicio">
                            <option value="">Selecione a empresa primeiro</option>
                        </select>

                        <select multiple class="form-control select-regiao-reta-manual" name="regiao" ></select>
                    </div>
                    <div class="col-sm-6">
                        <input type="text" id="dataRoteirizacao" name="data_saida" value="{{date('d/m/Y')}}" placeholder="Data saída" class="form-control data-data">
                        <select id="pontoFim" class="inicio-fim disabled-bt form-control" name="ponto_fim">
                            <option value="">Selecione a empresa primeiro</option>
                        </select>
                        <a id="btGerarRotaManual" disabled="disabled" class="col-xs-12 btn btn-lg btn-info"><span class="glyphicon glyphicon-thumbs-up"></span>Gerar</a>
                    </div>
                </form>
            </div>
            <div class="rota-manual-pedidos">
                <h3>
                    Pedidos
                    <a class="mais-pedidos">
                        <span class="fa fa-plus-circle"></span>
                    </a>
                </h3>
                <div class="busca-pedido">
                    <input type="text" placeholder="Buscar pedido" name="busca_pedido" class="form-control">
                </div>
                <div class="ip-check-todos">
                    <input type="checkbox" class="checkk" title="Selecionar todos os pedidos" name="" id="">
                </div>
            </div>
            <div class="pedidos-rota"></div>

        </div>
        <div class="col-sm-8">
            <div class="col-sm-12">
                <div id="mapaRotaManual" class="mapa-rota-manual">
                    <div id="mapaPrincipal"></div>
                    <div class="block-roteirizados">
                        <div class="ja-roteirizados">
                            <h3>Veículos carregados</h3>
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true"></div>
                        </div>
                        <a href="#" class="mostrar-painel">
                            <span class="fa fa-truck"></span><br />
                            Cargas
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="veiculos-rota-titulo">
                <h3>Veículos</h3>
            </div>
            <div class="busca-veiculos">
                <input type="text" placeholder="Buscar Veículos" name="busca_veiculo" class="form-control">
            </div>
            <div class="veiculos-rota-manual"></div>
        </div>
    </div>
</div>
@stop
