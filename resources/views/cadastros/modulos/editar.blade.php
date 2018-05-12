@extends('layouts.eagle')
@section('title')
Edição de modulo @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a href="{{url('painel/cadastros/modulos')}}">Módulos</a></li>
        <li class="active">Editar</li>
    </ul>
    <form id="formCadastroModulos" method="POST" action="{{url('painel/cadastros/modulos/editar/'.$modulo->mocodigo)}}" class="form-horizontal" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div id="cadastroVeiculo">
            <div class="tab-content col-sm-12">
                <div class="page-title">
                    <h2>
                        <span class="flaticon-icon006"></span> Edição e Monitoramento de Modulos
                    </h2>
                </div>
                <ul  class="nav nav-tabs nav-eagle">
                    <li class="active"><a  href="#editar" data-toggle="tab">Edição</a></li>
                    <li><a href="#monitor" data-toggle="tab">Monitoramento</a></li>
                </ul>

                <div class="tab-pane pane-eagle active" id="editar">
                    <div class="panel panel-default pane-eagle">
                        <div class="col-xs-12">
                            <div class="col-xs-12 form-group">
                                <label>Serial*</label>
                                <input type="text" value="{{$modulo->mocodigo}}"  class="form-control" disabled/>
                                <input type="hidden" name="mocodigo" value="{{$modulo->mocodigo}}"  />
                            </div>

                            <div class="col-xs-12 form-group {{ ($errors->has('moimei')) ? 'has-error' : '' }}" >
                                <label>IMEI*</label>
                                <input type="text" name="moimei" value="{{$modulo->moimei}}" id="inputIMEI" class=" form-control" />
                                <p class="help-block">{{ ($errors->has('moimei') ? $errors->first('moimei') : '') }}</p>
                            </div>

                            <div class="col-xs-12 form-group">
                                <label>Data de instalação</label>
                                <input type="text" value="{{$modulo->modatainstalacao}}" id="inputData" name="modatainstalacao" class="data-data form-control"/>
                            </div>


                            <div class="col-xs-12 form-group busca-chip {{ ($errors->has('mosim')) ? 'has-error' : '' }}">
                                <label>SIM</label>
                                <select name="mosim" class="form-control js-example-basic-single js-states ls-select" id="inputMosim">
                                    <option value= "" >Selecione</option>
                                    @foreach ($chips as $chip)
                                        @if(isset($modulo->chip) && $modulo->chip->chnumero == $chip->chnumero)
                                            <option selected value="{{$chip->chcodigo}}">{{$chip->chnumero}}</option>
                                        @elseif(!isset($chip->modulo))
                                            <option value="{{$chip->chcodigo}}">{{$chip->chnumero}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <p class="help-block">{{ ($errors->has('mosim') ? $errors->first('mosim') : '') }}</p>
                            </div>
                            <div class="col-xs-12 form-group busca-modelo {{ ($errors->has('momodelo')) ? 'has-error' : '' }}">
                                <label>Modelo*</label>
                                <select name="momodelo" class="form-control" id="inputModelo">
                                    @foreach ($modelos as $modelo)
                                        @if($modulo->moduloModelo->mmcodigo == $modelo->mmcodigo)
                                            <option selected value="{{$modelo->mmcodigo}}" >{{$modelo->mmdescricao}}</option>
                                        @else
                                            <option value="{{$modelo->mmcodigo}}" >{{$modelo->mmdescricao}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <p class="help-block">{{ ($errors->has('momodelo') ? $errors->first('momodelo') : '') }}</p>
                            </div>
                            <div class="col-xs-12 form-group busca-cliente {{ ($errors->has('moproprietario')) ? 'has-error' : '' }}">
                                <label>Cliente*</label>
                                <select name="moproprietario" class="form-control" id="inputCliente">
                                    @if(Auth::user()->usumaster == 'S')
                                        @foreach ($clientes as $cliente)
                                            @if($modulo->proprietario->clcodigo == $cliente->clcodigo)
                                                <option selected value="{{$cliente->clcodigo}}" >{{$cliente->clnome}}</option>
                                            @else
                                                <option value="{{$cliente->clcodigo}}" >{{$cliente->clnome}}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option selected value="{{Auth::user()->cliente->clcodigo}}" >{{Auth::user()->cliente->clnome}}</option>
                                    @endif
                                </select>
                                <p class="help-block">{{ ($errors->has('moproprietario') ? $errors->first('moproprietario') : '') }}</p>
                            </div>
                        </div>

                        <div class="col-xs-12 form-group">
                            <div class="col-xs-3">
                                <label class="">Status</label>
                                <div class="chec-tipo-cliente">
                                    <input type="hidden" name="mostatus" value="D">
                                    <span class="col-xs-4 psa-fisica">Inativo</span>
                                    <label class="col-xs-4 switch">
                                        <input {{$modulo->mostatus == 'A' ? 'checked' : '' }} type="checkbox" name="mostatus" value="A">
                                        <div class="slider round"></div>
                                    </label>
                                    <span class="col-xs-4 psa-juridica">Ativo</span>
                                </div>
                            </div>
                        </div>

                        <div style="float: right;" class="form-group block-salvar">
                            <button id="salvarModulo" type="submit" value="save" class="btn btn-lg btn-primary">Salvar</button>
                            <a href="{{ url('painel/cadastros/modulos') }}" class="btn btn-lg btn-danger">Cancelar</a>
                        </div>
                    </div>
                </div>
                <div class="tab-pane pane-eagle" id="monitor">
                    <div class="panel panel-default pane-eagle">
                        <div class="row">
                            <div class="col-xs-3">
                                <label><b>ID:</b> <span class="moduloid">{{$modulo->mocodigo}}</span></label>
                            </div>

                            <div class="col-xs-3">
                                <label><b>Número:</b> {{$modulo->chnumero}}</label>
                            </div>

                            <div class="col-xs-3">
                                <label><b>Modelo:</b> {{$modulo->mmdescricao}}</label>
                            </div>

                            <div class="col-xs-3">
                                <label><b>Placa:</b> {{$modulo->veplaca}}</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3">
                                <label><b>IMEI:</b> {{$modulo->moimei}}</label>
                            </div>

                            <div class="col-xs-3">
                                <label><b>ICCID:</b> {{$modulo->iccid}}</label>
                            </div>

                            <div class="col-xs-3">
                                <label><b>Status:</b> {{$modulo->mostatus == 'A' ? "Ativo" : "Inativo"}}</label>
                            </div>
                            
                            <div class="col-xs-3">
                                <label><b>Cliente:</b> {{$modulo->clnome}}</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4" style="border-bottom: 1px solid #ddd; border-top: 1px solid #ddd; margin-bottom: 30px; margin-top: 30px">
                                <label id="modulolat"><b>Latitude:</b> {{$modulo->moultimalat}}</label>
                            </div>

                            <div class="col-xs-4" style="border-bottom: 1px solid #ddd; border-top: 1px solid #ddd; margin-bottom: 30px; margin-top: 30px">
                                <label id="modulolon"><b>Longitude:</b> {{$modulo->moultimalon}}</label>
                            </div>

                            <div class="col-xs-4" style="border-bottom: 1px solid #ddd; border-top: 1px solid #ddd; margin-bottom: 30px; margin-top: 30px">
                                <label><b>Ultima Atualização:</b> {{$modulo->moultimoevento}}</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-7" style="margin-left: -10px;">
                                <div class="col-xs-4">
                                    <label><b>Direção:</b>
                                    @php
                                    switch ($modulo->moultimadirecao){
                                        case 0: 
                                            echo " Norte";
                                        break;
                                        case 1:
                                            echo " Nordeste";
                                        break;
                                        case 2:
                                            echo " Leste";
                                        break;
                                        case 3:
                                            echo " Sudeste";
                                        break;
                                        case 4:
                                            echo " Sul";
                                        break;
                                        case 5:
                                            echo " Sudoeste";
                                        break;
                                        case 6:
                                            echo " Oeste";
                                        break;
                                        case 7:
                                            echo " Noroeste";
                                        break;
                                    }
                                    @endphp
                                    </label>
                                </div>

                                <div class="col-xs-4">
                                    @if($modulo->moultimaignicao == 1)
                                        <label><b>Ignição:</b> Ligado</label>
                                    @else
                                        <label><b>Ignição:</b> Desligado</label>
                                    @endif
                                </div>

                                <div class="col-xs-4">
                                    <label><b>Panico:</b> {{$modulo->moultimopanico == 1 ? "Ativo" : "Inativo" }}</label>
                                </div>
                            </div>

                            <div class="col-xs-5" style="text-align: center;">
                                <div class="col-xs-6">
                                    <label><b>Entradas</b></label>
                                </div>
                                <div class="col-xs-6">
                                    <label><b>Saidas</b></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-7" style="margin-left: -9px;">
                                <div class="col-xs-4">
                                    <label><b>Odômetro:</b> {{$modulo->moultimohodometro}} km</label>
                                </div>

                                <div class="col-xs-4">
                                    <label><b>Movendo:</b> {{$modulo->moultimavelocidade >=1 ? "Sim" : "Não"}}    </label>
                                </div>

                                <div class="col-xs-4">
                                    <label><b>Bateria:</b> {{$modulo->moalimentacao == 1 ? "Conectada" : "Desconectada"}}</label>
                                </div>
                            </div>
                            <div class="col-xs-5" style="text-align: center;">
                                <div class="col-xs-3">
                                    <label><b>1:</b> Ativo</label>
                                </div>
                                <div class="col-xs-3">
                                    <label><b>2:</b> Inativo</label>
                                </div> 
                                <div class="col-xs-3">
                                    <label><b>1:</b> Ativo</label>
                                </div>
                                <div class="col-xs-3">
                                    <label><b>2:</b> Inativo</label>
                                </div>             
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-offset-7 col-xs-5">
                                <div class="col-xs-offset-6 col-xs-3" style="text-align: center; margin-left: 225px;">
                                    <label><b>3:</b> Ativo</label>
                                </div>
                                <div class="col-xs-3" style="text-align: center;">
                                    <label><b>4:</b> Inativo</label>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <!-- <div class="mapa-cliente">
                        <div style="position: relative;" id="mapaPrincipal">
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </form>
@stop
