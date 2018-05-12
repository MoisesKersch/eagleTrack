@extends('layouts.eagle')
@section('title')
Listagem de veículos @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active">Proximidade</li>
</ul>
    <div class="page-title">
        <h2>
            <span class="fa fa-users"></span> Proximidade
        </h2>
    </div>
    <div class="page-content-wrap" id="proximidade">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-km" action="{{ url('painel/relatorios/proximidade/buscar') }}" type="POST">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="col-sm-12 col-xs-12">
                                    <div class="col-sm-3">
                                        <div class="col-sm-6 block-data">
                                            <label for="">Início data</label>
                                            <input data-form=".form-km" class="proximidade  form-control data-data data-inicio" value="{{ date('d/m/Y') }}" id="" type="text" name="data_inicio">
                                        </div>
                                        <div class="col-sm-6 block-hora">
                                            <label for="">hora</label>
                                            <input data-form=".form-km" class="proximidade  form-control hora-inicio data-hora-inicio" id="" type="text" name="hora_inicio">
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="col-sm-6 block-data">
                                            <label for="">Fim data</label>
                                            <input data-form=".form-km" class="form-control data-fim proximidade data-data" value="{{ date('d/m/Y') }}" id="" type="text" name="data_fim">
                                        </div>
                                        <div class="col-sm-6 block-hora">
                                            <label for="">hora</label>
                                            <input data-form=".form-km" class="form-control hora-fim proximidade data-hora-fim" id="" type="text" name="hora_fim">
                                        </div>
                                    </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-5">
                                    <label for="">Selecione as empresas</label>
                                    <select data-form=".form-km"  multiple class="form-control proximidade proximidade-clientes" name="clientes[]">
                                        <option class="todos-preoximidade" value="0">Selecionar todos</option>
                                        @foreach($clientes as $empresa)
                                            <option value="{{ $empresa->clcodigo }}">{{ $empresa->clnome }}</option>
                                        @endforeach
                                    </select>
                                  </div>

                                    <div class="col-sm-5">
                                    <label for="">Selecione as placas</label>
                                    <select data-form=".form-km" id="selectPlaca" multiple class="form-control buscar-veiculos-prox proximidade" name="buscar[]">
                                        @foreach($veiculos as $veiculo)
                                            <option value="{{ $veiculo->vecodigo }}">{{ $veiculo->veplaca }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                         <div class="col-sm-12">
                             <div class="col-sm-12">
                                <span class="label-botoes-table">Exportar para:</span>
                                <div class="btn-group">
                                    <a disabled data-type="pdf" class="btn btn-default exportar_proximidade">PDF</a>
                                    <a disabled data-type="xls" class="btn btn-default exportar_proximidade">Excel</a>
                                    <a disabled data-type="csv" class="btn btn-default exportar_proximidade">CSV</a>
                                </div>
                            </div>
                        </div>

                        <table id="customers2" class="table">
                            <thead>
                                <tr>
                                    <th>Placa</th>
                                    <th>Prefixo</th>
                                    <th>Descrição</th>
                                    <th>Motorista</th>
                                    <th>Dia da semana</th>
                                    <th>Data\Hora</th>
                                    <th>Tempo Parado</th>
                                    <th>Ponto Próximo</th>
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
@stop
