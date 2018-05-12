    <h3 style="text-align: center ">Tempo ignição ligada | Eagle track</h3>
    <table style="width: 530; text-align: center; font-size: 14px;">
        <caption style="text-align: left;">Tempo ignição ligada do dia {{ $dia->format('d-m-Y') }} as {{ $dia->format('H:i:s')}}</caption>
                <thead style="border-bottom: 1px solid #000;">
                    <tr>
                        <th>Data/Hora Início</th>
                        <th>Data/Hora Fim</th>
                        <th>Tempo ligado</th>
                        <th>Motorista</th>
                    </tr>
            </thead>

        @foreach($dados as $i => $dado)
                @if(isset($dado['placa']))
                    <tr>
                        <td colspan="4" style="text-align: left; border-bottom:1px solid #000">
                            <span>Placa: {{ $dado['placa'] ? : '' }} </span>
                        </td>
                    </tr>
                @endif
                @if(isset($dado['inicio']))
                                    @endif
            <tbody>
                @if(isset($dado['inicio']))
                    @if($i % 2 != 0)
                        <tr style="background-color: #f6f6f6; border-color: #f6f6f6;">
                    @else
                        <tr>
                    @endif
                        <td>{{ $dado['inicio'] }}</td>
                        <td>{{ $dado['fim'] }}</td>
                        <td>{{ $dado['total'] }}</td>
                        <td>{{ $dado['motorista'] }}</td>
                    @endif
                </tr>
            </tbody>
            @endforeach
    </table>

