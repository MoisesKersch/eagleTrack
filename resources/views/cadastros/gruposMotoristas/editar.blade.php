@extends('layouts.eagle')
@section('title')
Editar grupo de motoristas @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a href="{{url('painel/cadastros/gruposMotoristas')}}">Grupos de Motoristas</a></li>
        <li class="active">Editar</li>
    </ul>
    <div class="container">
        <div class="page-title">
            <h2>
                <span class="flaticon-icon009"></span> Edição de grupos de motoristas
            </h2>
        </div>
        <div id="formCadastro" class="panel panel-default">
            <form id="formCadastroCliente" method="POST" action="{{url('painel/cadastros/gruposMotoristas/editar/'.$grupo->gmcodigo)}}" class="form-horizontal" enctype="multipart/form-data">
                {{ csrf_field() }}
                    <div class="col-sm-12 form-group">
                        <div class="row">
                            <div class="col-sm-6 {{ ($errors->has('descricao')) ? 'has-error' : '' }}">
                                <label>Grupo de Motoristas*</label>
                                <input type="text" placeholder="Digite o nome do grupo de motoristas"
                                name="descricao" value="{{ old('gmdescricao') ? : $grupo->gmdescricao }}"
                                class="form-control desabilitar">
                                <p class="help-block">{{ ($errors->has('descricao') ? $errors->first('descricao') : '') }}</p>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="campo-status" name="gmstatus" value="A">
                    <div class="col-sm-offset-1 col-sm-12 form-group">
                        <!-- <div class="col-sm-12"> -->
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Empresa</label>
                                <select name="gmcliente" value="{{old('$grupo->gmcliente')}}" class="form-control desabilitar select2-noClear">
                                    @foreach ($clientes as $key => $cliente)
                                        <option {{ $grupo->gmcliente == $cliente->clcodigo ? 'selected' : '' }} value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                    @endforeach;
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="block-salvar">
                        <div class="col-sm-12">
                            <button id="salvarCliente" type="submit" value="save" class="btn salvar btn-lg desabilitar btn-primary">
                                <span class="glyphicon glyphicon-ok"></span>
                                Salvar
                            </button>
                            <a href="{{url('painel/cadastros/gruposMotoristas')}}" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                        </div>
                    </div>
                </div>
           </form>
        </div>
    </div>
@stop
