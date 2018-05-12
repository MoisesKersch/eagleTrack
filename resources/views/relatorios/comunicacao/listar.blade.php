@extends('layouts.eagle')
@section('title')
Relatório Controle de Horarios @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active">Comunicação</li>
</ul>
    <div class="page-content-wrap" id="controleComunicacao">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default"><br>
                    <div class="col-md-6">
                        <h2>
                            <span class="flaticon-icon047"></span> Comunicação
                        </h2>
                    </div>        

                        <div class="col-md-6 form-group" align="right">
                            <div class="dropdown">
                              <button disabled id="btnExportar" class="dropbtn flaticon-icon035" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                Exportar
                                <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                <li><a data-type="pdf" class="btn btn-lg btn-default exportar-comunicacao"><span class="flaticon-icon037"></span>&nbspPDF</a></li>
                                <li><a data-type="xls" class="btn btn-lg btn-default exportar-comunicacao"><span class="flaticon-icon038"></span>&nbspExcel</a></li>
                                <li><a data-type="csv" class="btn btn-lg btn-default exportar-comunicacao"><span class="flaticon-icon036"></span>&nbspCSV</a></li>
                              </ul>
                            </div>
                            
                            <button id="btnImprimir" disabled style="background-color: 5BC0DE;color: white;" 
                                class="btn-imprimir btn btn-default btn-lg" ><span class="glyphicon glyphicon-print"></span>Imprimir</button>

                             <a id="btnGerarComunicacao"  disabled class="btn btn-lg btn-info"><span class="glyphicon glyphicon-thumbs-up"></span>Gerar</a>
                        </div>
                                  
                    <div class="col-md-12">
                        <div class="panel-body">
                            <form class="form-comunicacao" action="{{ url('painel/relatorios/comunicacao') }}" type="POST">
                                <div class="col-sm-12">
                                    <div class="col-sm-2 block-hora">
                                        <h5 for="">Tempo sem comunicar</h5>
                                        <input data-form=".form-km" class="form-control hora-hora comunicacao-tempo data-hora-fim" id="" type="text" name="hora">
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="col-sm-4">
                                            <h5 for="">Selecione as Empresas</h5>
                                            <!-- <select data-form=".form-parado" multiple class="form-control select-cliente-horario jornada-trabalho" name="buscar[]"> -->
                                            <select data-form=".form-comunicacao" id="selectClientesComunicacao" multiple class="form-control select-selecionar-todos" name="buscar[]">
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
                                            <select data-form=".form-comunicacao" id="selectVeiculosComunicacao" multiple class="form-control select-selecionar-todos" name="veiculos[]">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form><br>
                            <div class="col-sm-12 divImprimir">
                                <table id="tableControleComunicacao" class="table table-comunicacao table-hover     table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Última posição</th>
                                            <th>Local</th>
                                            <th>Módulo</th>
                                            <th>Modelo</th>
                                        </tr>
                                    </thead>
                                    <tbody id="corpoTabelaComunicacao">
                                         <tr>
                                            <td colspan="7"><span style="margin-top: 0px;" class="alert alert-info">Selecione ao menos uma empresa ou veículo para gerar a listagem.</span></td>
                                        </tr>
                                   </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop



<style>


.dropbtn {
    background-color: #428BCA;
    border:0px;
    color: white;
    font-size: 13px;
    padding: 10px 5px;
    line-height: 17px;
    padding-top: 7px;
}

.dropdown {
    position: absolute;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 80px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a {
    color: black;
    padding: 16px 22px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {background-color: #cccccc}

.dropdown:hover .dropdown-content {
    display: block;
}

.dropdown:hover .dropbtn {
    background-color: #428BCA;
}
</style>
