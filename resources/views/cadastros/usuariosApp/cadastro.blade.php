@extends('layouts.eagle')
@section('content')
<div class="panel panel-usuario">
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a href="{{url('painel/cadastros/usuarios/app')}}">Usuário aplicativo</a></li>
        @if(isset($usuapp))
            <li class="active">Editar</li>
        @else
            <li class="active">Novo</li>
        @endif
    </ul>
    <div class="panel-body">
        <div class="col-xs-8 col-xs-offset-2">
            <div class="page-title">
                <h2>
                    @if(isset($usuapp))
                        <span class="flaticon-icon001"></span> Edição de usuário aplicativo
                    @else
                        <span class="flaticon-icon001"></span> Cadastro de usuário aplicativo
                    @endif
                </h2>
            </div>
        </div>
        <div id="formCadastroUsuarios" >
            @if(isset($usuapp->usacodigo))
            <form id="formCadastroUsuario" method="POST" action="{{url('painel/cadastros/usuarios/app/editar/'.$usuapp->usacodigo)}}" class="form-horizontal">
            @else
                <form id="formCadastroUsuario" method="POST" action="{{url('painel/cadastros/usuarios/app/cadastrar')}}" class="form-horizontal">
            @endif
                {{ csrf_field() }}
                <div class="col-xs-8 col-xs-offset-2">
                    <div class="col-xs-6  busca {{ ($errors->has('usacliente')) ? 'has-error' : '' }}">
                        <label>Selecione o cliente*</label>
                        <select name="usacliente" class="form-control usacliente">
                            @foreach($clientes as $cliente)
                            @if( (old('usacliente') == $cliente->clcodigo) || ( isset($usuapp) && $cliente->clcodigo == $usuapp->usacliente ))
                                    <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                @else
                                    <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                @endif
                            @endforeach
                        </select>
                        <p class="help-block">{{ ($errors->has('usacliente') ? $errors->first('usacliente') : '') }}</p>
                    </div>
                    <div class="col-xs-6 {{ ($errors->has('usaperfil')) ? 'has-error' : '' }}">
                        <label>Selecione o perfil*</label>
                        <select name="usaperfil" class="usuapp-perfil form-control">
                            <option value="">Selecione</option>
                            <option {{ old('usaperfil') == 'M' ? 'selected' : '' }}  {{ isset($usuapp) && $usuapp->usaperfil == "M" ? 'selected' : '' }} value="M">Motorista/Ajudante</option>
                            <option {{ old('usaperfil') == 'R' ? 'selected' : '' }} {{ isset($usuapp) && $usuapp->usaperfil == "R" ? 'selected' : '' }} value="R">Rastreamento</option>
                        </select>
                        <p class="help-block">{{ ($errors->has('usaperfil') ? $errors->first('usaperfil') : '') }}</p>
                    </div>
                </div>
                <div class="col-xs-8 col-xs-offset-2">

                    <div class="col-xs-6 {{ ($errors->has('usausuario')) ? 'has-error' : ''}}  {{ ($errors->has('usamotorista')) ? 'has-error' : '' }}">
                        <label>Associado a:*</label>
                        <select name="usausuario" data-val="{{ isset($usuapp) ? ($usuapp->usamotorista ? $usuapp->usamotorista : $usuapp->usausuario) : '' }}" class="form-control usaassociado"></select>
                        <p class="help-block">{{ ($errors->has('usamotorista') ? $errors->first('usamotorista') : '') }}</p>
                        <p class="help-block">{{ ($errors->has('usausuario') ? $errors->first('usausuario') : '') }}</p>
                    </div>
                    <div class="col-xs-3">
                        <label>Status Usuário App*</label>
                        <div class="chec-tipo-cliente">
                            <input type="hidden" name="usastatus" value="I">
                            <span class="col-xs-4">Inativo</span>
                            <label class="col-xs-4 switch">
                                <input type="checkbox" name="usastatus" {{ isset($usuapp) && $usuapp->usastatus == 'I' ? '' : 'checked' }} value="A">
                                <div class="slider round"></div>
                            </label>
                            <span class="col-xs-3">Ativo</span>
                        </div>
                    </div>
                    <div class="col-xs-3 ususapp-modo hidden">
                        <label>Modo Rastreador*</label>
                        <div class="chec-tipo-cliente" >
                            <input type="hidden" name="usarastreador" value="N">
                            <span class="col-xs-3">Não</span>
                            <label class="col-xs-4 switch">
                                <input id="usuAppModo" type="checkbox" {{ isset($usuapp) && $usuapp->usarastreador == 'S' ? 'checked' : ''}}  name="usarastreador" value="S">
                                <div class="slider round"></div>
                            </label>
                            <span class="col-xs-4 psa-juridica">Sim</span>
                        </div>
                    </div>
                    <div class="form-group block-salvar">
                        <a href="{{url('painel/cadastros/usuarios/app')}}" class="btn btn-lg btn-danger danger-eagle">
                            <span class="glyphicon glyphicon-remove"></span>
                            Cancelar</a>
                        <button  style="float:right" id="salvarUsuario" type="submit" value="save" class="btn btn-lg btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>
                        Salvar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
