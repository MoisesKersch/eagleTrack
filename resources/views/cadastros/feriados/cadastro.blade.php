@extends('layouts.eagle')
@section('title')
Cadastro de feriados @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active"><a href="{{url('painel/cadastros/feriados')}}">Feriados</a></li>
    <li class="active">Novo</li>
</ul>
<div id="cadastroFeriados">
	<div class="container">
		<div class="page-title">
            <h2>
                <span class="flaticon-icon002"></span> Cadastro de feriados
            </h2>
        </div>
        <div class="col-sm-12 col-xs-12" style="margin-top: 20px;">
            @include('addons.mensagens')
        </div>
	</div>
	<div id="formCadastro" class="panel panel-default">
        @if(isset($feriado->frcodigo))
            <form class="form-horizontal" action="{{url('painel/cadastros/feriados/editar/'.$feriado->frcodigo)}}" method="POST">
        @else
            <form class="form-horizontal" action="{{url('painel/cadastros/feriados/cadastrar')}}" method="POST">
        @endif
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="col-sm-4">
                <div class="col-sm-12 {{ ($errors->has('frcliente')) ? 'has-error' : '' }}">
                    <label class="label-nome-cl">Empresa*</label>
                    <select name="frcliente" id="frcliente" class="form-control">
                        @foreach($clientes as $cliente)
                            @if(isset($feriado))
                                @if($feriado->frcliente == $cliente->clcodigo)
                                    <option selected value="{{$cliente->clcodigo}}">{{$cliente->clnome}}</option>
                                @else
                                    <option value="{{$cliente->clcodigo}}">{{$cliente->clnome}}</option>
                                @endif
                            @elseif($user->cliente->clcodigo == $cliente->clcodigo)
                                <option selected value="{{$cliente->clcodigo}}">{{$cliente->clnome}}</option>
                            @else
                                <option value="{{$cliente->clcodigo}}">{{$cliente->clnome}}</option>
                            @endif

                        @endforeach
                    </select>
                    <p class="help-block">{{ ($errors->has('frcliente') ? $errors->first('frcliente') : '') }}</p>
                </div>
                <div class="col-sm-12 {{ ($errors->has('frtipo')) ? 'has-error' : '' }}">
                    <label class="label-nome-cl">Tipo*</label>
                    <select name="frtipo" class="form-control">
                        @if($user->usumaster == 'S')
                            <option {{ (isset($feriado) && $feriado->frtio) == 'N' ? 'selected' : '' }} value="N">Nacional</option>
                        @endif
                        <option {{ (isset($feriado) && $feriado->frtio) == 'R' ? 'selected' : '' }} value="R">Regional</option>
                    </select>
                    <p class="help-block">{{ ($errors->has('frtipo') ? $errors->first('frtipo') : '') }}</p>
                </div>
                <div class="col-sm-12 {{ ($errors->has('frdescricao')) ? 'has-error' : '' }}">
                    <label class="label-nome-cl">Descrição*</label>
                    <input type="text" name="frdescricao" placeholder="Digite o nome" class="form-control" value="{{isset($feriado->frdescricao) ? $feriado->frdescricao : old('frdescricao')}}">
                    <p class="help-block">{{ ($errors->has('frdescricao') ? $errors->first('frdescricao') : '') }}</p>
                </div>
            </div>  
            <div class="col-sm-12">
                <div class="col-sm-4 sm-4-data {{ ($errors->has('frdata')) ? 'has-error' : '' }}">
                    <label class="label-nome-cl">Data*</label>
                    <input type="text" id="ipDataFeriado" name="frdata" data-id="{{isset($feriado) ? $feriado->frcodigo : ''}}" placeholder="Selecione a data" class="form-control data-feriado" value="{{isset($feriado->frdata) ? $feriado->frdata : old('frdata') }}">
                    <p class="help-block">{{ ($errors->has('frdata') ? $errors->first('frdata') : '') }}</p>
                </div>
                <div class="col-sm-8">
                    <div class="block-salvar col-sm-12 text-right">
                        <div class="col-xs-12">
                            <button id="salvarFeriado" type="submit" value="save" class="btn btn-lg btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>
                                Salvar
                            </button>
                            <a href="{{url('painel/cadastros/feriados')}}" class="btn btn-danger btn-lg">
                            <span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
		</form>
	</div>
</div>	
@stop