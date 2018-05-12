<form class="form-horizontal" role="form" method="POST" action="{{ url('painel/cadastros/jornadaTrabalho/desativar/'.$jt->jtcodigo) }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="panel panel-default">
        <div class="panel-heading">
            Desativar jornada de trabalho
        </div>
        <div class="panel-body">
            <p>Deseja desativar a jornada de trabalho {{ $jt->jtdescricao }}?</p>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-sm btn-danger btn-addon"><i class="glyphicon glyphicon-ok"></i>Desativar</button>
            <a href="{{ url('painel/cadastros/jornadaTrabalho') }}" class="btn btn-default btn-sm btn-addon"><i class="glyphicon glyphicon-remove"></i>Cancelar</a>
        </div>
    </div>
</form>
