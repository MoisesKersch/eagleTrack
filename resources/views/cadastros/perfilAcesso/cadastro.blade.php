@extends('layouts.eagle')
@section('content')
<div class="panel panel-usuario">
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a href="{{url('painel/cadastros/perfil/acesso')}}">Perfil de acesso</a></li>
        <li class="active">Novo</li>
    </ul>
    <div class="panel-body">

        <div class="col-xs-12 ">
            <div class="page-title">
                <h2>
                    <span class="flaticon-icon031"></span> Cadastro de Perfil de Acesso <span class="load load-perfil-acesso"></span>
                </h2>
            </div>
        </div>
        <div id="cadastroPerfilAcesso">
            <form id="formPerfilAcesso" method="POST" action="{{url('painel/cadastros/perfil/acesso/cadastrar')}}" class="form-horizontal">
                {{csrf_field()}}
                <div class="col-sm-3">
                    <label>Selecione a empresa*</label>
                    <select name="pecliente" class="form-control select-cliente-pe" id="">
                        @foreach($clientes as $cliente)
                            @if(\Auth::user()->usumaster == 'N' && $cliente->clcodigo == \Auth::user()->usucliente)
                                <option selected value="{{$cliente->clcodigo}}">{{$cliente->clfantasia}}</option>
                            @else
                                <option value="{{$cliente->clcodigo}}">{{$cliente->clfantasia}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-4">
                    <label for="">Perfil de acesso*</label>
                    <input type="text" id="idPedescricao" name="pedescricao" placeholder="Descrição" class="form-control">
                    <p id="hasErrorPedescricao" class="hidden help-block" style="color:red;">Campo descrição inválido ou já existe</p>
                </div>
                <div class="col-sm-5">
                    <div class="btn-group bts-acesso">
                        <button type="button" id="btAcessoTotal" class=" btn-permissoes btn btn-default">Acesso total</button>
                        <button type="button" id="btnApenasVisualizar" class="btn-permissoes btn btn-default">Apenas visualizar</button>
                        <button type="button" id="btnMenosExcluir" class="btn-permissoes btn btn-info">Menos excluir <span class="fa fa-check"><span></button>
                    </div>
                </div>
                <div class="col-sm-12 block-categorias">
                    <div class="col-sm-2 categorias-nomes">
                    </div>
                    <div class="col-sm-10 block-permissoes">
                    </div>
                </div>
                <div class="col-sm-12">
                    <input id="ckVeiculos" type="checkbox" name="ckveiculos" > <span class="imput-size" > Selecionar veículos que o perfil <span class="color-red-text" >NÃO</span> terá acesso </span><br>
                </div>
                <div class="col-sm-12 nopadding content-foother">
                    <div class="col-sm-2">
                        <label>Grupo de Veículos</label>
                        <select disabled multiple name="pegrupoveiculo[]" placeholder="Grupo Veículos" class="grupos-veiculos form-control"></select>
                    </div>
                    <div class="col-sm-10">
                        <label>Veículos</label>
                        <select disabled multiple name="peveiculos[]" class="pe-veiculos form-control"></select>
                        <p id="hasErrorPeveiculos" class="hidden help-block" style="color:red;">Selecione um veículo</p>
                    </div>
                </div>
                <div class="block-salvar col-sm-12">
                    <div class="col-sm-7">
                        <p id="hasErrorSelecionados" class="hidden help-block" style="float:right; color:red;">Selecione no mínimo uma opção nas categorias para salvar o perfil</p>
                    </div>
                    <div style="float:right" class="col-sm-5">
                        <button id='perfilAcessoSave' value="save" class="btn salvar-witout-readonly btn-lg btn-primary"><span class="glyphicon glyphicon-ok"></span>Salvar</button>
                        <a href="{{url('/painel/cadastros/perfil/acesso')}}" class="btn btn-danger btn-lg "><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
