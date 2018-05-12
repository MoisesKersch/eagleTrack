@extends('layouts.eagle')
@section('title')
Listagem de veículos @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('home')}}">Painel</a></li>
    <li class="active"><a>Veículos</a></li>
</ul>
<div class="page-title">
    <h2>
        <span class="flaticon-icon023"></span> Listagem de veículos
        @if(\App\Helpers\AcessoHelper::acessosPermissao('cadveiculos','ppcadastrar'))
            <a href="{{ url('painel/cadastros/veiculos/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>Novo veículo</a>
        @endif
    </h2>
</div>
@if(\App\Helpers\AcessoHelper::acessosPermissao('cadveiculos','ppvisualizar'))
    <div id="ppeditar" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadveiculos','ppeditar')}}"></div>
    <div id="ppexcluir" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadveiculos','ppexcluir')}}"></div>
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Veículos</h3>
                    </div>
                    <div class="panel-body">

                        <div class="col-md-4 col-xs-12">
                          <div class="select-clientes">
                              <!--<label for="">Selecione o cliente</label>-->
                              <span class="label-botoes-table">Selecione as empresas</span>
                               <select id="" multiple class="select-cliente-veiculo select-selecionar-todos" name="cliente_veiculos">
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
                          <span class="label-botoes-table">Módulo</span>
                          <div class="btn-group">
                              <a class="btn btn-default  filtro_mo_ve " id="ve_mo_sim">Com</a>
                              <a class="btn btn-default  filtro_mo_ve " id="ve_mo_nao">Sem</a>
                              <a class="btn btn-primary  filtro_mo_ve " id="ve_mo_todos">Todos</a>
                          </div>
                      </div>
                      <div class="col-md-4 col-xs-12">
                          <span class="label-botoes-table">Status</span>
                          <div class="btn-group">
                              <a class="btn btn-default status_ve" id="ativos_ve">Ativos</a>
                              <a class="btn btn-default status_ve" id="inativos_ve">Inativos</a>
                              <a class="btn btn-primary status_ve" id="todos_ve">Todos</a>
                          </div>
                      </div>

                      <input type="hidden" id="flg_modulo_ve" value="todos" />
                      <input type="hidden" id="flg_status_ve" value="todos" />

                            <table id="tb_lista_veiculos" class="table">
                                <thead>
                                    <tr>
                                        <th>PLACA</th>
                                        <th>PREFIXO</th>
                                        <th>Módulo</th>
                                        <th>DESCRIÇÃO</th>
                                        <th>PROPRIETÁRIO</th>
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
