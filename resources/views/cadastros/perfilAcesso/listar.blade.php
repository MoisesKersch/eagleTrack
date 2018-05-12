@extends('layouts.eagle')
@section('title')
Listagem de perfil de acesso @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a>Perfil de acesso</a></li>
    </ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon031"></span> Listagem de perfil de acesso
            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadperfilacesso','ppcadastrar'))
                <a href="{{ url('painel/cadastros/perfil/acesso/cadastrar') }}" class="btn btn-info">
                    <span class="glyphicon glyphicon-plus"></span>
                    Novo perfil de acesso
                </a>
            @endif
        </h2>
    </div>
    @if(\App\Helpers\AcessoHelper::acessosPermissao('cadperfilacesso','ppvisualizar'))
        <div id="ppeditar" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadperfilacesso','ppeditar')}}" ></div>
        <div id="ppexcluir" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadperfilacesso','ppexcluir')}}" ></div>
      <div id="listarPerfilAcesso" class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-sm-5">
                            <span class="label-botoes-table">Selecione as empresas</span>
                            <select multiple class="col-sm-12 cliente-usuario cliente-pe-listagem" name="cliente_usuario">
                                <option value="00">Selecionar todos</option>
                                @foreach($clientes as $cliente)
                                    @if(\Auth::user()->usumaster == 'S')
                                        <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @else
                                        <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3 col-xs-12">
                            <span class="label-botoes-table">Status</span>
                            <div class="btn-group">
                                <a class="btn btn-primary fl_perfil_st" id="at_perfil">Ativos</a>
                                <a class="btn btn-default fl_perfil_st" id="in_perfil">Inativos</a>
                                <a class="btn btn-default fl_perfil_st" id="td_perfil">Todos</a>
                                <input type="hidden" id="status_perfil" value="A" />
                            </div>
                        </div>

                        <table id="tablePerfisAcesso" class="table datatable">
                            <thead>
                                <tr>
                                    <th>Descrição</th>
                                    <th>Empresa</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody  id="tableBodyPerfisAcesso">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@stop
