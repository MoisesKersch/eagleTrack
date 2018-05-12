@extends('layouts.eagle')
@section('title')
Listagem de Tipo de Manutenção @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active"><a>Tipos de manutenções</a></li>
</ul>
<div class="page-title">
    <h2>
        <span class="flaticon-icon032"></span> Listagem de Tipo Manutenção
        <a href="{{ url('painel/manutencao/tipo_manutencao/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>Novo Tipo Manutenção</a>
    </h2>
</div>
<div class="page-content-wrap">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Tipo Manutenção</h3>
                </div>
                <div class="panel-body">

                    <div class="col-md-4 col-xs-12">
                      <div class="select-clientes">
                          <span class="label-botoes-table">Selecione as empresas</span>
                           <select id="selectClientesTipoManutencao" multiple class="select-selecionar-todos" name="clientes">
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
                    </div>
                        <table id="tbListaTipoManutencao" class="table">
                            <thead>
                                <tr>
                                    <th>DESCRIÇÃO</th>
                                    <th>KM PADRÃO</th>
                                    <th>EMPRESA</th>
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
