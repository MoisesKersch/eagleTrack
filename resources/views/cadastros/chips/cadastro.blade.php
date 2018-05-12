@extends('layouts.eagle')
@section('title',  'Cadastro de Chip')
@section('content')
  <ul class="breadcrumb">
      <li><a href="{{url('painel')}}">Painel</a></li>
      <li class="active"><a href="{{url('painel/cadastros/chips')}}">Chips</a></li>
      <li class="active">Novo</li>
  </ul>
    <div class="container">
      <div class="page-title">
          <h2>
              <span class="flaticon-icon016"></span> Cadastro de Chip
          </h2>
      </div>
      <div class="col-sm-12 col-xs-12" style="margin-top: 20px;">
          @include('addons.mensagens')
      </div>

      <div id="formCadastro" class="panel panel-default">
        @if(isset($chip->chcodigo))
          <form class="form-horizontal" action="{{url('/painel/cadastros/chips/cadastrar')}}" method="POST">
        @else
          <form class="form-horizontal" action="{{url('/painel/cadastros/chips/editar')}}" method="POST">
        @endif
          <!-- <div class="divMenuModalCadastro">
              <div class="btn-group" role="group">
                  <button type="button" class="btn btn-primary" onclick="limparCampos();">Limpar campos</button>
              </div>
          </div> -->

          <div class="col-xs-4 col-sm-4">
            <label>Status</label>
            <div>
                <div class="chec-tipo-cliente col-xs-8">
                    <input type="hidden" name="status" value="I" {!! (isset($chip) && ( $chip->chstatus == "I" ))?  'checked' : ''  !!}  >
                    <span class="col-xs-4 psa-fisica">Inativo</span>
                    <label class="col-xs-4 switch">
                    <input type="checkbox" name="status" value="A"   {{ isset($chip)? 'unchecked' : 'checked' }}  {!! (isset($chip) && ( $chip->chstatus == "A" ))?  'checked' : ''  !!} >
                      <div class="slider round"></div>
                    </label>
                    <span class="col-xs-4 psa-juridica">Ativo</span>
                </div>
              </div>
          </div>

          <input  value="{{isset($chip) ? $chip->chcodigo : '' }}" name="chcodigo" class="form-control " type="hidden" />

          <div class="col-xs-12 col-sm-4 {{ ($errors->has('iccid')) ? 'has-error' : '' }} ">
            <label>ICCID*</label>
            <input  id="iccid" value="{{isset($chip) ? $chip->iccid : '' }}" name="iccid" class="form-control vazio iccid " type="text" maxlength="50" autocomplete="off" />
            <p class="help-block">{{ ($errors->has('iccid') ? $errors->first('iccid') : '') }}</p>
          </div>

          <div class="col-xs-12 col-sm-4  {{ ($errors->has('numero')) ? 'has-error' : '' }} ">
            <label>Número*</label>
            <input  id="numero" value="{{isset($chip) ? $chip->chnumero : '' }}" name="numero" class="form-control vazio telefone" type="text" maxlength="50" autocomplete="off">
            <p class="help-block">{{ ($errors->has('numero') ? $errors->first('numero') : '') }}</p>
          </div>

          <div class="col-xs-12 col-sm-2 ">
            <label>Operadora*</label>
              <select id="operadora" value="{{isset($chip) ? $chip->choperadora : '' }}" name="operadora" class="form-control" maxlength="20" autocomplete="off">
                  <option value="1" {{isset($chip) ? ($chip->choperadora == "1") ? 'selected' : '' : '' }} >Vivo</option>
                  <option value="2" {{isset($chip) ? ($chip->choperadora == "2") ? 'selected' : '' : '' }} >Claro</option>
                  <option value="3" {{isset($chip) ? ($chip->choperadora == "3") ? 'selected' : '' : '' }} >Tim</option>
                  <option value="4" {{isset($chip) ? ($chip->choperadora == "4") ? 'selected' : '' : '' }} >Oi</option>
              </select>
          </div>

          <div class="col-xs-12 col-sm-2 {{ ($errors->has('franquiamb')) ? 'has-error' : '' }}">
            <label>Franquia MB</label>
            <input id="franquiamb" value="{{isset($chip) ? $chip->chfranquiamb : 0 }}" name="franquiamb"
                    class="form-control vazio" type="number" min="0" autocomplete="off" max="9999"
                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                    maxlength = "4">
            <p class="help-block">{{ ($errors->has('franquiamb') ? $errors->first('franquiamb') : '') }}</p>
          </div>

          <div class="col-xs-12 col-sm-2  {{ ($errors->has('franquiasms')) ? 'has-error' : '' }}">
            <label>Franquia SMS</label>
            <input id="franquiasms" value="{{isset($chip) ? $chip->chfranquiasms : 0 }}" name="franquiasms" class="form-control vazio" min="0" type="number" max="9999" autocomplete="off"
                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                    maxlength = "4">
            <p class="help-block">{{ ($errors->has('franquiasms') ? $errors->first('franquiasms') : '') }}</p>
          </div>

          <div class="col-xs-12 col-sm-2 {{ ($errors->has('custo')) ? 'has-error' : '' }}">
            <label>Custo R$</label>
            <input  id="custo" value="{{isset($chip) ? $chip->chcusto : '' }}" name="custo" class="form-control vazio money" min="0" type="text" maxlength="6" autocomplete="off">
            <p class="help-block">{{ ($errors->has('custo') ? $errors->first('custo') : '') }}</p>
          </div>

          <input type="hidden" name="_token" value="{{ csrf_token() }}">

          <div class="block-salvar col-xs-12">
               <div class="col-xs-6" style="float:right">
                   <button type="submit" value="save" class="btn salvar btn-lg btn-primary"><span class="glyphicon glyphicon-ok"></span>Salvar</button>
                   <a href="{{url('/painel/cadastros/chips')}}"  class="btn btn-danger btn-lg "><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
               </div>
           </div>

          <!-- <div class="block-salvar">
              <div class="col-xs-12">
                <a style="float:right" href="{{url()->previous()}}" class="btn btn-lg btn-danger">Cancelar</a>
                <button style="float:right" type="submit" value="save" class="btn btn-lg btn-primary">Salvar</button>
              </div>
          </div> -->
      </form>
     </div>
    </div>
@stop
