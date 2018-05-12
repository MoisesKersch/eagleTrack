<h3 style="text-align: center ">Listagem de clientes | Eagle track</h3>
<table style="width: 530; text-align: center; font-size: 14px;">
    <caption style="text-align: left;">Listagem de clientes do dia {{ $dia->format('d-m-Y') }} as {{ $dia->format('H:i:s')}}</caption>
    <thead style="border-bottom: 1px solid #000;">
        <tr>
            <th style="text-align: left;">Nome</th>
            <th>Cidade</th>
            <th>Endereço</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>Tipo pessoa</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $cliente)
            @if($i % 2 != 0)
                <tr style="background-color: #f6f6f6; border-color: #f6f6f6;">
            @else
                <tr>
            @endif
                <td style="text-align: left;">{{ $cliente->clnome }}</td>
                <td>{{$cliente->cidade}}</td>
                <td>{{ $cliente->cllogradouro }}</td>
                <td>{{ $cliente->telefone }}</td>
                <td>{{ $cliente->email }}</td>
                <td>
                    {{ $cliente->cltipo == 'J' ? 'Jurídica' : 'Física' }}
                </td>
           </tr>
        @endforeach
    </tbody>
</table>
