@extends('layouts.eagle')
@section('title')
Listagem dos grupos de motoristas @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a>Grupos de Motoristas</a></li>
    </ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon009"></span> Listagem dos grupos de motoristas
            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadgrupomotoristas','ppcadastrar'))
                <a href="{{ url('painel/cadastros/gruposMotoristas/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>Novo Grupo</a>
            @endif
        </h2>
    </div>
    @if(\App\Helpers\AcessoHelper::acessosPermissao('cadgrupomotoristas','ppvisualizar'))
        <div id="ppeditar" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadgrupomotoristas','ppeditar')}}"></div>
        <div id="ppexcluir" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadgrupomotoristas','ppexcluir')}}"></div>
        <div class="page-content-wrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-md-6 col-xs-12 form-group">
                                <span class="label-botoes-table">Selecione as empresas</span>
                                <select multiple class="col-sm-12 select-selecionar-todos buscar-clientes-gm" name="clientesbusca">
                                    <option value="T">Selecionar todos</option>
                                    @foreach($clientes as $cliente)
                                        <option {{ $adm ? '' : 'selected' }} value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <span class="label-botoes-table">Status</span>
                                <div class="btn-group btn-group-altera-status">
                                    <button type="button" value="ativo"
                                        class="btn {{ $status=='ativo' ? 'btn-primary' : 'btn-default' }} bt-filtro-alterar grumot-status bt-filtros-update">Ativos</button>
                                    <button type="button" value="inativo"
                                        class="btn {{ $status=='inativo' ? 'btn-primary' : 'btn-default' }} bt-filtro-alterar grumot-status bt-filtros-update">Inativos</button>
                                    <button type="button" value="todos" class="btn {{ $status=='todos' ? 'btn-primary' : 'btn-default' }}
                                        {{ $status== null ? 'btn-primary' : 'btn-default' }} bt-filtro-alterar grumot-status bt-filtros-update">Todos</button>
                                </div>
                            </div>
                            <table id="table-grupo-motorista" class="table datatable-col-3">
                                <thead>
                                    <tr>
                                        <th>Descrição</th>
                                        <th>Empresa</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grupos as $grupo)
                                        <tr>
                                            <td>{{ $grupo->gmdescricao }}</td>
                                            <td>{{ $grupo->clienteGm->clnome }}</td>
                                            <td>
                                                @if(\App\Helpers\AcessoHelper::acessosPermissao('cadgrupomotoristas','ppexcluir'))
                                                    @if ($grupo->gmstatus == 'A')
                                                        <a title="Desativar Grupo" class="btDelModal btn btn-danger desativar-cadastros {{ $grupo->gmstatus == 'I' ? 'hidden' : '' }}" data-toggle="modal" data-target="#modalDelataDesativa" data-delete-action="{!! url('/painel/cadastros/gruposMotoristas/desativar/'.$grupo->gmcodigo) !!}">
                                                            <span class="fa fa-ban"></span>
                                                        </a>
                                                    @else
                                                        <a href='#' title="Ativar Grupo" data-url="{{ url('painel/cadastros/gruposMotoristas/ativar') }}" data-id="{{ $grupo->gmcodigo }}" class="btn {{ $grupo->gmstatus == 'A' ? 'hidden' : '' }} btn-success ativar-cadastros">
                                                            <span class="fa fa-check"></span>
                                                        </a>
                                                    @endif
                                                @endif
                                                @if(\App\Helpers\AcessoHelper::acessosPermissao('cadgrupomotoristas','ppeditar'))
                                                    <a title="Editar Grupo" class="btn btn-info" href="{{url('painel/cadastros/gruposMotoristas/editar/'.$grupo->gmcodigo)}}"><span class="fa fa-pencil"></span></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop
