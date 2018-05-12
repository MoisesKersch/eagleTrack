@extends('layouts.eagle')
@section('title')
Relatório Controle de Horarios @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active">Controle do horário</li>
</ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon017"></span> Controle de horário
        </h2>
    </div>
    <div class="page-content-wrap" id="controleHorarios">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-controle-horarios" action="{{ url('painel/relatorios/controle/horario/relatorio') }}" type="POST">
                            <div class="col-sm-12">
                                <div class="col-sm-4">
                                    <div class="col-sm-6">
                                        <div class="col-sm-12 block-data">
                                            <h5 for="">Início data</h5>
                                            <input data-form=".form-controle-horarios" class="form-control data-data relatorio-controle-horas horario-data-inicio" value="{{ date('d/m/Y') }}" id="" type="text" name="data_inicio">
                                        </div>
                                   </div>
                                    <div class="col-sm-6">
                                         <div class="col-sm-12 block-data">
                                            <h5 for="">Fim data</h5>
                                            <input data-form=".form-controle-horarios" class="form-control tempo-data-fim relatorio-controle-horas data-data" value="{{ date('d/m/Y') }}" id="" type="text" name="data_fim">
                                        </div>
                                   </div>
                               </div>
                               <div class="col-sm-8">
                                    <div class="col-sm-4">
                                        <h5 for="">Selecione as Empresas</h5>
                                        <!-- <select data-form=".form-parado" multiple class="form-control select-cliente-horario jornada-trabalho" name="buscar[]"> -->
                                        <select data-form=".form-controle-horarios" id="clientesControleHorarios" multiple class="form-control select-selecionar-todos" name="buscar[]">
                                            <option value="0">Selecionar todos</option>
                                            @foreach($clientes as $cliente)
                                                @if(count($clientes) == 1){
                                                    <option value="{{ $cliente->clcodigo }}" selected>{{ $cliente->clnome }}</option>
                                                @else
                                                    <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <h5 for="">Selecione os Veículos</h5>
                                        <select data-form=".form-controle-horarios" id="veiculosHorarioControle" multiple class="form-control select-selecionar-todos relatorio-controle-horas" name="veiculos[]">
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <h5 for="">Selecione os Motoristas</h5>
                                        <select data-form=".form-controle-horarios" id="motoristasHorarioControle" multiple class="form-control select-selecionar-todos relatorio-controle-horas" name="motoristas[]">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-sm-12 cabecalho-exportacoes">
                             <div class="hidden-xs col-sm-10">
                                <div class="btn-group">
                                    <a disabled data-type="pdf" class="btn btn-lg btn-default exportar-cont-horario"><span class="fa fa-save"></span>PDF</a>
                                    <a disabled data-type="xls" class="btn btn-lg btn-default exportar-cont-horario"><span class="fa fa-save"></span>Excel</a>
                                    <a disabled data-type="csv" class="btn btn-lg btn-default exportar-cont-horario"><span class="fa fa-save"></span>CSV</a>
                                    <a class="btn btn-lg btn-default btn-imprimir"><span class="glyphicon glyphicon-print"></span>Imprimir</a>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <a id="btnGerarControleHorario"  disabled class="col-xs-12 btn btn-lg btn-info"><span class="glyphicon glyphicon-thumbs-up"></span>Gerar</a>
                            </div>
                        </div>
                        <div class="col-sm-12 divImprimir">
                            <table id="tableControleHorario" class="table table-horarios table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th>Início</th>
                                        <th>Fim</th>
                                        <th>Tempo</th>
                                        <th>Código</th>
                                        <th>Evento</th>
                                        <th>Local</th>
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
@stop
