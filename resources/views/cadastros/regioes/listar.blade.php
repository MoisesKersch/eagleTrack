@extends('layouts.eagle')
@section('title')
Controle de regiões @parent
@stop
@section('content')
<ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a>Regiões</a></li>
</ul>
<div id="divCadastroRegiao">
    <div class="page-title">
        <h2>
            <span class="flaticon-icon040"></span> Controle de regiões
            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadregioes','ppcadastrar'))
                <button id="buttonNovaRegiao" class="btn btn-info disabled"><span class="glyphicon glyphicon-plus"></span>Nova Região</button>
            @endif
        </h2>
    </div>
    @if(\App\Helpers\AcessoHelper::acessosPermissao('cadregioes','ppvisualizar'))
        <div id="ppeditar" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadregioes','ppeditar')}}"></div>
        <div id="ppexcluir" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadregioes','ppexcluir')}}"></div>
        <div class="page-content-wrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <section id="sectionFiltrosRegiao">
                                <div class="col-md-4 col-xs-12 form-group">
                                    <span class="label-botoes-table">Selecione a empresa</span>
                                    <select class="col-sm-12 select-selecionar-todos" name="clientesbusca">
                                        @foreach($clientes as $cliente)
                                            <option {{ count($clientes) == 1 || $codcliente == $cliente->clcodigo ? 'selected' : '' }} value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </section>
                            <section id="sectionNovaRegiao" class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label>Descrição*</label>
                                        <input type="text" name="redescricao" class="form-control" placeholder="Ex.: Área escolar">
                                    </div>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label>Velocidade</label>
                                    <input type="number" min="0" name="revelocidade" class="form-control inteiro-positivo " placeholder="Km/h" maxlength="3">
                                </div>
                                <div class="col-md-2 form-group">
                                    <label>Cor</label>
                                    <input type="color" name="recor" class="form-control">
                                </div>
                                <div class="col-md-8 form-group">
                                    <label>Empresa*</label>
                                    <select class="col-md-12 select-selecionar-todos" name="recliente" style="width: 100%">
                                        @foreach($clientes as $cliente)
                                            <option {{ count($clientes) == 1 || $codcliente == $cliente->clcodigo ? 'selected' : '' }} value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" id="longitudeClienteRegiao" val="{{ $clientes[array_search($codcliente, $clientes->toArray())]->cllatitude }}">
                                <input type="hidden" id="latitudeClienteRegiao" val="{{ $clientes[array_search($codcliente, $clientes->toArray())]->cllatitude }}">
                            </section>
                            <div class="col-xs-12 col-sm-12 divMapaRegioes">
                                <hr>
                                <label>Listagem das regiões</label>
                                <div class="mapa-regioes">
                                    <div id="mapaRegiao"></div>
                                </div>
                            </div>
                            <section id="sectionNovaRegiaoSalvar" class="col-md-12">
                                <div class="block-salvar">
                                    <div class="col-sm-12">
                                        <button id="salvarRegiao" type="button" value="save" class="btn salvar btn-lg btn-primary">
                                            <span class="glyphicon glyphicon-ok"></span>
                                            Salvar
                                        </button>
                                        <button id="cancelarRegiao" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-remove"></span>Cancelar</button>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@stop
