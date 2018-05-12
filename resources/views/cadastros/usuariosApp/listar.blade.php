@extends('layouts.eagle')
@section('title')
Listagem de usuários app @parent
@stop

@section('content')

    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a>Usuário aplicativo</a></li>
    </ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon001"></span> Listagem de usuários app
            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadusuarioapp','ppcadastrar'))
                <a href="{{ url('painel/cadastros/usuarios/app/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>Novo usuário</a>
            @endif
        </h2>
    </div>
    @if(\App\Helpers\AcessoHelper::acessosPermissao('cadusuarioapp','ppvisualizar'))
      <div class="page-content-wrap">
          <div id="ppeditar" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadusuarioapp','ppeditar')}}" ></div>
          <div id="ppexcluir" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadusuarioapp','ppexcluir')}}" ></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-sm-5">
                                <span class="label-botoes-table">Selecione as empresas</span>
                                <select id="selUsuaApp" multiple class="col-sm-12 select-selecionar-todos" name="cliente_usapp[]">
                                    <option value="0">Selecionar todos</option>
                                        @foreach($clientes as $cliente)
                                            @if(\Auth::user()->usumaster == 'S')
                                                <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                            @else
                                                <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                            @endif
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <div class="filtros-cliente">
                                    <span class="label-botoes-table">Ativos/Inativos</span>
                                    <div class="btn-group">
                                      <a data-f="statusUsuApp" data-url="{{ url('painel/cadastros/usuarios/app/status') }}" data-val="A" class="btn btn-primary bt-filtros-usuapp bt-filtros-update">Ativos</a>
                                      <a data-f="statusUsuApp" data-url="{{ url('painel/cadastros/usuarios/app/status') }}" data-val="I" class="btn btn-default bt-filtros-usuapp bt-filtros-update">Inativos</a>
                                      <a data-f="statusUsuApp" data-url="{{ url('painel/cadastros/usuarios/app/status') }}" data-val="T" class="btn btn-default bt-filtros-usuapp bt-filtros-update">Todos</a>
                                    </div>
                                </div>
                            </div>
                            <table id="tableCadUsuApp" class="table datatable">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nome</th>
                                        <th>Perfil</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif
@stop
