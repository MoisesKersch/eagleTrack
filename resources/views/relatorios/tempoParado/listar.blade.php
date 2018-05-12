@extends('layouts.eagle')
@section('title')
Relatório tempo parado @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active">Tempo parado</li>
</ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon017"></span> Tempo parado
        </h2>
    </div>
    <div class="page-content-wrap" id="tempoParado">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-parado" action="{{ url('painel/relatorios/tempo/parado') }}" type="POST">
                            <div class="col-xs-12">
                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-3">
                                    <div class="form-group">
                                        <div class="col-sm-6 col-md-12 col-lg-6">
                                                <div class="h4">Data inicial</div>
                                                <input data-form=".form-parado" class="form-control data-data tempo-data-inicio" value="{{ date('d/m/Y') }}" id="" type="text" name="data_inicio">
                                        </div>
                                        <div class="col-sm-6 col-md-12 col-lg-6">
                                                <div class="h4">Data Final</div>
                                                <input data-form=".form-parado" class="form-control tempo-data-fim  data-data" value="{{ date('d/m/Y') }}" id="" type="text" name="data_fim">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12 col-md-12" style="margin-top: 10px;">
                                            <div class="h4">Ignição</div>
                                            <a href="#" data-form=".form-parado" class="btn btn-success tempo-opcoes tempo-check" data-att="tempo-ligado">Ligada</a>
                                            <input data-form=".form-parado" type="hidden" name="ligado" class="tempo-ligado" value="off">
                                            <input data-form=".form-parado" type="hidden" name="desligado" class="tempo-desligado" value="off">
                                            <a href="#" data-form=".form-parado" class="btn btn-success tempo-opcoes tempo-check" data-att="tempo-desligado">Desligada</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                            <div class="h4">Selecione as empresas</div>
                                            @if(count($clientes) == 1)
                                                <select disabled data-id="tempoParadoVeiculo" data-url="{{ url('painel/cadastros/veiculos/veiculo') }}" multiple class="form-control select-cliente select-selecionar-todos" name="clientes[]">
                                            @else
                                                <select data-id="tempoParadoVeiculo" data-url="{{ url('painel/cadastros/veiculos/veiculo') }}" multiple class="form-control select-cliente select-selecionar-todos" name="clientes[]">
                                            @endif
                                                <option value="0">Selecionar todos</option>
                                                @foreach($clientes as $cliente)
                                                    @if(count($clientes) == 1)
                                                        <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                                    @else
                                                        <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-6">
                                            <div class="h4">Selecione os Veículos</div>
                                            <select data-form=".form-parado" id="tempoParadoVeiculo" multiple class="form-control tempo-buscar select-selecionar-todos-veiculos" name="buscar[]">
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <div class="col-sm-12 col-md-12">
                                            <div class="h4">Tempo Parado Maior Que</div>
                                            <input data-form=".form-parado" type="hidden" name="inp_time_parado" class="inp-time-parado" value="0">
                                            <a data-time="1" class="time-parado btn btn-primary ">1 min</a>
                                            <a data-time="5" class="time-parado btn btn-primary ">5 min</a>
                                            <a data-time="10" class="time-parado btn btn-primary ">10 min</a>
                                            <a data-time="15" class="time-parado btn btn-primary ">15 min</a>
                                            <a data-time="30" class="time-parado btn btn-primary ">30 min</a>
                                            <a data-time="60" class="time-parado btn btn-primary ">60 min</a>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </form>
                    <div class="col-xs-12 cabecalho-exportacoes">
                            <div class="hidden-xs col-sm-10 btn-group">
                                    <button disabled type="button" data-type="pdf" class="btn btn-lg btn-default exportar-parado"><span class='fa fa-save'></span>PDF</button>
                                    <button disabled type="button" data-type="xls" class="btn btn-lg btn-default exportar-parado"><span class='fa fa-save'></span>Excel</button>
                                    <button disabled type="button" data-type="csv" class="btn btn-lg btn-default exportar-parado"><span class='fa fa-save'></span>CSV</button>
                                    <button type="button" class="btn btn-lg btn-default btn-imprimir"><span class="glyphicon glyphicon-print"> Imprimir</span></button>
                            </div>
                            <div class="col-xs-12 col-sm-2">
                                <a disabled  id="gerarRelatorioTempoParado" class="col-xs-12 btn btn-lg btn-info bt_gerar_relatorio"><span class="glyphicon glyphicon-thumbs-up"></span>Gerar</a>
                            </div>
                    </div>
                        <div class="divImprimir">
                            <table id="tableTempoParado" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Hora Início</th>
                                        <th>Hora Fim</th>
                                        <th>Tempo parado</th>
                                        <th>Endereço</th>
                                        <th>Ponto</th>
                                        <th>Região</th>
                                        <th>Ignição</th>
                                        <th class="hidden-print">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tableTempoParadoBody">
                               </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop
