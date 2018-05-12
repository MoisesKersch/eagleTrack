@extends('layouts.eagle')
@section('title',  'Importar entregas e coletas')
@section('content')
<ul class="breadcrumb">
	<li><a href="{{url('painel')}}">Painel</a></li>
	<li class="active">Importar Cargas</li>
</ul>
<div class="container" id="importarCargasRoteirizador">
	<div class="page-title">
		<h2>
			<span class="flaticon-icon039"></span> Importar Cargas
		</h2>
	</div>
	<div class="col-sm-12 col-xs-12" style="margin-top: 20px;">
		@include('addons.mensagens')
	</div>

	<div id="importarCargas" class="panel panel-default">
		<div class="col-sm-3">
			<input type="hidden" id="token" value="{{csrf_token()}}">
			<label for="">Empresa</label>
			<select class="form-control" id="selectEmpresa" name="irempresa">
				<option value="">Selecione um empresa</option>
				@foreach($empresas as $empresa)
					@if(count($empresas) <= 1)
						<option selected value="{{$empresa->clcodigo}}">{{$empresa->clnome}}</option>
					@else
						<option value="{{$empresa->clcodigo}}">{{$empresa->clnome}}</option>
					@endif
				@endforeach
			</select>
		</div>
		<hr class="col-sm-12">
		<div class="margin col-xs-12 dropzone dropzone-mini dz-clickable to-hidden-importacao-cargas">
			<div id="dropZone" action="url.php" class="block push-up-10" data-url="{{url('/painel/roteirizador/importar/cargas')}}">
				<div class="dz-default dz-message col-xs-12">
					<span>Arraste e solte o arquivo com a extensão .kml aqui!</span>
				</div>
			</div>
		</div>
        <div class="block-salvar col-xs-12 to-hidden-importacao-cargas">
           <div class="col-xs-6">
               <a id="gravarImportacao" class="btn disabled salvar btn-primary btn-lg ">
                   <span class="glyphicon glyphicon-ok"></span>Gravar
               </a>
           </div>
        </div>
		<span class="col-sm-12 hidden to-hidden-inverse-importacao-cargas"> Os pontos listados abaixo não estão cadastrados. Clique em [+] para cadastra-los, ou [-] para ignora-los.</span>
        <table class="table" id="tableCondigosNaoEncontrados">
          	<thead>
              	<tr>
                	<th>Código externo</th>
                	<th>Nome</th>
                	<th style="width: 105px;">AÇÕES</th>
              	</tr>
	            </thead>
        	<tbody>
            </tbody>
       	</table>
       	<div class="nao-salvos"></div>
	</div>
</div>
@stop
