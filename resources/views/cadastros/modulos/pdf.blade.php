<h3 style="text-align: center">Listagem de modulos | Eagle track</h3>
<table>
    <thead style="border-bottom: 1px solid #000;">
        <tr>
            <th style="text-align: left;">Serial</th>
            <th>Modelo</th>
            <th>Proprietário</th>
            <th>Status</th>
            <th>SIM</th>
            <th>IMEI</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dados as $i => $modulo)
            @if($i % 2 != 0)
                <tr style="background-color: #f6f6f6; border-color: #f6f6f6;">
            @else
                <tr>
            @endif
                    <td style="text-align: left;">{{$modulo->mocodigo}}</td>
                    <td>{{$modulo->modulomodelo->mmdescricao}}</td>
                    <td>{{$modulo->proprietario->clnome}}</td>
                    <td>
                        @if($modulo->mostatus == 'D')
                            Desativado
                        @else
                            Ativado
                        @endif
                    </td>
                    <td>{{$modulo->chip? $modulo->chip->chnumero:'Sem Chip'}}</td>
                    <td>{{$modulo->moimei}}</td>
                </tr>
        @endforeach
    </tbody>
</table>
