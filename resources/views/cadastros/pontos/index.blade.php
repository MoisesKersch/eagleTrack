@extends('layouts.eagle')
@section('content')
<ul class="breadcrumb">
  <li><a href="{{url('painel')}}">Painel</a></li>
  <li class="active"><a>Pontos</a></li>
</ul>
<div class="margin-top: 25px; col-xs-12 col-md-12 col-sm-12">
  <div class="col-xs-12 col-md-6 col-sm-6 page-title">
      <h2>
          <span class="flaticon-icon012"></span> Listagem de pontos
          @if(\App\Helpers\AcessoHelper::acessosPermissao('cadpontos','ppcadastrar'))
            <a href="{{ url('painel/cadastros/pontos/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span> Novo Ponto</a>
          @endif
          @if(\App\Helpers\AcessoHelper::acessosPermissao('cadpontos','ppimportar'))
            <a href="{{url('/painel/cadastros/pontos/importar')}}" class="btn btn-success"><span class="glyphicon glyphicon-import"></span>Importar</a>
          @endif
      </h2>
  </div>
  <div class="col-xs-12 col-md-6 col-sm-6">
      @include('addons.mensagens')
  </div>
</div>
@if(\App\Helpers\AcessoHelper::acessosPermissao('cadpontos','ppvisualizar'))
    <div id="ppeditar" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadpontos','ppeditar')}}"></div>
    <div id="ppexcluir" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadpontos','ppexcluir')}}"></div>
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="select-clientes col-xs-4">
                            <span class="label-botoes-table">Selecione as empresas</span>
                            <select id="clientesbusca_pontos" multiple class="select-cliente-pontos select-selecionar-todos" name="clientesbusca[]">
                                <option value="0">Selecionar Todos</option>
                                @foreach($clientes as $cliente)
                                    @if($adm)
                                        <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @else
                                        <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xs-4 marginPattern">
                          <label>Tipo de Ponto</label>
                            <select id="tipo_ponto" name="tipo_ponto" class="tipo_ponto form-control">
                              <option value="0">Todos</option>
                              <option value="C">Ponto de Coleta</option>
                              <option value="E">Ponto de Entrega</option>
                              <option value="P">Referência</option>
                            </select>
                        </div>


                        <table id="tableCadastroPontos" class="table datatable table-striped table-hover">
                          <thead>
                              <tr>
                                <th>DESCRIÇÃO</th>
                                <th>TIPO</th>
                                <th>ENDEREÇO</th>
                                <th>RAIO</th>
                                <th>EMPRESA</th>
                                <th>Ações</th>
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
