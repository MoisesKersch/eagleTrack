@extends('layouts.eagle')
@section('title')
Painel de controle
@stop
@section('content')
<div class="page-content-wrap hidden-print">
    <!-- START WIDGETS -->
    @if(Auth::user()->usumaster == 'S')
    <div class="row">
        <div class="col-md-3">
            <div class="widget btn-info widget-padding-sm">
                <div class="widget-big-int plugin-clock">00<span>:</span>00</div>
                <div id="divDataHoraPainel" class="widget-subtitle"></div>
                <div class="widget-controls">
                    <a href="#" class="widget-control-right"><span class="fa fa-times"></span></a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div id="divPrevisaoTempo" class="widget btn-info widget-padding-sm">
                <div class="widget-title">Temperatura</div>
                <div id="cidadeTemperatura" class="widget-subtitle"></div>
                <div id="temperaturaAgora" class="widget-int"></div>
            </div>
        </div>
        <div class="col-md-3">
            <!-- START WIDGET SLIDER -->
            <div class="widget btn-info widget-carousel">
                <div class="owl-carousel" id="owl-example">
                    <div>
                        <div class="widget-title">Clientes</div>
                        <div class="widget-subtitle">Clientes cadastrados</div>
                        <div class="widget-int">{{$countClientes}}</div>
                    </div>
                    <div>
                        <div class="widget-title">Usuários</div>
                        <div class="widget-subtitle">Total usuários cadastrados</div>
                        <div class="widget-int">{{$countUsers}}</div>
                    </div>
                    <div>
                        <div class="widget-title">Veiculos Ativos</div>
                        <div class="widget-subtitle">Veiculos cadastrados</div>
                        <div class="widget-int">{{$countVeiculos}}</div>
                    </div>
                </div>
            </div>
            <!-- END WIDGET SLIDER -->
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-3">
            <div class="widget btn-info">
                <div class="widget-big-int plugin-clock">00<span>:</span>00</div>
                <div id="divDataHoraPainel" class="widget-subtitle"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div id="divPrevisaoTempo" class="widget btn-info widget-padding-sm">
                <div class="widget-title">Temperatura</div>
                <div id="cidadeTemperatura" class="widget-subtitle"></div>
                <div id="temperaturaAgora" class="widget-int"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="widget btn-info">
                <div class="widget-title">Veiculos Ativos</div>
                <div class="widget-big-int">{{$countVeiculos}}</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Top 5 - Veículos que mais rodaram</h3>
                </div>
                <div class="panel-body">
                    <div class='divCarregaGrafico'><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Carregando</div>
                    <div id="divGraficoQuilometragem">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row alertas-painel">
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading ui-draggable-handle">
                    <h3 class="panel-title">Alerta de Manutenção</h3>
                </div>
                <div class="panel-body">
                    <a href="/painel/manutencao/manutencao">
                        <table id="tablePainelAlertaManutencao" class="table table-hover ">
                            <thead>
                                <tr>
                                    <th>PLACA</th>
                                    <th>TIPO</th>
                                    <th>STATUS</th>
                                    <th class="hidden">Km faltantes</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                     </a>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading ui-draggable-handle">
                    <h3 class="panel-title">Vencimento de CNH</h3>
                </div>
                <div class="panel-body">
                    <table id="tablePainelCnhVencida" class="table table-hover ">
                        <thead>
                            <tr>
                                <th>MOTORISTA</th>
                                <th>VENCIMENTO</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-5" id="veicRegioes">
            <div class="panel panel-default">
                <div class="panel-heading ui-draggable-handle">
                    <h3 class="panel-title">Veículos nas Regiões</h3>
                </div>
                <div class="panel-body">
                    <table id="tableVeicnaRegi" class="table table-hover ">
                        <tbody id="dec">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <br>
        <br>
        
    </div>


    @endIf
</div>
@stop
<!-- @section('title')
    <script type="text/javascript" src="{{asset('js/template/plugins/scrolltotop/scrolltopcontrol.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/template/plugins/morris/raphael-min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/template/plugins/morris/morris.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/template/plugins/rickshaw/d3.v3.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/template/plugins/rickshaw/rickshaw.min.js')}}"></script>
    <script type='text/javascript' src='{{asset("js/template/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js")}}'></script>
    <script type='text/javascript' src='{{asset("js/template/plugins/jvectormap/jquery-jvectormap-world-mill-en.js")}}'></script>
    <script type='text/javascript' src='{{asset("js/template/plugins/bootstrap/bootstrap-datepicker.js")}}'></script>
    <script type="text/javascript" src="{{asset('js/template/settings.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/template/plugins.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/template/actions.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/template/demo_dashboard.js')}}"></script>
@stop -->
