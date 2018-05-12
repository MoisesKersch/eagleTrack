@extends('layouts.eagle')
@section('title')
Listagem de Motoristas e Ajudantes @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active"><a>Motoristas e Ajudantes</a></li>
</ul>
<div class="page-title">
    <h2>
        <span class="flaticon-icon010"></span> Listagem de Motoristas e Ajudantes
        @if(\App\Helpers\AcessoHelper::acessosPermissao('cadmotoristas','ppcadastrar'))
            <a href="{{ url('painel/cadastros/motoristas/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>Novo Motorista ou Ajudante</a>
        @endif
    </h2>
</div>
@if(\App\Helpers\AcessoHelper::acessosPermissao('cadmotoristas','ppvisualizar'))
    <div id="ppeditar" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadmotoristas','ppeditar')}}"></div>
    <div id="ppexcluir" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadmotoristas','ppexcluir')}}"></div>
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Motoristas e Ajudantes</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-4 col-xs-12">
                          <div class="select-clientes">
                              <!--<label for="">Selecione o cliente</label>-->
                              <span class="label-botoes-table">Selecione as empresas</span>
                              <select  id="" multiple class="select-cliente-motoristas select-selecionar-todos" name="cliente_motoristas">
                                  <option value="0">Selecionar todos</option>
                                  @foreach($clientes as $cliente)
                                      @if($adm)
                                          <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                      @else
                                          <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                      @endif
                                  @endforeach
                              </select>
                          </div>
                        </div>

                      <div class="col-md-4 col-xs-12">
                          <span class="label-botoes-table">Motoristas/Ajudantes</span>
                          <div class="btn-group">
                              <a class="btn btn-default  filtros_ma " id="mt_mot">Motoristas</a>
                              <a class="btn btn-default  filtros_ma " id="mt_aju">Ajudantes</a>
                              <a class="btn btn-primary  filtros_ma " id="mt_todos">Todos</a>
                          </div>
                      </div>
                      <div class="col-md-4 col-xs-12">
                          <span class="label-botoes-table">Status</span>
                          <div class="btn-group">
                              <a class="btn btn-default status_ma" id="at_mt">Ativos</a>
                              <a class="btn btn-default status_ma" id="in_mt">Inativos</a>
                              <a class="btn btn-primary status_ma" id="td_mt">Todos</a>
                          </div>
                      </div>

                      <input type="hidden" id="flg_ma" value="" />
                      <input type="hidden" id="flg_status_ma" value="" />


                        <table id="cadastroMotoAjudante" class="hover table table-motorista">
                            <thead>
                                <tr>
                                  <th>NOME</th>
                                  <th>CRACHÁ</th>
                                  <th width="100px">TELEFONE</th>
                                  <th width="100px">TIPO CNH</th>
                                  <th width="150px">VALIDADE CNH</th>
                                  <th>EMPRESA</th>
                                  <th width="120px">AÇÕES</th>
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
@endif
@stop
