@extends('layouts.eagle')
@section('title')
Agendar Manutenção @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a href="{{url('painel/manutencao/manutencao')}}">Agendar manutenção</a></li>
        <li class="active">Novo</li>
    </ul>
    <div class="container">
        <div class="page-title">
            <h2>
                <span class="flaticon-icon011"></span> Agendar Manutenção
            </h2>
        </div>
        <div id="formCadastro" class="panel panel-default">
            <form id="formCadastroManutencao" method="POST" action="{{url('painel/manutencao/manutencao/save')}}" class="form-horizontal" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="col-sm-offset-1 col-sm-9">
                    <div class="col-sm-12">
                        <hr class="col-sm-12 row">
                        <div class="col-md-12">
                          <div class="select-clientes">
                              <span class="label-botoes-table">Selecione a Empresa</span>
                               <select id="selectClientesManutencaoCadastro"  class="" name="mapcliente[]">
                                  @foreach($clientes as $cliente)
                                      <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                  @endforeach
                              </select>
                          </div>
                        </div>
                        <div class="col-sm-6 busca">
                            <label>Tipo Manutenção</label>
                            <select id="selectTipoManutencaoCadastro" name="maptipomanutencao" class="form-control">
                            </select>
                        </div>
                        <div class="col-sm-6 busca ">
                            <label>Veículos</label>
                            <select id="selectPlacaManutencaoCadastro" name="mapcodigoveiculo"  class="form-control">
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <label>Km Atual Veículo</label>
                            <input type="number"  placeholder="Selecione um veículo" name="kmatual" id="kmatual" class="form-control" >
                        </div>

                        <div class="col-sm-4">
                            <label>Km Padrão Manutenção</label>
                            <input type="number" min="0" placeholder="Selecione tipo de manutenção" name="kmmanutencao"  id="kmmanutencao" class="form-control km-padrao">
                        </div>

                        <div class="col-sm-4">
                            <label>Próxima Manutenção</label>
                            <input type="number"  name="kmProximaManutencao" id="kmProximaManutencao" class="form-control">
                        </div>
                    </div>
                </div>

                    <div class="block-salvar">
                        <div class="col-sm-12">
                            <button type="submit" value="save" class="btn salvar btn-lg btn-primary bt_save_manutencao" disabled="true">
                                <span class="glyphicon glyphicon-ok"></span>
                                Salvar
                            </button>
                            <a href="{{url('painel/manutencao/manutencao')}}" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                        </div>
                    </div>
                </div>
           </form>
        </div>
    </div>
@stop
