@extends('layouts.eagle')
@section('title')
Tempo de funcionamento @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active">Tempo de funcionamento</li>
</ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon048"></span> Tempo de funcionamento
        </h2>
    </div>
    <div class="page-content-wrap" id="divTempoFuncionamento">
        <div class="row">            
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form id="formTempoFuncionamento" action="{{ url('painel/relatorios/tempo/funcionamento') }}" type="POST">
                            <div class="col-sm-12">
                                <div class="col-xs-12 col-sm-3">
                                    <div class="col-sm-12 col-md-12 col-lg-6">
                                        <div class="h4">Data Início</div>
                                        <input class="form-control data-data tempo-funcionamento funcionamento-data-inicio" value="{{ date('d/m/Y') }}" id="" type="text" name="data_inicio">
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6">
                                        <div class="h4">Data Fim</div>
                                        <input class="form-control funcionamento-data-fim tempo-funcionamento data-data" value="{{ date('d/m/Y') }}" id="" type="text" name="data_fim">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-9">
                                    <div class="col-sm-7">
                                        <div class="h4">Selecione as empresas</div>
                                        @if(count($clientes) == 1)
                                            <select disabled  data-id="selectTempoFuncionamento" data-url="{{ url('painel/cadastros/veiculos/veiculo') }}" multiple class="form-control select-cliente select-cliente-funcionamento select-selecionar-todos" name="clientes[]">
                                        @else
                                            <select  data-id="selectTempoFuncionamento" data-url="{{ url('painel/cadastros/veiculos/veiculo') }}" multiple class="form-control select-cliente select-cliente-funcionamento select-selecionar-todos" name="clientes[]">
                                                <option value="0">Selecionar todos</option>
                                        @endif
                                        @foreach($clientes as $cliente)
                                            @if(count($clientes) == 1)
                                                <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                            @else
                                                <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                            @endif
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="h4">Veiculos</div>
                                        <select id="selectTempoFuncionamento" multiple class="form-control funcionamento-buscar tempo-funcionamento select-selecionar-todos-veiculos" name="buscar[]">
                                        @if(Auth::user()->usumaster == 'N')
                                            <option value="0">Todos</option>
                                            @foreach($veiculos as $veiculo)
                                                <option value="{{$veiculo->vecodigo}}">{{$veiculo->veplaca}}</option>
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </form>
                        <div class="col-xs-12">
                           <div class="col-xs-12 cabecalho-exportacoes">
                               <div class="col-xs-hidden col-sm-10 btn-group">
                                    <button disabled class="btn btn-default btn-lg exportar-tempoFuncionamento" data-type="pdf" ><span class="fa fa-save"></span>PDF</button>
                                    <button disabled class="btn btn-default btn-lg exportar-tempoFuncionamento" data-type="xls" ><span class="fa fa-save"></span>Excel</button>
                                    <button disabled class="btn btn-default btn-lg exportar-tempoFuncionamento" data-type="csv" ><span class="fa fa-save"></span>CSV</button>
                                    <button class="btn btn-default btn-lg btn-imprimir" ><span class="glyphicon glyphicon-print"></span>Imprimir</button>
                               </div>
                               <div class="col-xs-12 col-sm-2">
                                   <button class="col-xs-12 btn btn-info btn-lg" id="gerarRelatorioTempoFuncionamento" disabled><span class="glyphicon glyphicon-thumbs-up"></span>Gerar</button>
                               </div>
                           </div>
                       </div>
                       <div class="divImprimir">
                            <div class="h3 visible-print">Relatório de Funcionamento</div>
                            <table id="tempoIgnicaoLigada" class="table table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th>Ligou</th>
                                        <th>Desligou</th>
                                        <th>Tempo</th>
                                        <th>Ponto</th>
                                        <th>Referencia(Mapa)</th>
                                    </tr>
                                </thead>
                                <tbody id="tableTempoFuncionamentoBody">
                               </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
