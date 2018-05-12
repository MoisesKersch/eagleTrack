<h3 style="text-align: center ">Relatório tempo parado | Eagle track</h3>
<table style="width: 530; text-align: center; font-size: 14px;">
<caption style="text-align: left;">Relatório tempo parado do dia {{ $dia->format('d-m-Y') }} as {{ $dia->format('H:i:s')}}</caption>
    <thead style="border-bottom: 1px solid #000;">
        <tr>
            <th>Data</th>
            <th>Identificação</th>
            <th>Semana</th>
            <th>Trabalhada</th>
            <th>Falta</th>
            <th>Extra</th>
            <th>Extra 100%</th>
            <th>Ad.Noturno</th>
            <th>Extra Noturno</th>
            <th>Hora Espera</th>
            <th>Int.Refeição</th>
        </tr>
    </thead>
    <tbody>
        @foreach($jornada as $i => $trabalho)
            @if($i % 2 != 0)
                <tr style="background-color: #f6f6f6; border-color: #f6f6f6;">
            @else
                <tr>
            @endif
                <td>{{ $trabalho->fedataentrada}}</td>
                <td>{{ $trabalho->mtnome }}</td>
                <td>{{ $trabalho->fedataentrada }}</td>
                <td>{{ $trabalho->fehoratrabalhada }}</td>
                <td>{{ $trabalho->fehorafalta }}</td>
                <td>{{ $trabalho->fehoraextra }}</td>
                <td>{{ $trabalho->porcento }}</td>
                <td>{{ $trabalho->fehonoturna }}</td>
                <td>{{ $trabalho->feextranoturno }}</td>
                <td>{{ $trabalho->fehoraespera }}</td>
                <td>{{ $trabalho->feintervalo }}</td>
           </tr>
        @endforeach
    </tbody>
</table>
<!--<div style="page-break-after: always;"></div>-->

