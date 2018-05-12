@extends('layouts.eagle')
@section('title')
Cadastro de Tipo de Manutenção @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a href="{{url('painel/manutencao/tipo_manutencao')}}">Tipos de manutenções</a></li>
        <li class="active">Editar</li>
    </ul>
    <div class="container">
        <div class="page-title">
            <h2>
                <span class="flaticon-icon032"></span> Cadastro de Tipo de Manutenção
            </h2>
        </div>
        <div id="formCadastro" class="panel panel-default">
            <form id="formCadastroTipoManutencao" method="POST" action="{{url('painel/manutencao/tipo_manutencao/save')}}" class="form-horizontal" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input value="{{isset($tim) ? $tim->ticodigo : '' }}" name="ticodigo"  type="hidden" />
                <div class="col-sm-offset-1 col-sm-9">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-12 {{ ($errors->has('timdescricao')) ? 'has-error' : '' }}">
                                <label>Descrição*</label>
                                <input type="text" placeholder="Digite a Descrição" name="timdescricao" id="" value="{{ old('timdescricao') != null ? old('timdescricao') : isset($tim)? $tim->timdescricao : ''}}" class="form-control">
                                <p class="help-block">{{ ($errors->has('timdescricao') ? $errors->first('timdescricao') : '') }}</p>
                            </div>
                            <div class="col-sm-4 {{ ($errors->has('timkmpadrao')) ? 'has-error' : '' }}">
                                <label>Km Padrão*</label>
                                <input type="number" placeholder="Digite o Km Padrão" min="0" name="timkmpadrao" id="" value="{{ old('timkmpadrao') != null ? old('timkmpadrao') : isset($tim)? $tim->timkmpadrao : ''}}" class="form-control km-padrao ">
                                <p class="help-block">{{ ($errors->has('timkmpadrao') ? $errors->first('timkmpadrao') : '') }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="col-sm-12 row">
                        <div class="col-sm-6 busca {{ ($errors->has('timproprietario')) ? 'has-error' : '' }}">
                            <label>Proprietário</label>
                            <select id="" {{  \Auth::user()->usumaster == 'S'? '' : 'readonly="readonly"' }} name="timproprietario" class="form-control">
                                @foreach($clientes as $cliente)
                                    @if(old('timproprietario') == $cliente->clcodigo || ( isset($tim) && $tim->timproprietario == $cliente->clcodigo))
                                        <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @else
                                        <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <p class="help-block">{{ ($errors->has('timproprietario') ? $errors->first('timproprietario') : '') }}</p>
                        </div>
                    </div>

                    <div class="block-salvar">
                        <div class="col-sm-12">
                            <button type="submit" value="save" class="btn salvar btn-lg btn-primary">
                                <span class="glyphicon glyphicon-ok"></span>
                                Salvar
                            </button>
                            <a href="{{url('painel/manutencao/tipo_manutencao')}}" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                        </div>
                    </div>
                </div>
           </form>
        </div>
    </div>
@stop
