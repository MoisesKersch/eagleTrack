@extends('layouts.eagle')
@section('title',  'Cadastro de Pontos')
@section('content')
<ul class="breadcrumb">
	<li><a href="{{url('painel')}}">Painel</a></li>
	<li class="active"><a href="{{url('painel/cadastros/pontos')}}">Pontos</a></li>
	<li class="active">Editar</li>
</ul>
@if(isset($ponto->pocodigo))
	<form class="form-horizontal" id="cadastroPontos"  action="{{url('/painel/cadastros/pontos/editar')}}" method="POST">
@else
	<form class="form-horizontal" id="cadastroPontos"  action="{{url('/painel/cadastros/pontos/cadastrar')}}" method="POST">
@endif
	<div id="divCadastroPonto" class="container" >
		<div class="tab-content col-sm-12">
			<div class="page-title">
				<h2>
					<span class="flaticon-icon012"></span> Cadastro de Pontos
				</h2>
			</div>
			<ul class="nav nav-tabs">
				<li class="active"><a  href="#1" data-toggle="tab">Home</a></li>
				<li><a href="#2" data-toggle="tab">Disponibilidade</a></li>
			</ul>
			<div class="tab-pane active" id="1">
				<div class="panel panel-default" >
					{{ csrf_field() }}
					<input value="{{isset($ponto) ? $ponto->pocodigo : '' }}" name="pocodigo"  type="hidden" />
					<div class="col-xs-12 col-sm-12">
						<div class="col-sm-12">
							<div class="col-xs-12 col-sm-6 {{ ($errors->has('descricao')) ? 'has-error' : '' }} ">
								<label>Descrição*</label>
								@if(isset($_GET['de']))
									<input  id="descricao" value="{{$_GET['de']}}" name="descricao" class="form-control vazio" type="text" maxlength="50" autocomplete="off">
								@else
									<input  id="descricao" value="{{isset($ponto) ? $ponto->podescricao : old('descricao') }}" name="descricao" class="form-control vazio" type="text" maxlength="50" autocomplete="off">
								@endif
								<p class="help-block">{{ ($errors->has('descricao') ? $errors->first('descricao') : '') }}</p>
							</div>

							<div class=" col-xs-12 col-sm-6 busca {{ ($errors->has('veproprietario')) ? 'has-error' : '' }}">
								<label>Cliente*</label>
								<select id="pontoVeproprietario" name="veproprietario" class="form-control">
									@foreach($clientes as $cliente)
										@if(isset($ponto) && $cliente->clcodigo == $ponto->pocodigocliente)
											<option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
										@elseif(isset($_GET['cl']) && $_GET['cl'] == $cliente->clcodigo)
											<option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
										@else
											<option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
										@endif
									@endforeach
								</select>
								<p class="help-block">{{ ($errors->has('veproprietario') ? $errors->first('veproprietario') : '') }}</p>
							</div>
						</div>
						<div class="col-sm-6 col-xs-12">
							<div class="col-xs-6">
								<label>Tipo de Ponto*</label>
								<select id="tipo" value="{{isset($ponto) ? $ponto->potipo : '' }}" name="tipo" class="form-control" maxlength="20" autocomplete="off">
									<option id="optionPontoC" value="C" {{isset($ponto) ? ($ponto->potipo == "C") ? 'selected' : '' : '' }}>Ponto de Coleta</option>
									<option id="optionPontoE" value="E" {{isset($ponto) ? ($ponto->potipo == "E") ? 'selected' : '' : '' }}>Ponto de Entrega</option>
									<option id="optionPontoP" value="P" {{isset($ponto) ? ($ponto->potipo == "P") ? 'selected' : '' : '' }}>Referência</option>
								</select>
							</div>
							<div class="col-xs-6 {{ ($errors->has('pocodigoexterno')) ? 'has-error' : '' }} ">
								<label>Código Externo</label>
								@if(isset($_GET['cd']))
									<input  id="codigoexterno" value="{{ $_GET['cd'] }}" name="pocodigoexterno" class="form-control vazio" type="text" maxlength="50" autocomplete="off" />
								@else
									<input  id="codigoexterno" value="{{ old('pocodigoexterno')? old('pocodigoexterno') : (isset($ponto) ? $ponto->pocodigoexterno : '') }}" name="pocodigoexterno" class="form-control vazio" type="text" maxlength="50" autocomplete="off" />
								@endif
								<p class="help-block">{{ ($errors->has('pocodigoexterno') ? $errors->first('pocodigoexterno') : '') }}</p>
							</div>
						</div>
						<div class="col-sm-12 col-xs-12">
							<hr />
						</div>
					</div>
					<div class="col-sm-12">
						<div class="col-xs-12">
							<label class="col-xs-12">Localização</label>
							<div class="col-xs-12 col-sm-12">
								<label>Pesquisar</label>
								<span class='fa fa-spinner fa-spin fa-span fa-2x  fa-spinner-localiza-ponto'></span>
								<div class="input-group">
									<span class="input-group-addon spanPontoPesquisar" id="spanLimpaTempoLocaliza"
										title="Limpar busca">
										<span class="glyphicon glyphicon-remove"></span>
									</span>
									<input  id="inputPesquisarLocalizacao" value="{{isset($ponto) ? $ponto->poendereco : '' }}" name="endereco" class="form-control vazio" type="text" autocomplete="off">
									<span class="input-group-addon spanPontoPesquisar" id="spanBuscaPonto"
										title="Buscar endereço">
										Buscar <span class="glyphicon glyphicon-search"></span>
									</span>
								</div>
							</div>
							<div class="col-xs-12 col-sm-4 {{($errors->has('cllatitude')) ? 'has-error' : ''}}">
								<label>Latitude</label>
								<input type="text" id="inputPontoLatitude" class="inputLatitude form-control vazio" name="cllatitude" value="{{ isset($ponto) ? $ponto->polatitude : Auth::user()->cliente->cllatitude}}">
								<p class="help-block has-error">{{ ($errors->has('cllatitude') ? $errors->first('cllatitude') : '') }}</p>
							</div>
							<div class="col-xs-12 col-sm-4 {{($errors->has('cllongitude')) ? 'has-error' : '' }}">
								<label>Longitude</label>
								<input type="text" id="inputPontoLongitude" class="inputLongitude form-control vazio" name="cllongitude" value="{{ isset($ponto) ? $ponto->polongitude : Auth::user()->cliente->cllongitude}}">
								<p class="help-block has-error">{{ ($errors->has('cllongitude') ? $errors->first('cllongitude') : '') }}</p>
							</div>
							<div class="col-sm-4">
								<label for="">Região associada</label>
								<input type="text" readonly value="{{isset($ponto->regiao) ? $ponto->regiao->redescricao : ''}}" class="form-control regiao-nome">
								<input type="hidden" value="{{isset($ponto->regiao) ? $ponto->regiao->recodigo : ''}}" id="poregiao" name="poregiao">
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 ">
							<input type="hidden" class="inputRaio" id="inputRaio" name="clraio" value="{{isset($ponto) ? $ponto->poraio : '' }}">
							<div id="mapaPontos" class="mapa-cliente col-xs-12 col-sm-12">
								<div style="position: relative;" id="mapaPrincipal"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="2">
				<div id="divCadastroPonto" class="panel panel-default" >
					<div class="col-sm-12">
						<div class="col-sm-2">
							<label for="">Hora inicial</label>
							<input type="text" class="form-control hora-inicio input-time">
						</div>
						<div class="col-sm-2">
							<label for="">Hora final</label>
							<input type="text" class="form-control hora-fim input-time">
						</div>
						<div class="col-sm-4">
							<label for="">Selecione os dias</label>
							<select class="form-control" id="diaSemanaPontos" multiple="multiple">
								@include('addons.optionsSemana')
							</select>
						</div>
						<div class="col-sm-2"><a href="#" class="btn btn-info bt-add-disponibilidade">Adicionar</a></div>
						<div class="col-sm-12 block-table-disp">
							<table id="tableDispoPontos" class="table table-dispo-pontos">
								<thead>
									<th>Início</th>
									<th>Fim</th>
									<th>Dia da semana</th>
									<th>Ações</th>
								</thead>
								<tbody>
									@if(isset($dispon))
									@foreach($dispon as $disp)
									<tr>
										<td>{{$disp->pdihorainicio}}</td>
										<input type="hidden" name="hora_inicio[]" value="{{$disp->pfihorainicio}}"/>
										<td>{{$disp->pdihorafim}}</td>
										<input type="hidden" name="hora_fim[]" value="{{$disp->pdihorafim}}"/>
										<td>{{$disp->pdidiasemana}}</td>
										<input type="hidden" name="semana[]" value="{{$disp->pdidiasemana}}"/>
										<td>
											<a data-id="{{$disp->pdicodigo}} "href="#" class="removeDispo btn btn-danger">
												<span class="glyphicon glyphicon-remove"></span>
											</a>
										</td>
									</tr>
									@endforeach
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="block-salvar col-xs-12">
				<div class="col-xs-6" style="float:right">
					<button type="submit" value="save" class="btn salvar btn-lg btn-primary"><span class="glyphicon glyphicon-ok"></span>Salvar</button>
					<a href="{{url('/painel/cadastros/pontos')}}" class="btn btn-danger btn-lg "><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
				</div>
			</div>
		</div>
	</div>
</form>
<div class="col-sm-12 col-xs-12" style="margin-top: 20px;">
	@include('addons.mensagens')
</div>

</div>
@stop
