@extends('layouts.eagle')
@section('title')
Histórico de Posições @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active">Histórico de posições</li>
</ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon030"></span> Histórico de Posições
        </h2>
    </div>
    <div class="page-content-wrap" id="relHistoricoPosicoes">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                            <div class="col-sm-12">
                                <div class="col-sm-2">
                                    <div class="">
                                        <!-- <div class="col-sm-12"> -->
                                            <label for="">Data Inicial</label>
                                            <input id="inputDataInicio" class="form-control data-data" value="{{ date('d/m/Y') }}" type="text" name="data_inicio">
                                        <!-- </div> -->
                                    </div>
                                    <div class="">
                                         <!-- <div class="col-sm-12"> -->
                                            <label for="">Data Final</label>
                                            <input id="inputDataFim" class="form-control data-data" value="{{ date('d/m/Y') }}" type="text" name="data_fim">
                                        <!-- </div> -->
                                    </div>

                                </div>
                                <div class="col-sm-5">
                                    <div class='h4'>Selecione as empresas</div>
                                    @if(count($empresas) == 1)
                                        <select id="selectClienteHistoricoPosicoes" multiple class="form-control" name="empresas" disabled>
                                    @else
                                        <select id="selectClienteHistoricoPosicoes" multiple class="form-control" name="empresas">
                                    @endif
                                        @foreach($empresas as $empresa)
                                            @if(Auth::user()->usumaster == 'S')
                                                <option value="{{ $empresa->clcodigo }}">{{ $empresa->clnome }}</option>
                                            @else
                                                <option selected value="{{ $empresa->clcodigo }}">{{ $empresa->clnome }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <div class='h4'>Veículos</div>
                                    <select id="selectVeiculosHistoricoPosicoes" multiple class="form-control select-selecionar-todos" name="veiculos">
                                        <option value="" >Selecionar Todos</option>
                                        @if(Auth::user()->usumaster == 'N')
                                            @foreach($veiculos as $veiculo)
                                                @if(count($veiculos) == 1)
                                                    <option selected value="{{ $veiculo->vecodigo }}">{{ $veiculo->veplaca }} | {{ $veiculo->veprefixo }}</option>
                                                @else
                                                    <option value="{{ $veiculo->vecodigo }}">{{ $veiculo->veplaca }} | {{ $veiculo->veprefixo }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <div class='h4'>Grupo Motorista</div>
                                    @if(count($grpMotoristas) == 0)
                                        <select id="selectGrpMotoristasHistoricoPosicoes" multiple class="form-control" name="grupos" disabled>
                                    @else
                                        <select id="selectGrpMotoristasHistoricoPosicoes" multiple class="form-control" name="grupos" >
                                    @endif
                                        @if(Auth::user()->usumaster == 'N')
                                            @foreach($grpMotoristas as $grpMotorista)
                                                <option value="{{ $grpMotorista->gmcodigo }}">{{ $grpMotorista->gmdescricao }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 cabecalho-exportacoes">
                                 <div class="hidden-xs col-sm-10">
                                    <div class="btn-group">
                                        <a disabled data-type="pdf" class="btn btn-lg btn-default exportar-historico"><span class="fa fa-save"></span>PDF</a>
                                        <a disabled data-type="xls" class="btn btn-lg btn-default exportar-historico"><span class="fa fa-save"></span>Excel</a>
                                        <a disabled data-type="csv" class="btn btn-lg btn-default exportar-historico"><span class="fa fa-save"></span>CSV</a>
                                        <a class="btn btn-lg btn-default btn-imprimir"><span class="glyphicon glyphicon-print"></span>Imprimir</a>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <a id="btnGerarHistoricoPosicoes" href="#" class="col-xs-12 btn btn-lg btn-info"><span class="glyphicon glyphicon-thumbs-up"></span>Gerar</a>
                                </div>
                            </div>
                        <div class="col-sm-12 divImprimir">
                            <table id="relatorioHistoricoPosicoes" class="table table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th>Hora</th>
                                        <th>Tempo</th>
                                        <th>Evento</th>
                                        <th>Endereço</th>
                                        <th>Cidade/UF</th>
                                        <th>Ponto</th>
                                        <th class="hidden-print">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="corpoTabelaHistoricoPosicoes">
                                    <tr>
                                        <td colspan="7"><span style="margin-top: 0px;" class="alert alert-info">Selecione ao menos um veiculo ou grupo para gerar a listagem.</span></td>
                                    </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop
