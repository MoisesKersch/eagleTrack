@extends('layouts.eagle')
@section('title')
Cadastro de veículos @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a href="{{url('painel/cadastros/veiculos')}}">Veículos</a></li>
        <li class="active">Novo</li>
    </ul>
    <form id="formCadastroVeiculo" method="POST" action="{{url('painel/cadastros/veiculos/cadastrar')}}" class="form-horizontal" enctype="multipart/form-data">
        <div id="cadastroVeiculo" class="container">
            <div class="tab-content col-sm-12">
                <div class="page-title">
                    <h2>
                        <span class="flaticon-icon023"></span> Cadastro de veículos
                    </h2>
                </div>
                <ul class="nav nav-tabs nav-eagle">
                    <li class="active"><a data-toggle="tab" href="#homeCadCliente">Cadastro</a></li>
                    <li><a data-toggle="tab" href="#menu1CadCliente">Parâmetros Roteirização</a></li>
                </ul>
                <div id="homeCadCliente" class="tab-pane fade in active pane-eagle">
                    <div id="formCadastro" class="panel panel-default">
                        {{ csrf_field() }}
                        <div class="col-sm-offset-1 col-sm-9">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Tipo</label>
                                        <select id="" name="vetipo" value="{{old('vetipo')}}" class="form-control">
                                            <option value="C">Caminhão</option>
                                            <option value="O">Ônibus</option>
                                            <option value="A">Automóvel</option>
                                            <option value="M">Moto</option>
                                            <option value="T">Máquina</option>
                                            <option value="U">Outro</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">

                                    <div class="col-sm-4 {{ ($errors->has('veplaca')) ? 'has-error' : '' }}">
                                        <label>Placa*</label>
                                        <input type="text" placeholder="Digite a placa" name="veplaca" id="" value="{{old('veplaca')}}" class="form-control maiuscula  placa">
                                        <p class="help-block">{{ ($errors->has('veplaca') ? $errors->first('veplaca') : '') }}</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Chassi</label>
                                        <input type="text" name="vechassi" value="{{old('vechassi')}}" placeholder="Digite o chassi" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <hr class="col-sm-9">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3 {{ ($errors->has('veprefixo')) ? 'has-error' : '' }}">
                                        <label>Prefixo*</label>
                                        <input type="text" id="limitePrefixo"  maxlength="8" name="veprefixo" value="{{old('veprefixo')}}" placeholder="Digite o prefixo" class="form-control">
                                        <p class="help-block">{{ ($errors->has('veprefixo') ? $errors->first('veprefixo') : '') }}</p>
                                    </div>
                                    <div class="col-sm-9 block-cpf {{ ($errors->has('vedescricao')) ? 'has-error' : '' }}">
                                        <label>Descrição*</label>
                                        <input type="text" placeholder="Digite a descrição" name="vedescricao" class="form-control" value="{{old('vedescricao')}}">
                                        <p class="help-block">{{ ($errors->has('vedescricao') ? $errors->first('vedescricao') : '') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label>Velocidade máx</label>
                                        <input type="number" min="0" name="vevelocidademax" value="{{old('vevelocidademax')}}" placeholder="Digite a máxima" class="form-control inteiro-positivo">
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Hodômetro atual</label>
                                        <input id="hodometroAtual" name="vehodometroatual" value="{{old('vehodometroatual') ? old('vehodometroatual') : '' }}" type="number" class="form-control desabilitar " disabled>
                                        <input id="origHodometroAtual" name="orig_vehodometroatual" value="{{old('vehodometroatual') ? '0':''}}" type="number" class="form-control hidden ">
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Horímetro atual</label>
                                        <input id="horimetroAtual" name="vehorimetroatual" value="{{old('vehorimetroatual') ? old('vehorimetroatual') : '' }}" type="number" class="form-control desabilitar " disabled>
                                        <input id="origHorimetroAtual" name="orig_vehorimetroatual" value="{{old('vehorimetroatual') ? '0':'' }}" type="number" class="form-control hidden">
                                    </div>
                                </div>
                            </div>
                            <hr class="col-sm-12">
                            <div class="col-sm-12">
                                <div class="row">

                                    <div class="col-sm-6 busca {{ ($errors->has('veproprietario')) ? 'has-error' : '' }}">
                                        <label>Proprietário</label>
                                        <select id="veicProprietario" {{  \Auth::user()->usumaster == 'S'? '' : 'readonly="readonly"' }} name="veproprietario" class="form-control proprietario_veiculo">
                                            @foreach($clientes as $cliente)
                                                @if(old('veproprietario') == $cliente->clcodigo)
                                                    <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                                @else
                                                    <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <p class="help-block">{{ ($errors->has('veproprietario') ? $errors->first('veproprietario') : '') }}</p>
                                    </div>

                                    <div class="col-sm-6 busca {{ ($errors->has('vemodulo')) ? 'has-error' : '' }}">
                                        <label>Módulo</label>
                                        @if( \Auth::user()->usumaster == 'S')
                                            <select id="" name="vemodulo" class="form-control select-veiculo-modulo select2-noClear">
                                                <option selected value="-1"> Sem Módulo</option>
                                                @if(isset($modulos))
                                                    @foreach($modulos as $modulo)
                                                        @if(old('vemodulo') == $modulo->mocodigo)
                                                            <option selected value="{{ $modulo->mocodigo }}">{{ $modulo->mocodigo }}</option>
                                                        @else
                                                            <option value="{{ $modulo->mocodigo }}">{{ $modulo->mocodigo }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        @else
                                            <input type="text" readonly value="{{old('vemodulo')}}" class="form-control" name="vemodulo">
                                        @endif
                                        <p class="help-block">{{ ($errors->has('vemodulo') ? $errors->first('vemodulo') : '') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="menu1CadCliente" class="tab-pane fade">
                    <div class="panel panel-default pane-eagle">
                        <hr class="col-sm-12">
                        <label>Disponível para Roterização</label>
                        <div class="col-sm-12">
                            <div class="col-sm-4" style="margin-top:20px; height:33px">
                                <input type="hidden" name="veroterizar" value="N">
                                <span class="col-xs-4">Indisponível</span>
                                <label class="col-xs-4 switch">
                                    <input type="checkbox" name="veroterizar"
                                        {{ old('veroterizar') == 'N' ? '' : 'checked' }}
                                        value="S">
                                    <div class="slider round"></div>
                                </label>
                                <span class="col-xs-3">Disponível</span>
                            </div>
                        </div>
                        <hr class="col-sm-12">

                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Autonomia em Km</label>
                                    <input type="text" min="0" name="veautonomia" value="{{old('veautonomia')}}" placeholder="Total de quilômetros que o veículo pode percorrer em um dia"
                                        title="Total de quilômetros que o veículo pode percorrer em um dia" class="form-control money">
                                </div>
                                <div class="col-sm-3">
                                    <label>Custo por Km</label>
                                    <input name="vecusto" value="{{old('vecusto')}}" placeholder="Custo estimado por quilometro rodado"
                                        title="Custo estimado por quilometro rodado" class="form-control money">
                                </div>
                                <div class="col-sm-3">
                                    <label>Máx horas</label>
                                    <input name="vemaxhoras" placeholder="Máximo de horas que o veículo pode trabalhar em um dia"
                                        title="Máximo de horas que o veículo pode trabalhar em um dia" value="{{old('vemaxhoras') != null ? old('vemaxhoras'):'00:00'}}" class="form-control input-time-infinit">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Carga Quilos</label>
                                    <input type="text" placeholder="Peso total que o veículo pode carregar"
                                        title="Peso total que o veículo pode carregar" name="vemaxpeso" value="{{old('vemaxpeso')}}" class="form-control money">
                                </div>
                                <div class="col-sm-3">
                                    <label>Cubagem</label>
                                    <input type="text" name="vecubagem" value="{{old('vecubagem')}}" placeholder="Cubagem total que cabe no veículo"
                                        title="Cubagem total que cabe no veículo" class="form-control money">
                                </div>

                                <div class="col-sm-3">
                                    <label>Máx. Entregas</label>
                                    <input name="vemaxentregas" value="{{old('vemaxentregas')}}" placeholder="Número máximo de entregas por rota"
                                        title="Número máximo de entregas por rota" class="form-control inteiro-positivo">
                                </div>
                            </div>
                        </div>
                        <hr class="col-sm-12">
                        <h4>Horário de Trabalho</h4>
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-3 ">
                                    <label>Hora Início Trabalho</label>
                                    <input type="text" name="vehorainiciotrabalho" value="{{old('vehorainiciotrabalho')}}" class="form-control desabilitar data-hora-inicio-veiculo"
                                        title="Hora inicial de trabalho do veículo">
                                </div>
                                <div class="col-sm-3 ">
                                    <label>Hora Fim Trabalho</label>
                                    <input type="text" name="vehorafinaltrabalho" value="{{old('vehorafinaltrabalho')}}" class="form-control desabilitar data-hora-fim-veiculo"
                                        title="Hora final permitida para trabalho do veículo">
                                </div>
                            </div>
                        </div>
                        <hr class="col-sm-12">
                        <h4>Regiões</h4>
                        <div class="col-sm-12">
                            <h5>Selecione as Regiões</h5>
                            <select style="width:300px" id="veRegioes" name="veregioes[]"  data-regioes="{{ $ids_reg }}" multiple class="form-control select2-noClear">
                            </select>
                        </div>

                        <hr class="col-sm-12">
                        <h4>Roterizar Por</h4>
                        <div class="col-sm-12">
                            <div class="col-sm-2">
                                <div class="col-sm-12">
                                    <h5>Estradas de Terra</h5>
                                    <input type="hidden" name="veestradaterra" value="N">
                                    <div class="col-sm-4" style="margin-top:20px; height:33px">
                                        <label class="col-xs-4 switch">
                                            <input type="checkbox" name="veestradaterra"
                                                {{ null !== old('veestradaterra') && old('veestradaterra') == 'S' ? 'checked' : '' }}
                                                value="S" >
                                            <div class="slider round"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="col-sm-12">
                                    <h5>Balsas</h5>
                                    <input type="hidden" name="vebalsas" value="N">
                                    <div class="col-sm-4" style="margin-top:20px; height:33px">
                                        <label class="col-xs-4 switch">
                                            <input type="checkbox" name="vebalsas"
                                                {{ null !== old('vebalsas') && old('vebalsas') == 'S' ? 'checked' : '' }}
                                                value="S">
                                            <div class="slider round"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="col-sm-12">
                                    <h5>Pedágios</h5>
                                    <input type="hidden" name="vepedagios" value="N">
                                    <div class="col-sm-4" style="margin-top:20px; height:33px">
                                        <label class="col-xs-4 switch">
                                            <input checked type="checkbox" name="vepedagios"
                                                {{ null !== old('vepedagios') && old('vepedagios') == 'S' ? 'checked' : '' }}
                                                value="S">
                                            <div class="slider round"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="block-salvar col-sm-12 text-right">
                    <div class="col-sm-12">
                        <button id="salvarCliente" type="submit" value="save" class="btn salvar btn-lg btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>
                            Salvar
                        </button>
                        <a href="{{url('painel/cadastros/veiculos')}}" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop
