
<div style="float: left;" class="col-md-12">
	<form type="post" action="{{ url('painel/testes/montaCargas') }}">
		<div style="float: left;display: table;">
			<label>Clientes</label>
			<select name="clientes" id="clientesCarregaCarga">
				@foreach($empresas as $cliente)
					<option value="{{ $cliente->clcodigo }}">{{ $cliente->clcodigo}} - {{ $cliente->clnome }}</option>
				@endforeach
			</select>
		</div>
		<div style="float: left;display: table;">
			<label>Pedidos</label>
			<select multiple name="pedidos[]">
				@foreach($pedidos as $pedido)
					<option value="{{ $pedido->ircodigo }}">{{ $pedido->ircliente }} - {{ $pedido->irnome }}</option>
				@endforeach
			</select>
		</div>
		<div style="float: left;display: table;">
			<label>Veiculos</label>
			<select multiple name="veiculos[]">
				@foreach($veiculos as $veiculo)
					<option value="{{ $veiculo->vecodigo }}">{{ $veiculo->veproprietario }} - {{ $veiculo->vedescricao }}</option>
				@endforeach
			</select>
		</div>
		<button style="float: right;position: relative;" type="submit" class="btn btn-primary">Montar cargas</button>
	</form>
</div>
<hr style="width: 100%; float: left; position: relative;">
<div style="float: left;" class="col-md-12">
	@if (isset($result['mensagem']))
		<div>{{ $result['mensagem'] }}</div>
	@endif
	@if (isset($result['ok']))
		<div>Pedidos carregados por veiculo</div>
		@foreach($result['ok'] as $p => $placa)
			<div style="display: table;float: left;margin-top: 10px;width: 100%;">
				<div>{{ $p }}</div>
				@foreach($placa as $p => $pedido)
					@if($p == 'totais')
						@continue
					@endif
					<div>{{ $pedido->ircodigo }} - {{ $pedido->irnome }} - {{ $pedido->irpeso }} - {{ $pedido->ircubagem }} - ({{ $pedido->polatitude }},{{ $pedido->polongitude }})</div>
				@endforeach
			</div>
		@endforeach
	@endif
	<hr style="width: 100%; float: left; position: relative;">
	@if (isset($result['erros']))
		<div style="float: left;width: 100%;">
			<div style="margin-top: 10px;">Pedidos não carregados</div>
			@foreach($result['erros'] as $p => $pedido)
				<div style="display: table;float: left;margin-top: 10px;width: 100%;">
					<div>{{ $pedido->ircodigo }} - {{ $pedido->irnome }} - {{ $pedido->irpeso }} - {{ $pedido->ircubagem }} - ({{ $pedido->polatitude }},{{ $pedido->polongitude }})</div>
				</div>
			@endforeach
		</div>
	@endif
</div>
