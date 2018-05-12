@extends('layouts.eagle')
@section('title')
Listagem de clientes @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a>Clientes</a></li>
    </ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon019"></span> Listagem de clientes
            <a href="{{ url('painel/cadastros/clientes/cadastrar') }}" class="btn btn-info">
                <span class="glyphicon glyphicon-plus"></span>
                Novo cliente
            </a>
        </h2>
    </div>

    <div class="page-content-wrap" id="listarClientes">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="filtros-cliente">
                            <span class="label-botoes-table">Tipo de Pessoa</span>
                            <div class="btn-group">
                                <a data-val="F" class="btn btn-default bt-tipo-pessoa">Física</a>
                                <a data-val="J" class="btn btn-default bt-tipo-pessoa">Jurídica</a>
                                <a data-val="T" class="btn btn-primary bt-tipo-pessoa">Todos</a>
                            </div>
                        </div>
                        <div class="filtros-cliente">
                            <span class="label-botoes-table">Ativos/Inativos</span>
                            <div class="btn-group">
                              <a data-val="A" class="btn btn-primary bt-filtros-clientes bt-filtros-update">Ativos</a>
                              <a data-val="I" class="btn btn-default bt-filtros-clientes bt-filtros-update">Inativos</a>
                              <a data-val="T" class="btn btn-default bt-filtros-clientes bt-filtros-update">Todos</a>
                            </div>
                        </div>
                        <table id="tableListarClientes" class="hover table table-cliente">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Cidade</th>
                                    <th>Endereço</th>
                                    <th>Telefone</th>
                                    <th>E-mail</th>
                                    <th class="th-acoes">Ações</th>
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
@stop
