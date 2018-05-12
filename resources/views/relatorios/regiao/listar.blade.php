@extends('layouts.eagle')
@section('title')
Região @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active">Veículos nas regiões</li>
</ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon040"></span>Região
        </h2>
    </div>
    <div class="page-content-wrap" id="relRegiao">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-regiao" action="{{ url('painel/relatorios/regiao/relatorio') }}" type="POST">
                            <div class="col-sm-12">
                                    <div class="col-sm-2">
                                        <div class="col-sm-12">
                                                <h5 for="">Início data</h5>
                                                <input data-form=".form-regiao" class="rel-regiao form-control regiao-data-inicio data-data" value="{{ date('d/m/Y') }}" id="" type="text" name="data_inicio">
                                            </div>
                                        <div class="col-sm-12">
                                                <h5 id="data-fim-label" for="">Fim data</h5>
                                                <input data-form=".form-regiao" class="rel-regiao form-control regiao-data-fim data-data" value="{{ date('d/m/Y') }}" id="data_fim" type="text" name="data_fim">
                                        </div>
                                    </div>
                            


                            <!-- <div class="col-sm-12"> -->
                                <div class="col-sm-4 class-reg-emp">
                                    <h5 for="">Selecione as empresas</h5>
                                    <select id="clientesRegiao" data-form=".form-regiao" data-id="clientesRegiao" data-url="{{ url('painel/cadastros/veiculos/veiculo') }}" multiple class="rel-regiao form-control select-selecionar-todos" name="clientes_regioes[]">
                                        <option value="0">Selecionar todos</option>
                                        @foreach($clientes as $cliente)
                                            @if(count($clientes) == 1)
                                                <option selected class="item-empresa-regioes" value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                            @else
                                                <option class="item-empresa-regioes" value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3  class-reg-veic">
                                    <h5 for="">Selecione os Veículos</h5>
                                        <select data-form=".form-regiao" id="regiaoVeiculo" multiple class="rel-regiao form-control select-selecionar-todos" name="regiao_veics[]">
                                    </select>
                                </div>
                                <div class="col-sm-3 class-reg-reg">
                                    <h5 for="">Selecione as Regiões</h5>
                                        <select data-form=".form-regiao" id="regiaoRegiao" multiple class="rel-regiao form-control select-selecionar-todos" name="regiao_regioes[]">
                                    </select>
                                </div>
                            <!-- </div> -->
                            </div>
                        </form>
                       <div class="col-xs-12 cabecalho-exportacoes">
                           <div class="hidden-xs col-sm-10">
                              <div class="btn-group">
                                  <a disabled data-type="pdf" class="btn btn-lg btn-default exportar-rel-regiao"><span class="fa fa-save"></span>PDF</a>
                                  <a disabled data-type="xls" class="btn btn-lg btn-default exportar-rel-regiao"><span class="fa fa-save"></span>Excel</a>
                                  <a disabled data-type="csv" class="btn btn-lg btn-default exportar-rel-regiao"><span class="fa fa-save"></span>CSV</a>
                                  <a class="btn btn-lg btn-default btn-imprimir"><span class="glyphicon glyphicon-print"></span>Imprimir</a>
                              </div>
                          </div>
                           <div class="col-xs-2">
                               <a id="gerarRelatorioRegiao" class="col-xs-12 btn btn-lg btn-info" disabled href="#"><span class="glyphicon glyphicon-thumbs-up"></span>Gerar</a>
                           </div>
                       </div>
                       <div class="col-sm-12 divImprimir">
                            <table id="relatorioRegiao" class="table-hover table table-relatorios table-condensed">
                                <thead>
                                    <tr>
                                        <!-- <th>DATA</th> -->
                                        <th>DATA HORA ENTRADA</th>
                                        <th>DATA HORA SAÍDA</th>
                                        <th>MOTORISTA</th>
                                        <th>REGIÃO</th>
                                        <th>KMS</th>
                                        <th>VEL. MÉDIA</th>
                                        <th>PARADAS</th>
                                    </tr>
                                </thead>
                                <tbody id="relatorioRegiaoBody">
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
