@extends('layouts.eagle')
@section('title')
Manutenções Agendadas @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active"><a>Agendar manutenção</a></li>
</ul>
<div class="page-title">
    <h2>
        <span class="flaticon-icon011"></span> Tipos de manutenções
        <a href="{{ url('painel/manutencao/manutencao/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>Agendar Manutenção</a>
    </h2>
</div>
<div class="page-content-wrap">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Manutenções</h3>
                </div>
                <div class="mensagem-suss col-md-12"></div>
                <div class="panel-body">
                    <div class="col-md-12" style="margin-bottom: 20px;">
                        <div class="col-md-4" style="width: 50%;">
                          <div class="select-clientes">
                              <span class="label-botoes-table">Selecione as Empresas</span>
                               <select id="selectClientesManutencao" multiple class="select-selecionar-todos" name="clientes">
                                   <option value="0">Selecionar todos</option>
                                  @foreach($clientes as $cliente)
                                      @if(\Auth::user()->usumaster == 'N')
                                          <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                      @else
                                          <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                      @endif
                                  @endforeach
                              </select>
                          </div>
                        </div>

                        <div class="col-md-4" >
                          <div class="select-clientes">
                              <span class="label-botoes-table">Selecione tipo Manutenção</span>
                               <select id="selectListTipoManutencao" class="col-md-12" name="tipo_manutencao[]" >
                              </select>
                          </div>
                        </div>

                        <div class="col-md-6">
                            <span class="label-botoes-table">Status</span>
                            <div class="btn-group">
                                <a class="btn btn-success btn-primary btn-lg fl_man_st" id="todasMan" title="Clique aqui para visualizar todas as manutenções lançadas, ainda não realizadas." >Agendadas</a>
                                <a class="btn btn-warning btn-lg fl_man_st" id="proximaMan" title="Clique aqui para visualizar as manutenções que estão próximas, ou seja, menores que mil quilômetros." >Próxima</a>
                                <a class="btn btn-danger btn-lg fl_man_st" id="vencidaMan" title="Clique aqui para visualizar somente as manutenções que já passaram da quilometragem agendada." >Vencida</a>
                                <a class="btn btn-default btn-lg fl_man_st" id="realMan" title="Clique aqui para visualizar as manutenções já realizadas." >Realizadas</a>
                            </div>
                        </div>
                        <input type="hidden" id="filtroManutencaoAgendada" value="todas" />
                    </div>
                    <table id="tbListaManutencao" class="table">
                        <thead>
                            <tr>
                                <th>PLACA</th>
                                <th>PREFIXO</th>
                                <th>DESCRIÇÃO</th>
                                <th>TIPO MANUTENÇÃO</th>
                                <th>KM ATUAL</th>
                                <th>KM MANUTENÇÃO</th>
                                <th>CLIENTE</th>
                                <th hidden="true" >STATUS</th>
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
