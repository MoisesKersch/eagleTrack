@extends('layouts.eagle')
@section('title')
Linhas @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li  ><a href="{{url('painel')}}">Painel</a></li>
    <li class="active"><a>Linhas</a></li>
</ul>
<div class="page-title">
    <h2>
        <span class="flaticon-icon045"></span> Linhas
        @if(\App\Helpers\AcessoHelper::acessosPermissao('cadlinhas','ppcadastrar'))
            <a href="{{ url('painel/coletivos/cadastros/linhas/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>Nova linha</a>
        @endif
    </h2>
</div>
@if(\App\Helpers\AcessoHelper::acessosPermissao('cadlinhas','ppvisualizar'))
    <div id="ppeditar" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadlinhas','ppeditar')}}"></div>
    <div id="ppexcluir" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadlinhas','ppexcluir')}}"></div>
    <div class="page-content-wrap">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                        <div class="col-sm-3">
                            <span> Selecione as empresas</span>
                            <select id="selectCliListLinhas" class="col-sm-12 form-control select-selecionar-todos" multiple name="cliente">
                                @foreach($clientes as $cliente)
                                    @if(\Auth::user()->cliente->clcodigo == $cliente->clcodigo || \Auth::user()->usumaster != 'S')
                                        <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clfantasia }}</option>
                                    @else
                                        <option value="{{ $cliente->clcodigo }}">{{ $cliente->clfantasia }}</option>
                                    @endif
                                @endforeach
                            </select>
                      </div>

                    <table id="tb_linhas" class="table">
                        <thead>
                            <tr>
                              <th>Descrição</th>
                              <th>Cliente</th>
                              <th>AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endif
@stop
