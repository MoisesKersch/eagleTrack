@extends('layouts.eagle')
@section('title')
Relatório de acionamento de portas @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active">Abertura de portas</li>
</ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon029"></span> Acionamento de Portas
        </h2>
    </div>
    <div class="page-content-wrap" id="tempoParado">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-acionamento-porta" action="{{ url('painel/relatorios/acionamentoPortas/relatorio') }}" type="POST">
                            <div>
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
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-9">
                                    <div class="form-group">
                                        <div class="col-xs-12 col-sm-5 col-md-5">
                                            <div class="h4">Selecione as empresas</div>
                                                <select data-id="tempoParadoVeiculo" data-url="{{ url('painel/cadastros/veiculos/veiculo') }}" multiple class="form-control select-cliente select-selecionar-todos" name="clientes[]">
                                                    @if(count($clientes) == 1)
                                                        <option selected value="{{ $clientes[0]->clcodigo }}">{{ $clientes[0]->clnome }}</option>
                                                    @else
                                                        <option value="0">Selecionar todos</option>
                                                        @foreach($clientes as $cliente)
                                                            <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                        </div>
                                        <div class="col-xs-12 col-sm-4 col-md-4">
                                            <div class="h4">Selecione os Veículos</div>
                                            <select data-form=".form-parado" id="tempoParadoVeiculo" multiple class="form-control tempo-buscar-acp select-selecionar-todos-veiculos" name="buscar[]">
                                            </select>
                                        </div>

                                        <div class="h4">Portas</div>
                                        <input type="hidden" name="portas" class="input-portas">
                                        <a href="#" data-form=".form-parado" class="btn btn-default selecao-portas" data-value="13,14">1</a>

                                        <a href="#" data-form=".form-parado" class="btn btn-default selecao-portas" data-value="15,16">2</a>

                                        <a href="#" data-form=".form-parado" class="btn btn-default selecao-portas" data-value="16,17">3</a>

                                        <a href="#" data-form=".form-parado" class="btn btn-default selecao-portas" data-value="18,19">4</a>
                                    </div>
                                </div>
                        </div>
                    </form>
                    <div class="col-xs-12 cabecalho-exportacoes">
                            <div class="hidden-xs col-sm-10 btn-group">
                                    <button disabled type="button" data-type="pdf" class="btn btn-lg btn-default exportar-acionamento-portas"><span class='fa fa-save'></span>PDF</a>
                                    <button disabled type="button" data-type="xls" class="btn btn-lg btn-default exportar-acionamento-portas"><span class='fa fa-save'></span>Excel</button>
                                    <button disabled type="button" data-type="csv" class="btn btn-lg btn-default exportar-acionamento-portas"><span class='fa fa-save'></span>CSV</button>
                                    <button type="button" class="btn btn-lg btn-default btn-imprimir"><span class="glyphicon glyphicon-print"></span>Imprimir</button>
                            </div>
                            <div class="col-xs-12 col-sm-2">
                                <a disabled  id="gerarRelatorioAcionamentoPortas" class="col-xs-12 btn btn-lg btn-info bt_gerar_relatorio"><span class="glyphicon glyphicon-thumbs-up"></span>Gerar</a>
                            </div>
                    </div>
                        <div class="divImprimir">
                            <table id="tableAcionamentoPortas" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Porta</th>
                                        <th>Hora início</th>
                                        <th>Hora fim</th>
                                        <th>Tempo</th>
                                        <th>Endereço</th>
                                        <th>Local</th>
                                        <th class="hidden-print">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tableAcionamentoPortasBody">
                               </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop
