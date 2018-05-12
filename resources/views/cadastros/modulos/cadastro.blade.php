@extends('layouts.eagle')
@section('title')
Cadastro de modulos @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active"><a href="{{url('painel/cadastros/modulos')}}">Listagem de módulos</a></li>
    <li class="active">Novo</li>
</ul>
<div class="panel panel-modulo">
    <div class="panel-body col-xs-12">
        <div class="page-title">
            <h2>
                <span class="flaticon-icon006"></span> Cadastro de módulos
            </h2>
            @if (Session::has('error'))
                <div class="col-xs-12 error help-block">
                    <span class="error">{{ Session::get('error')}}</span>
                </div>
            @endif
            <div id="formCadastroModulos">
                <form id="formCadastroModulos" method="POST" action="{{url('painel/cadastros/modulos/cadastrar')}}" class="form-horizontal">
                    {{ csrf_field() }}
                     <div class="col-xs-12">
                        <div class="col-xs-12 form-group {{ ($errors->has('mocodigo')) ? 'has-error' : '' }}">
                            <label>Serial*</label>
                            <input type="text" name="mocodigo" value="{{old('mocodigo')}}" id="mocodigo" class="form-control"/>
                            <p class="help-block">{{ ($errors->has('mocodigo') ? $errors->first('mocodigo') : '') }}</p>
                        </div>

                        <div class="col-xs-12 form-group {{ ($errors->has('moimei')) ? 'has-error' : '' }}">
                            <label>IMEI*</label>
                            <input type="text" name="moimei" value="{{old('moimei')}}" id="inputIMEI" class="form-control"/>
                            <p class="help-block">{{ ($errors->has('moimei') ? $errors->first('moimei') : '') }}</p>
                        </div>

                        <div class="col-xs-12 form-group">
                            <label>Data de instalação</label>
                            <input type="text" value="{{old('modatainstalacao')}}" placeholder="__/__/____" id="inputData" name="modatainstalacao" class="data-data form-control"/>
                        </div>

                        <div class="col-xs-12 form-group busca-chip {{ ($errors->has('mosim')) ? 'has-error' : '' }} ">
                            <label>SIM</label>
                            <select name="mosim" class="form-control" id="inputMosim">
                                <option>Selecione...</option>
                                @foreach ($chips as $chip)
                                    @if($chip->chcodigo == old('mosim'))
                                        <option selected value="{{$chip->chcodigo}}" >{{$chip->chnumero}}</option>
                                    @else
                                        <option value="{{$chip->chcodigo}}" >{{$chip->chnumero}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <p class="help-block">{{ ($errors->has('mosim') ? $errors->first('mosim') : '') }}</p>
                            <!-- <input autocomplete="off" type="text" value="" placeholder="Busque um SIM cadastrado" id="inputMosim" name="inputMosim" class="form-control telefone"/>
                            <input type="hidden" name="mosim" id="mosim" value=""/> -->
                        </div>

                        <div class="col-xs-12 form-group busca-modelo {{ ($errors->has('momodelo')) ? 'has-error' : '' }}">
                            <label>Modelo*</label>
                            <select name="momodelo" class="form-control" id="inputModelo">
                                <option>Selecione...</option>
                                @foreach ($modelos as $modelo)
                                    @if($modelo->mmcodigo == old('momodelo'))
                                        <option selected value="{{$modelo->mmcodigo}}" >{{$modelo->mmdescricao}}</option>
                                    @else
                                        <option value="{{$modelo->mmcodigo}}" >{{$modelo->mmdescricao}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <!-- <input autocomplete="off" type="text" value="" placeholder="Busque um modelo já cadastrado" id="inputModelo" name="inputModelo" class="form-control vazio"/>
                            <input type="hidden" name="momodelo" id="momodelo" value=""/> -->
                            <p class="help-block">{{ ($errors->has('momodelo') ? $errors->first('momodelo') : '') }}</p>
                        </div>


                        <div class="col-xs-12 form-group busca-cliente {{ ($errors->has('moproprietario')) ? 'has-error' : '' }}">
                            <label>Cliente*</label>
                            <select name="moproprietario" class="form-control" id="inputCliente">
                                @if(Auth::user()->usumaster == 'S')
                                    <option>Selecione...</option>
                                    @foreach ($clientes as $cliente)
                                        @if($cliente->clcodigo == old('moproprietario'))
                                            <option selected value="{{$cliente->clcodigo}}" >{{$cliente->clnome}}</option>
                                        @else
                                            <option value="{{$cliente->clcodigo}}" >{{$cliente->clnome}}</option>
                                        @endif
                                    @endforeach
                                @else
                                    <option selected value="{{Auth::user()->cliente->clcodigo}}" >{{Auth::user()->cliente->clnome}}</option>
                                @endif
                            </select>

                            <!-- <input autocomplete="off" type="text" id="inputCliente" name="inputCliente" placeholder="Busque pelo nome ou razão social" class="form-control vazio">
                            <input type="hidden" name="moproprietario" id="moproprietario" value=""/> -->
                            <p class="help-block">{{ ($errors->has('moproprietario') ? $errors->first('moproprietario') : '') }}</p>
                        </div>
                    </div>

                    <div class="col-xs-12 form-group">
                        <div class="col-xs-3">
                            <label class="">Status*</label>
                            <div class="chec-tipo-cliente">
                                <input type="hidden" name="mostatus" value="D">
                                <span class="col-xs-4 psa-fisica">Inativo</span>
                                <label class="col-xs-4 switch">
                                  <input type="checkbox" name="mostatus" checked value="A">
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
                </form>
            </div>
        </div>
    </div>
</div>
@stop
