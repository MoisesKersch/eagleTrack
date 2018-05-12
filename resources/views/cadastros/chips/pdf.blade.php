<h3 style="text-align: center ">Listagem de chips | Eagle track</h3>
<table style="width: 530; text-align: center; font-size: 14px;">
    <caption style="text-align: left;">Listagem de chips do dia {{ $dia->format('d-m-Y') }} as {{ $dia->format('H:i:s')}}</caption>
    <thead style="border-bottom: 1px solid #000;">
        <tr>
            <th style="text-align: left;">Número</th>
            <th>Código</th>
            <th>Operadora</th>
            <th>Franquia MB</th>
            <th>Franquia SMS</th>
            <th>Custo R$</th>
            <th>ICCID</th>
            <th>SERIAL MODULO<th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($chips as $i => $chip)
            @if($i % 2 != 0)
                <tr style="background-color: #f6f6f6; border-color: #f6f6f6;">
            @else
                <tr>
            @endif
                <td style="text-align: left;">{{ $chip->chnumero }}</td>
                <td>{{ $chip->chcodigo}}</td>
                <td>
                    @if ($chip->choperadora == 1)
                        Vivo
                    @elseif($chip->choperadora == 2)
                        Claro
                    @elseif($chip->choperadora == 3)
                        Tim
                    @elseif($chip->choperadora == 4)
                        Oi
                    @endif

                </td>
                <td>{{ $chip->chfranquiamb }}</td>
                <td>{{ $chip->chfranquiasms }}</td>
                <td>{{ $chip->chcusto }}</td>
                <td>{{ $chip->iccid }}</td>
                @if($chip->modulo != null)
                  <td>{{ $chip->modulo->mocodigo}}</td>
                @else
                  <td></td>
                @endif
                <td>
                    {{ $chip->chstatus == 'A' ? 'Ativo' : 'Inativo' }}
                </td>
           </tr>
        @endforeach
    </tbody>
</table>
