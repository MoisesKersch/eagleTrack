@extends('layouts.eagle')
@section('content')
<div class="panel panel-usuario">
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a href="{{url('painel/cadastros/usuarios')}}">Usuários</a></li>
        <li class="active">Editar</li>
    </ul>
    <div class="panel-body">
        <div class="col-sm-12">
            <div class="page-title">
                <h2>
                    <span class="flaticon-icon008"></span> Edição de Usuário
                </h2>
            </div>
        </div>
        <div id="formCadastroUsuarios" >
            <form id="formCadastroUsuario" method="POST" action="{{url('painel/cadastros/usuarios/editar/'.$usuario->id)}}" class="form-horizontal">
                {{ csrf_field() }}

                <div class="col-sm-12">
                    <!-- content1 -->
                    <div class="col-sm-4">
                        <div class="col-sm-12 { ($errors->has('email')) ? 'has-error' : '' }}">
                            <label>Email*</label>
                            <input value="{{$usuario->email}}" type="email"  name="email" id="" placeholder="Digite o email" class="form-control vazio">
                            <p class="help-block">{{ ($errors->has('email') ? $errors->first('email') : '') }}</p>
                        </div>
                        <div class="col-sm-12 {{ ($errors->has('name')) ? 'has-error' : '' }}">
                            <label>Login*</label>
                            <input type="text" name="name" id="" placeholder="Digite o login" class="form-control vazio" value="{{$usuario->name}}">
                            <p class="help-block">{{ ($errors->has('name') ? $errors->first('name') : '') }}</p>
                        </div>
                        <div class="col-sm-12 {{ ($errors->has('password')) ? 'has-error' : '' }}">
                            <label>Senha*</label>
                            <input type="password" name="password" id="senhaUser" placeholder="Digite a senha" class="form-control vazio">
                            <p class="help-block">{{ ($errors->has('password') ? $errors->first('password') : '') }}</p>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="col-sm-12 busca {{ ($errors->has('usucliente')) ? 'has-error' : '' }}">
                                <label>Empresa*</label>
                                <select id="" name="multcliente[]" class="form-control select multcliente " multiple>
                                    @foreach($clientes as $cliente)
                                        @if(in_array($cliente->clcodigo, $clid))
                                            <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                        @else
                                            <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <p class="help-block">{{ ($errors->has('usucliente') ? $errors->first('usucliente') : '') }}</p>
                        </div>
                        <div class="col-sm-12">
                            <label>Empresa Principal*</label>
                            <select id="" name="usucliente" class="form-control usucliente">
                                @foreach($usuario->clientes as $cliente)
                                    @if($usuario->usucliente == $cliente->clcodigo)
                                        <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @else
                                        <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="col-sm-12  {{ ($errors->has('usuperfil')) ? 'has-error' : '' }} ">
                            <label>Perfil de Acesso*</label>
                            <input type="hidden" class="usuperfil-cadastrado-edicao" value="{{$usuario->usuperfil}}"></input>
                            <select id="usuPerfil" name="usuperfil" class="form-control"></select>
                            <p class="help-block">{{ ($errors->has('usuperfil') ? $errors->first('usuperfil') : '') }}</p>
                        </div>

                        <div class="col-sm-8">
                            <label>Status Cliente</label>
                            <div class="chec-tipo-cliente">
                                <input type="hidden" name="usuativo" value="N">
                                <span class="col-xs-3">Inativo</span>
                                <label class="col-xs-4 switch">
                                    <input {{$usuario->usuativo == 'S' ? 'checked' : '' }} type="checkbox" name="usuativo" value="S">
                                    <div class="slider round"></div>
                                </label>
                                <span class="col-xs-4">Ativo</span>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <label>Usuário Master</label>
                            <div class="chec-tipo-cliente">
                                <input {{\Auth::user()->usumaster == 'S' ? '': 'disabled'}} type="hidden" name="usumaster" value="N">
                                <span class="col-xs-3">Não</span>
                                <label class="col-xs-4 switch">
                                    <input {{\Auth::user()->usumaster == 'S' ? '': 'disabled'}} {{$usuario->usumaster == 'S' ? 'checked' : '' }} type="checkbox" name="usumaster" value="S">
                                    <div class="slider round"></div>
                                </label>
                                <span class="col-xs-4 psa-juridica">Sim</span>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-11 ">
                        <div class="form-group block-salvar">
                            <a href="{{url('painel/cadastros/usuarios')}}" class="btn btn-lg btn-danger ">
                                Cancelar
                                <span class="glyphicon glyphicon-remove"></span>
                            </a>

                            <button id="salvarUsuario" type="submit" value="save" class="btn btn-lg btn-primary">
                                Salvar
                                <span class="glyphicon glyphicon-ok"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
