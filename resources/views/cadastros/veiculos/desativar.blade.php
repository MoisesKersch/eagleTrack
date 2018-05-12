<form class="form-horizontal" role="form" method="POST" action="{{ url('painel/cadastros/veiculos/desativar/'.$veiculo->vecodigo) }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="panel panel-default">
        <div class="panel-heading">
            Desativar veiculo
        </div>
        <div class="panel-body">
            <p>Deseja desativar o veiculo {{ $veiculo->vedescricao }}?</p>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-sm btn-danger btn-addon"><i class="glyphicon glyphicon-ok"></i>Desativar</button>
            <a href="{{ url('painel/cadastros/veiculos?comModulo=todos&status=ativo') }}" class="btn btn-default btn-sm btn-addon"><i class="glyphicon glyphicon-remove"></i>Cancelar</a>
        </div>
    </div>
</form>
