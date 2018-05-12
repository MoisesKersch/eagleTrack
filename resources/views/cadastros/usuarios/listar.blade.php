@extends('layouts.eagle')
@section('title')
Listagem de usuários @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li><a>Usuários</a></li>
    </ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon008"></span> Listagem de usuários
            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadusuarios','ppcadastrar'))
                <a href="{{ url('painel/cadastros/usuarios/cadastrar') }}" class="btn btn-info">
                    <span class="glyphicon glyphicon-plus"></span>
                    Novo usuário
                </a>
            @endif
        </h2>
    </div>
    @if(\App\Helpers\AcessoHelper::acessosPermissao('cadusuarios','ppvisualizar'))
        <div id="ppeditar" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadusuarios','ppeditar')}}"></div>
        <div id="ppexcluir" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadusuarios','ppexcluir')}}"></div>
      <div id="listarUsuarios" class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                            <div class="col-sm-5">
                                <span class="label-botoes-table">Selecione as empresas</span>
                                <select multiple class="col-sm-12 cliente-usuario" name="cliente_usuario">
                                        <option value="00">Selecionar todos</option>
                                        @if(count($clientes) != 1)
                                        @endif
                                    @foreach($clientes as $cliente)
                                        @if(count($clientes) == 1)
                                            <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                        @else
                                            <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <div class="filtros-cliente">
                                    <span class="label-botoes-table">Ativos/Inativos</span>
                                    <div class="btn-group">
                                      <a data-f="statusUsuApp" data-url="{{ url('painel/cadastros/usuarios/status') }}" data-val="S" class="btn btn-primary bt-filtros-usuario bt-filtros-update">Ativos</a>
                                      <a data-f="statusUsuApp" data-url="{{ url('painel/cadastros/usuarios/status') }}" data-val="N" class="btn btn-default bt-filtros-usuario bt-filtros-update">Inativos</a>
                                      <a data-f="statusUsuApp" data-url="{{ url('painel/cadastros/usuarios/status') }}" data-val="T" class="btn btn-default bt-filtros-usuario bt-filtros-update">Todos</a>
                                    </div>
                                </div>
                            </div>

                            <table id="tableListarUsuario" class="table datatable">
                                <thead>
                                    <tr>
                                        <th>Login</th>
                                        <th>email</th>
                                        <th>Cliente</th>
                                        <th>Ações</th>
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
    @endif
@stop
