@extends('layouts.eagle')
@section('title')
Relatório jornada de trabalho @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active">Jornada de trabalho</li>
</ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon007"></span> Jornada de trabalho
        </h2>
    </div>
    <div class="page-content-wrap" id="jornadaTrabalho">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-jornada" action="{{ url('painel/relatorios/jornada/trabalho') }}" type="POST">
                            <div class="col-sm-12">
                                <div class="col-sm-4">
                                    <div class="col-sm-6">
                                        <div class="col-sm-12 block-data">
                                            <label for="">Início data</label>
                                            <input data-form=".form-parado" class="form-control data-data jornada-trabalho tempo-data-inicio" value="{{ date('d/m/Y') }}" id="" type="text" name="data_inicio">
                                        </div>
                                   </div>
                                    <div class="col-sm-6">
                                         <div class="col-sm-12 block-data">
                                            <label for="">Fim data</label>
                                            <input data-form=".form-parado" class="form-control tempo-data-fim jornada-trabalho data-data" value="{{ date('d/m/Y') }}" id="" type="text" name="data_fim">
                                        </div>
                                   </div>
                               </div>
                               <div class="col-sm-4">
                                    <div class="col-sm-12">
                                        <label for="">Selecione as empresas</label>
                                        <select data-form=".form-parado" multiple class="form-control jornada-trabalho select-cliente-jornada" name="clientes[]">
                                            <option class="todos-jornada" value="00">Selecionar todos</option>
                                            @foreach($clientes as $cliente)
                                                @if(count($clientes) == 1){
                                                    <option value="{{ $cliente->clcodigo }}" selected>{{ $cliente->clnome }}</option>
                                                @else
                                                    <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="col-sm-12 motoristas-jornada-relatorio">
                                        <label for="">Selecione os Motoristas</label>
                                        <select data-form=".form-parado" id="" multiple class="form-control tempo-buscar jornada-motorista jornada-trabalho" name="buscar[]">
                                        </select>
                                    </div>
                                </div>
                            </form>
                            </div>
                            <div class="col-xs-12">
                           <div class="col-xs-12 cabecalho-exportacoes">
                               <div class="col-xs-hidden col-sm-10 btn-group">
                                    <button disabled class="btn btn-default btn-lg exportar-jornada" data-type="pdf" ><span class="fa fa-save"></span>PDF</a>
                                    <button disabled class="btn btn-default btn-lg exportar-jornada" data-type="xls" ><span class="fa fa-save"></span>Excel</button>
                                    <button disabled class="btn btn-default btn-lg exportar-jornada" data-type="csv" ><span class="fa fa-save"></span>CSV</button>
                                    <button class="btn btn-default btn-lg btn-imprimir" ><span class="glyphicon glyphicon-print"></span>Imprimir</button>
                               </div>
                               <div class="col-xs-12 col-sm-2">
                                    <a disabled  id="gerarRelatorioJornadaTrabalho" class=" btn btn-info"><span class="glyphicon glyphicon-thumbs-up"></span>Gerar</a>
                               </div>
                           </div>
                       </div>
                            <div class='divImprimir'>
                                <table id="relatorioJornadaTable" class="table">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Semana</th>
                                            <th>Trabalhadas</th>
                                            <th>Falta</th>
                                            <th>Extra</th>
                                            <th>Extra 100%</th>
                                            <th>Ad.Noturno</th>
                                            <th>Extra Noturno</th>
                                            <th>Hora Espera</th>
                                            <th>Int.Refeição</th>
                                            <th>Total</th>
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
