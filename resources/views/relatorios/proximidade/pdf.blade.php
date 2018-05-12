<h3 style="text-align: center ">Relatorio Proximidade | Eagle track</h3>
<table style="width: 530; text-align: center; font-size: 14px;">
    <caption style="text-align: left;">Relatório de Proximidade {{ $datahorainicio }} a {{ $datahorafim}}</caption>
      <thead style="border-bottom: 1px solid #000;">
          <tr>
              <th>Placa</th>
              <th>Prefixo</th>
              <th>Descrição</th>
              <th>Motorista</th>
              <th>Dia da semana</th>
              <th>Data\Hora</th>
              <th>Ponto Próximo</th>
          </tr>
      </thead>
    @foreach($dados as $i => $dado)
      <tbody>
          <tr>
            <td>{{ $dado->biplaca }}</td>
            <td>{{ $dado->veprefixo }}</td>
            <td>{{ $dado->vedescricao }}</td>
            <td>{{ $dado->mtnome }}</td>
            <td>{{ $dado->dia_semana }}</td>
            <td>{{ $dado->bidataevento }}</td>
            <td>{{ $dado->proximo }}</td>
          </tr>
      </tbody>
    @endforeach
</table>
