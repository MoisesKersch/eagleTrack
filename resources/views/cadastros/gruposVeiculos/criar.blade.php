@extends('layouts.eagle')
@section('title')
Cadastro de grupos de veículos @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{ url('painel') }}">Painel</a></li>
        <li class="active"><a href="{{ url('painel/cadastros/gruposVeiculos') }}">Grupos de veículos</a></li>
        <li class="active">Novo</li>
    </ul>
    <div class="container">
        <div class="page-title">
            <h2>
                <span class="flaticon-icon014"></span> Cadastro de grupo de veículos
            </h2>
        </div>
        <div id="formCadastro" class="panel panel-default">
            <form id="formCadastroGrupoVeiculo" method="POST" action="{{ url('painel/cadastros/gruposVeiculos/cadastrar') }}" class="form-horizontal" enctype="multipart/form-data">
                {{ csrf_field() }}
                    <div class="col-sm-6 form-group">
                        <div class="row">
                            <div class="col-sm-12 {{ ($errors->has('gvdescricao')) ? 'has-error' : '' }}">
                                <label>Grupo de Veículos*</label>
                                <input type="text" placeholder="Digite o nome do grupo" name="gvdescricao" value="{{ old('descricao') }}" class="form-control">
                                <p class="help-block">{{ ($errors->has('gvdescricao') ? $errors->first('gvdescricao') : '') }}</p>
                            </div>
                            <div class="col-sm-12">
                                <label>Empresa</label>
                                <select name="gvempresa" value="{{ old('$grupo->gvempresa') }}" class="form-control desabilitar select2-noClear empresa-gv">
                                    @foreach ($clientes as $key => $cliente)
                                        @if(@old('gvempresa') !== null)
                                            <option {{ old('gvempresa') == $cliente->clcodigo ? 'selected' : '' }} value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                        @else
                                            <option {{ $grupo == $cliente->clcodigo ? 'selected' : '' }} value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                        @endif
                                    @endforeach;
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="campo-status" name="gvstatus" value="A">
                    <div class="col-sm-offset-1 col-sm-6 form-group">
                        <div class="row">
                            <div class="col-sm-12 {{ ($errors->has('veiculos')) ? 'has-error' : '' }} ">
                                <label>Veículos</label>
                                <select multiple name="veiculos[]" value="" class="form-control desabilitar veiculos-grupo-veiculos select-selecionar-todos">
                                    @foreach ($veiculos as $key => $veiculo)
                                        @if(@old('veiculos') !== null)
                                            <option {{ in_array($veiculo->vecodigo, @old('veiculos')) ? 'selected' : '' }} value="{{ $veiculo->vecodigo }}"> {{ $veiculo->veplaca.' | '.$veiculo->vedescricao }} </option>
                                        @else
                                            <option value="{{ $veiculo->vecodigo }}">{{ $veiculo->veplaca.' | '.$veiculo->vedescricao }}</option>
                                        @endif
                                    @endforeach;
                                </select>
                                <p class="help-block">{{ ($errors->has('veiculos') ? $errors->first('veiculos') : '') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="block-salvar">
                        <div class="col-sm-12">
                            <button id="salvarCliente" type="submit" value="save" class="btn salvar btn-lg btn-primary">
                                <span class="glyphicon glyphicon-ok"></span>
                                Salvar
                            </button>
                            <a href="{{url('painel/cadastros/gruposVeiculos')}}" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                        </div>
                    </div>
                </div>
           </form>
        </div>
    </div>
@stop
