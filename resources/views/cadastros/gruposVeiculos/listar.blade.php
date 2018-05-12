@extends('layouts.eagle')
@section('title')
Listagem dos grupos de veículos @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{ url('painel') }}">Painel</a></li>
    <li class="active"><a>Grupos de veículos</a></li>
</ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon014"></span> Listagem dos grupos de veículos
            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadgrupoveiculos','ppcadastrar'))
                <a href="{{ url('painel/cadastros/gruposVeiculos/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>Novo Grupo</a>
            @endif
        </h2>
    </div>
    @if(\App\Helpers\AcessoHelper::acessosPermissao('cadgrupoveiculos','ppvisualizar'))
        <div id="ppeditar" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadgrupoveiculos','ppeditar')}}"></div>
        <div id="ppexcluir" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadgrupoveiculos','ppexcluir')}}"></div>
        <div id="listCadastroGrupoVeiculos" class="page-content-wrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-md-6 col-xs-12 form-group">
                                <span class="label-botoes-table">Selecione as empresas</span>
                                <select multiple class="col-sm-12 select-selecionar-todos buscar-clientes-gv" name="clientesbusca">
                                    <option value="T">Selecionar todos</option>
                                    @foreach($clientes as $cliente)
                                        <option {{ $adm ? '' : 'selected' }} value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <table id="table-grupo-veiculo" class="table">
                                    <thead>
                                        <tr>
                                            <th class="gv-col-detalhes" >Detalhes</th>
                                            <th>Descrição</th>
                                            <th>Empresa</th>
                                            <th>Ações</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop
