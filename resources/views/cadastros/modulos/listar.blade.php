@extends('layouts.eagle')
@section('title')
Listagem de modulos @parent
@stop
@section('content')

    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a>Módulos</a></li>
    </ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon006"></span> Listagem de módulos
            <a href="{{ url('painel/cadastros/modulos/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>Novo módulo</a>
        </h2>
    </div>
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Módulos</h3>
                    </div>
                    <div class="panel-body">
                        <div class="">

                            <div class="select-clientes col-md-4 col-xs-12">
                                <span class="label-botoes-table">Selecione as empresas</span>
                                <select multiple class="form-control clientesbusca" name="clientesbusca">
                                    @foreach($clientes as $cliente)
                                        @if(count($clientes) == 1)
                                            <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                        @else
                                            <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 col-xs-12">
                                <span class="label-botoes-table">Chip</span>
                                <div class="btn-group">
                                    <a class="btn btn-default  filtros_modulo " id="com_chip">Com Chip</a>
                                    <a class="btn btn-default  filtros_modulo " id="sem_chip">Sem Chip</a>
                                    <a class="btn btn-primary  filtros_modulo " id="todos_chip">Todos</a>
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-12">
                                <span class="label-botoes-table">Status</span>
                                <div class="btn-group">
                                    <a class="btn btn-default fl_mo_st" id="at_chip">Ativos</a>
                                    <a class="btn btn-default fl_mo_st" id="in_chip">Inativos</a>
                                    <a class="btn btn-primary fl_mo_st" id="td_chip">Todos</a>
                                </div>
                            </div>

                            <input type="hidden" id="status" value="" />
                            <input type="hidden" id="chip" value="" />

                        </div>
                        <div class=" col-xs-12">
                            <table id="modulosTable" class=" table col-xs-12 datatable">
                                <thead>
                                    <tr>
                                        <th>SERIAL</th>
                                        <th>IMEI</th>
                                        <th>CHIP</th>
                                        <th>MODELO</th>
                                        <th>PROPRIETÁRIO</th>
                                        <th>AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyModulosTable" >
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop
