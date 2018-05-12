@extends('layouts.eagle')
@section('title',  'Importação de Pontos')
@section('content')
  <ul class="breadcrumb">
      <li><a href="{{url('painel')}}">Painel</a></li>
      <li class="active"><a href="{{url('painel/cadastros/pontos')}}">Pontos</a></li>
      <li class="active">Importação</li>
  </ul>
  <div class="container">
      <input type="hidden" id="token" value="{{csrf_token()}}">
      <div class="page-title">
          <h2>
              <span class="flaticon-icon012"></span> Importação de arquivos de pontos
          </h2>
      </div>
      <div id="divImportarPonto" class="marginPattern paddingPattern panel panel-default">

          <div class="col-xs-12 marginPattern">
              <div class="select-clientes">
                  <span class="label-botoes-table">Selecione a empresa</span>
                  <select id="empresa_importacao" class="select-cliente-pontos" name="empresa_importacao[]">
                      @foreach($clientes as $cliente)
                          @if(count($clientes) == 1)
                              <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                          @else
                              <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                          @endif
                      @endforeach
                  </select>
              </div>
          </div>

            <div class="col-xs-4 marginPattern">
              <label>Tipo de Ponto*</label>
                <select id="tipo_ponto_importacao" name="tipo" class="form-control" maxlength="20" autocomplete="off">
                  <option value="0">Selecione</option>
                  <option value="C">Ponto de Coleta</option>
                  <option value="E">Ponto de Entrega</option>
                  <option value="P">Restaurante/Posto Combustível</option>
                </select>
            </div>

            <div class="col-xs-6 marginPattern">
              <label>Raio padrão do ponto em metros*</label>
              <input id="raio_ponto_importacao" type="number" name="tipo" class="form-control" value="50" min="50" step="50" max="1000"/>
            </div>


            <hr class="col-xs-12" />

            <div class="margin col-xs-12 dropzone dropzone-mini dz-clickable">
              <div id="dropZone" action="url.php" class="block push-up-10" data-url="{{url('/painel/cadastros/pontos/importar')}}">
                  <div class="dz-default dz-message col-xs-12">
                      <span>Arraste e solte o arquivo com a extensão .kml aqui!</span>
                  </div>
              </div>
            </div>


            <div class="block-salvar col-xs-12">
               <div class="col-xs-6">
                   <a id="gravarImportacao" class="btn disabled salvar btn-primary btn-lg ">
                       <span class="glyphicon glyphicon-ok"></span>Gravar
                   </a>
               </div>
               <div class="col-xs-6">
                   <a id="descartarConflitos" class="btn disabled btn-danger btn-lg ">
                       <span class="glyphicon glyphicon-trash"></span>Descartar Conflitos
                   </a>
               </div>
            </div>

            <hr class="col-xs-12" />

            <div class="col-xs-12" id="texto">
                <h2>
                    <span class="fa "></span> Listagem de Conflitos da Importação
                </h2>
            </div>
            <table class="table" id="tableConflitosImportacao">
            <!--<table id="tableConflitosImportacao" class="table datatable">-->
              <thead>
                  <tr>
                    <th>DESCRIÇÃO DO PONTO</th>
                    <th>TIPO DO PONTO</th>
                    <th>RAIO</th>
                    <th hidden>CLIENTE</th>
                    <th>LATITUDE</th>
                    <th>LONGITUDE</th>
                    <th>CODIGO EXTERNO</th>
                    <th style="width: 105px;">AÇÕES</th>
                  </tr>
              </thead>
              <tbody>
              </tbody>
           </table>
           <div class="col-sm-12 erros-importacao ">
           </div>
      </div>
  </div>
@stop
@section('js')
    <script src="{{ asset('js/cadastros/importacaokml.js') }}"></script>
@stop
