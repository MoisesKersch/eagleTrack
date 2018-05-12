@extends('layouts.eagle')
@section('title')
Listagem de Quilometros Percorridos @parent
@stop
@section('content')
<ul class="breadcrumb">
  <li><a href="{{url('painel')}}">Painel</a></li>
  <li class="active">Quilometros percorridos</li>
</ul>
    <div class="page-title">
        <h2>
            <span class="flaticon-icon028"></span> Quilometros Percorridos
        </h2>
    </div>
    <div class="page-content-wrap" id="kmPercorrido">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-km" action="{{ url('painel/relatorios/kmspercorridos/buscar') }}" type="POST">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">

                          <div class="col-sm-10">
                              <div class="form-group">
                                  <div class="col-sm-4">
                                      <div class="col-sm-4 block-data">
                                          <label for="">Início data</label>
                                          <input data-form=".form-km" class="form-control data-data km-percorrido" value="{{ date('d/m/Y') }}" id="" type="text" name="data_inicio">
                                      </div>
                                      <div class="col-sm-4 block-hora">
                                          <label for="">hora</label>
                                          <input data-form=".form-km" class="form-control data-hora km-percorrido data-hora-inicio" id="" type="text" name="hora_inicio">
                                      </div>
                                  </div>
                                  <div class="col-sm-4">
                                       <div class="col-sm-4 block-data">
                                          <label for="">Fim data</label>
                                          <input data-form=".form-km" class="form-control data-fim km-percorrido  data-data" value="{{ date('d/m/Y') }}" id="" type="text" name="data_fim">
                                      </div>
                                      <div class="col-sm-4 block-hora">
                                          <label for="">hora</label>
                                          <input data-form=".form-km" class="form-control data-hora km-percorrido data-hora-fim" id="" type="text" name="hora_fim">
                                      </div>
                                  </div>
                              </div>
                          </div>

                            <div class="col-sm-6 col-xs-12">
                                <label for="">Selecione as empresas</label>
                                    <select id="clientesKmPercorrido" data-id="kmPercorridoVeiculo" data-url="{{ url('painel/cadastros/veiculos/veiculo') }}" multiple class="form-control select-cliente select-selecionar-todos" name="buscar[]">
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
                            <div class="col-sm-6 col-xs-12">
                                <label for="">Selecione os Veículos</label>
                                <select data-form=".form-km" id="kmPercorridoVeiculo" multiple class="form-control select-selecionar-todos" name="buscarVeiculos[]">
                                </select>
                            </div>
                        </form>


                         <div class="col-xs-12">
                            <span class="label-botoes-table">Exportar para:</span>
                            <div class="col-xs-12 btn-group">
                                <div class="col-xs-10">
                                    <a disabled data-col="7" data-url="{{ url('painel/relatorios/kmspercorridos/exportar') }}"  data-id="tableKmPercorrido" data-type="pdf" class="col-xs-1 btn btn-default exportar_km ">PDF</a>
                                    <form action="{{ url('painel/relatorios/kmspercorridos/exportar') }}" class="form-exportar" method="post" id="formKmPercorridoExportar" target="_blanck">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" class="exportar-dados" name="dados">
                                        <button disabled type="submit" class="exportar_km btn btn-default" name="type" value="xls">Excel</button>
                                        <button disabled type="submit" class="exportar_km btn btn-default" name="type" value="csv">CSV</button>
                                    </form>
                                </div>
                                <div class="col-xs-2">
                                    <a disabled  id="gerarRelatorioKmPercorrido" class=" btn btn-info bt_gerar_relatorio"><span class="glyphicon glyphicon-thumbs-up"></span>Gerar</a>
                                </div>
                            </div>
                        </div>
                        <table id="tableKmPercorrido" class="table">
                            <thead>
                                <tr>
                                    <th>DATA</th>
                                    <th>PREFIXO</th>
                                    <th>DESCRICAO</th>
                                    <th>KM's PERCORRIDOS</th>
                                </tr>
                            </thead>
                            <tbody id="tableTempoKmPercorridoBody">
                           </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

    </div>
@stop
