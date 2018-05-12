<form class="form-horizontal" role="form" method="POST" action="{{ url('painel/cadastros/clientes/desativar/'.$cliente->clcodigo) }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="panel panel-default">
        <div class="panel-heading">
            Desativar cliente
        </div>
        <div class="panel-body">
            <p>Deseja desativar o cliente {{ $cliente->clnome }}?</p>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-sm btn-danger btn-addon"><i class="glyphicon glyphicon-ok"></i>Desativar</button>
            <a href="{{ url('painel/cadastros/clientes') }}" class="btn btn-default btn-sm btn-addon"><i class="glyphicon glyphicon-remove"></i>Cancelar</a>
        </div>
    </div>
</form>
