@extends('layouts.eagle')
@section('title')
Listagem de chips @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active"><a>Chips</a></li>
</ul>
<div class="page-title">
    <h2>
        <span class="flaticon-icon016"></span> Listagem de Chip's
        <a href="{{ url('painel/cadastros/chips/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>Novo Chip</a>
    </h2>
</div>
<div class="page-content-wrap">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Chip's</h3>
                </div>
                <div class="panel-body">

                    <!-- Botão para carregar tabela -->
                    <button id="btload" class="hidden" ></button>


                  <div class="col-md-4 col-xs-12">
                      <span class="label-botoes-table">Módulos</span>
                      <div class="btn-group">
                          <a class="btn btn-default filtros_chip" id="com_modulo">Com Modulo</a>
                          <a class="btn btn-default filtros_chip" id="sem_modulo">Sem Modulo</a>
                          <a class="btn btn-primary filtros_chip" id="todos_modulo">Todos</a>
                      </div>
                  </div>
                  <div class="col-md-4 col-xs-12">
                      <span class="label-botoes-table">Status</span>
                      <div class="btn-group">
                          <a class="btn btn-primary fl_ch_st" id="at_chip">Ativos</a>
                          <a class="btn btn-default fl_ch_st" id="in_chip">Inativos</a>
                          <a class="btn btn-default fl_ch_st" id="td_chip">Todos</a>
                      </div>
                  </div>

                  <input type="hidden" id="status_chip" value="ativo" />
                  <input type="hidden" id="modulo_chip" value="todos_modulos" />

                    <table id="chipsTable" class="hover table">
                        <thead>
                            <tr>
                              <th width="180px">ICCID</th>
                              <th>NÚMERO</th>
                              <th>OPERADORA</th>
                              <th width="125px">SERIAL MODULO</th>
                              <th>AÇÕES</th>
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
