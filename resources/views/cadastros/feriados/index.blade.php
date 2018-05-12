@extends('layouts.eagle')
@section('title')
Listagem de feriados @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel ')}}">Painel</a></li>
    <li class="active"><a>Feriados</a></li>
</ul>
<div class="page-title">
    <h2>
        <span class="flaticon-icon002"></span> Listagem de feriados
        @if(\App\Helpers\AcessoHelper::acessosPermissao('cadferiados','ppcadastrar'))
            <a href="{{ url('painel/cadastros/feriados/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>Novo feriado</a>
        @endif
    </h2>
</div>
@if(\App\Helpers\AcessoHelper::acessosPermissao('cadferiados','ppvisualizar'))
    <div id="ppeditar" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadferiados','ppeditar')}}"></div>
    <div id="ppexcluir" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadferiados','ppexcluir')}}"></div>
    <div class="page-content-wrap" id="listagemFeriados">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Feriados</h3>
                    </div>
                    <div class="panel-body">

                        <!-- Botão para carregar tabela -->
                        <button id="btload" class="hidden" ></button>

                        <div class="select-clientes col-xs-4">
                            <span class="label-botoes-table">Selecione as empresas</span>
                            <select id="buscaCliente" multiple class="select-cliente-pontos select-selecionar-todos" name="clientesbusca[]">
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
    					<div class="col-md-4 col-xs-12">
    					  <span class="label-botoes-table">Tipo</span>
    					  <div class="btn-group">
    					      <a class="btn btn-primary filtros_feriados" data-tipo="" id="todos">Todos</a>
    					      <a class="btn btn-default filtros_feriados" data-tipo="N" id="nacional">Nacional</a>
    					      <a class="btn btn-default filtros_feriados" data-tipo="R" id="regional">Regional</a>
    					  </div>
    					</div>

    					<input type="hidden" id="status_chip" value="ativo" />
    					<input type="hidden" id="modulo_chip" value="todos_modulos" />

                        <table id="feriadosTable" class="hover table">
                            <thead>
                                <tr>
                                  <th>Data</th>
                                  <th>Descrição</th>
                                  <th>Tipo</th>
                                  <th>Cliente</th>
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
@endif
@stop
