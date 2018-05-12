<form class="form-horizontal" role="form" method="POST" action="{{ url('painel/cadastros/gruposMotoristas/desativar/'.$grupo->gmcodigo) }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="panel panel-default">
        <div class="panel-heading">
            Desativar grupo
        </div>
        <div class="panel-body">
            <p>Deseja desativar o grupo {{ $grupo->gmdescricao }}?</p>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-sm btn-danger btn-addon"><i class="glyphicon glyphicon-ok"></i>Desativar</button>
            <a href="{{ url('painel/cadastros/gruposMotoristas') }}" class="btn btn-default btn-sm btn-addon"><i class="glyphicon glyphicon-remove"></i>Cancelar</a>
        </div>
    </div>
</form>
