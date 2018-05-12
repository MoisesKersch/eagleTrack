@extends('layouts.eagle')
@section('title')
Excesso de velocidade @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active">Excesso de velocidade</li>
</ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon033"></span> Excesso de velocidade
        </h2>
    </div>
    <div class="page-content-wrap" id="excessoVelocidade">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-excesso" action="{{ url('painel/relatorios/excesso/velocidade') }}" type="POST">
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <div class="col-sm-6">
                                        <div class="h4">Data Início</div>
                                        <input data-form=".form-excesso" class="form-control data-data excesso-data-inicio" value="{{ date('d/m/Y') }}" id="" type="text" name="data_inicio">
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="h4">Data Fim</div>
                                        <input data-form=".form-excesso" class="form-control excesso-data-fim data-data" value="{{ date('d/m/Y') }}" id="" type="text" name="data_fim">
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="h4">Velocidade acima de:</div>
                                        <input data-form=".form-excesso" class="form-control only-number" value="" id="" type="number" name="velocidade" >
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="h4">Selecione as empresas</div>
                                    @if(count($clientes) == 1)
                                        <select disabled data-form=".form-excesso" data-id="excessoVelocidadeVeiculo" data-url="{{ url('painel/cadastros/veiculos/veiculo') }}" multiple class="form-control excesso-buscar excesso-clientes select2-selecionar-todos" name="clientes[]">
                                    @else
                                        <select data-form=".form-excesso" data-id="excessoVelocidadeVeiculo" data-url="{{ url('painel/cadastros/veiculos/veiculo') }}" multiple class="form-control excesso-buscar excesso-clientes" name="clientes[]">
                                            <option class="todos-jornada" value="0">Selecionar todos</option>
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
                                <div class="col-sm-3">
                                    <div class="h4">Selecione os veículos</div>
                                    <select data-form=".form-excesso" id="excessoVelocidadeVeiculo" multiple class="form-control excesso-buscar select2-selecionar-todos" name="buscar[]">
                                        @if(Auth::user()->usumaster == 'N')
                                            <option value="0">Todos</option>
                                            @foreach($veiculos as $veiculo)
                                                <option value="{{$veiculo->vecodigo}}">{{$veiculo->veplaca}} | {{$veiculo->veprefixo}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <div class="h4">Grupos de motoristas</div>
                                    <select data-form=".form-excesso" id="excessoVelocidadeGm" multiple class="form-control excesso-gm select2-selecionar-todos" name="gm[]">
                                    @if (count($grupoMotorista) > 1)
                                        <option value="0">Todos</option>
                                    @endif
                                    @foreach($grupoMotorista as $gm)
                                        <option value="{{ $gm->gmcodigo }}">{{ $gm->gmdescricao }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                       <div class="col-xs-12">
                           <div class="col-xs-12 cabecalho-exportacoes">
                               <div class="col-xs-hidden col-sm-10 btn-group">
                                    <button disabled class="btn btn-default btn-lg exportar-excesso" data-type="pdf" ><span class="fa fa-save"></span>PDF</a>
                                    <button disabled class="btn btn-default btn-lg exportar-excesso" data-type="xls" ><span class="fa fa-save"></span>Excel</button>
                                    <button disabled class="btn btn-default btn-lg exportar-excesso" data-type="csv" ><span class="fa fa-save"></span>CSV</button>
                                    <button class="btn btn-default btn-lg btn-imprimir" ><span class="glyphicon glyphicon-print"></span>Imprimir</button>
                               </div>
                               <div class="col-xs-12 col-sm-2">
                                   <a class="col-xs-12 btn btn-info btn-lg excesso-velocidade bt-relarorio-excesso" disabled href="#"><span class="glyphicon glyphicon-thumbs-up"></span>Gerar</a>
                               </div>
                           </div>
                       </div>
                       <div class="divImprimir">
                            <div class="visible-print h4">Relatório de Excesso de Velocidade</div>
                            <table id="relatorioExcessoVelocidade" class="table table-hover table-condensed table-relatorios">
                                <thead>
                                    <tr>
                                        <th>Data/Hora ocorrência <span></span></th>
                                        <th>Motorista <span></span></th>
                                        <th>Endereço</th>
                                        <th>Vel.permitida</th>
                                        <th>Vel.atingida</th>
                                        <th>Excedido</th>
                                        <th>Porcentagem</th>
                                        <th class="hidden-print"><span class="glyphicon glyphicon-screenshot"></span></th>
                                    </tr>
                                </thead>
                                <tbody id="relatorioExcessoVelocidadeBody">
                                    <tr>
                                        <td colspan="8">
                                            <span class="alert alert-info" style="margin-top: 0px;">Para gerar o relatório, selecione a data e a placa desejada.</span>
                                        </td>
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
