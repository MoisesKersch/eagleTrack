@extends('layouts.eagle')
@section('title')
Interface de Monitoramento @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active">Monitoramento</li>
</ul>
<div class="nomargin nopadding col-xs-12 col-md-12 col-sm-12">
  <div class="nomargin nopadding col-xs-12 col-md-6 col-sm-6 page-title">
      <h2>
          <span class="flaticon-icon022"></span> Monitoramento
      </h2>
  </div>
  <div>
      @include('addons.mensagens')
  </div>
</div>
<div class="nomargin nopadding page-content-wrap" id="acompanhamento">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body nopadding">
                    <div class="col-sm-12 ">
                        <div class="min-select2 select-clientes col-sm-4">
                            <span class="label-botoes-table">Selecione as empresas</span>
                            <select id="clientesAcompanhamento" multiple class="select-cliente-pontos select-selecionar-todos flt-change " name="clientes[]">
                                <option value="0">Selecionar Todos</option>
                                @foreach($clientes as $cliente)
                                    @if(\Auth::user()->usumaster == 'S')
                                        <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @else
                                        <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class='col-sm-2'>
                            <span class="label-botoes-table">Data</span>
                            <input type="text" id="dataRota"
                                name="data_saida" value="{{date('d/m/Y')}}" placeholder="Data saída"
                                class="form-control flt-change-data"> </input>
                        </div>
                        <div class='col-sm-6'>
                            <span class="label-botoes-table">Rota Status</span>
                            <div class="btn-group">
                                <a class="btn btn-default fl_ro_st" id="ini_ro">Iniciada</a>
                                <a class="btn btn-default fl_ro_st" id="fin_ro">Finalizadas</a>
                                <a class="btn btn-default fl_ro_st" id="pend_ini_ro">Pendentes|Iniciadas</a>
                                <a class="btn btn-primary fl_ro_st" id="td_ro">Todos</a>
                            </div>
                            <input type="hidden" id="status_acomp_rota" value="I,P,F,C" />
                        </div>
                        <div class='col-sm-12'>
                            <div class="col-sm-4 nomargin nopadding">
                                <span class="col-sm-12 nomargin nopadding label-botoes-table">Legenda: </span>
                                <div class="col-sm-12 nomargin nopadding">
                                    <div class='col-sm-4 nomargin nopadding'>
                                        <span class="leg-green"></span>
                                        <span>No Horário </span>
                                    </div>
                                    <div class='col-sm-4 nomargin nopadding'>
                                        <span class="leg-blue"> </span>
                                        <span>Adiantado </span>
                                    </div>
                                    <div class='col-sm-4 nomargin nopadding'>
                                        <span class="leg-red"></span>
                                        <span>Atrasado </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <a class="btn bt-justificativa btn-lg" href="#">Justificativas <span class="fa fa-plus"></span></a>
                            </div>
                            <div class='col-sm-3 input-search-acomp' title='Buscar por veículo, prefixo, Motorista, Ajudante ou Empresa'>
                                <span class="label-botoes-table" >Buscar</span>
                                <div class="inner-addon left-addon">
                                    <i class="glyphicon glyphicon-search"></i>
                                    <input type="text" value='' id='buscaAcompanhamento' class="flt-change-busca form-control" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Div colapse -->
                    <div class="col-md-12 list-acompanhamentos">
                        <!-- Aqui são adicionados os elementos via javascript -->


                        <!-- Ícones marcadores que são utilizados; -->
                        <!-- <div class="invisible col-sm-12">
                            <div class="col-sm-1">
                                <div class="icons-maker">
                                    <span class="ico fa fa-thumbs-o-down"></span>
                                    <span class="ico-marker fa fa-map-marker marker-up"></span>
                                </div>
                            </div>

                            <div class="col-sm-1">
                                <div class="icons-maker">
                                    <span class="ico fa fa-thumbs-o-up"></span>
                                    <span class="ico-marker fa fa-map-marker marker-up"></span>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="icons-maker">
                                    <span class="ico fa fa fa-thumb-tack"></span>
                                    <span class="ico-marker fa fa-map-marker marker-tack"></span>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="icon-truck">
                                    <span class="fa fa-check veic-status"></span>
                                    <span class="fa fa-truck fa-5 fa-truck-esquerda"></span>
                                </div>
                            </div>
                        </div> -->
                    </div>
               </div>
           </div>
       </div>
   </div>
</div>

@stop
