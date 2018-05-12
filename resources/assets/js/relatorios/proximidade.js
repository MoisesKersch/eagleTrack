
$(".proximidade").change(function(){
    proximidade($(this));
})

function proximidade(thad) {
    var todos = $(thad).val()
    var t = todos.indexOf("0")

    if(Array.isArray(todos) && t == 0) {
        //$(".proximidade-clientes").children('.todos-preoximidade').remove()
        $(".proximidade-clientes").children().attr('selected', true)
        $(".proximidade-clientes").select2()
        var selected = 'selected'
    }

    var form = $(thad).attr('data-form');
    $(form).ajaxForm({
        type:'post',
        beforeSubmit : function (){
            $("#proximidade table tbody").html('<tr><td class="load" colspan="5"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
        },
        success: function(dados) {
            placa = dados.placas
            if(placa.length > 0) {
                pl = ''
                for(j in placa) {
                    pl += '<option '+selected+' value="'+placa[j].vecodigo+'">'+placa[j].veplaca+'</option>'
                }
                $('.buscar-veiculos-prox').html(pl)
            }

            var veiculos = dados.dados;
            var tr = '';
            for(i in veiculos) {
                  tr += '<tr>';
                    tr += '<td><span class="badge">'+veiculos[i].biplaca+'</td>';
                    tr += '<td>'+veiculos[i].veprefixo+'</td>';
                    tr += '<td>'+veiculos[i].vedescricao+'</td>';
                    tr += '<td>'+veiculos[i].mtnome+'</td>';
                    tr += '<td>'+veiculos[i].dia_semana+'</td>';
                    tr += '<td>'+veiculos[i].bidataevento+'</td>';
                    tr += '<td>'+veiculos[i].tempo_parado+'</td>';
                    tr += '<td>'+veiculos[i].proximo+'</td>';
                  tr += '</tr>';
            }
            if(!jQuery.isEmptyObject(veiculos)) {
            $("#proximidade table tbody").html(tr)
                $(".exportar_proximidade").attr('disabled', false);
            }else{
                $(".exportar_proximidade").attr('disabled', true);
                $("#proximidade table tbody").html('<tr><td class="load" colspan="5">Nada encontrado!!!</td></tr>')
            }
        }
    }).submit();
}

$(".exportar_proximidade").click(function(e){
    var dataInicio = $(".data-inicio").val()
    var dataFim = $(".data-fim").val()
    var horaInicio = $(".hora-inicio").val()
    var horaFim = $(".hora-fim").val()
    var type = $(this).attr('data-type')
    var id = $('#selectPlaca').val()
    var get = 'data_inicio='+dataInicio+'&data_fim='+dataFim+'&hora_inicio='+horaInicio+'&hora_fim='+horaFim+'';
    get += '&type='+type+'&buscar='+id+'';
    window.open(ROOT+'/painel/relatorios/proximidade/exportar?'+get+'', '_blank');
})

$("#grupoMotorista").change(function(){
    var gm_codigo = $(this).val();
    if(gm_codigo > -1){
      $.ajax({
          url: ROOT+'/painel/relatorios/proximidade/placas_grupo_motorista',
          type: 'GET',
          dataType: 'json',
          data: {'gm_codigo' : gm_codigo},
          success: function(retorno){
            placas = retorno.placas;
            var opts = '';
            for(i in placas){
              console.log(placas[i].vecodigo);
              opts += "<option selected value='" + placas[i].vecodigo + "'>" + placas[i].veplaca + "</option>";
            }
            $("#selectPlaca").find('option').remove().end().append(opts).change();
          }
      });//fim ajax
    }else{
      $.ajax({
          url: ROOT+'/painel/relatorios/proximidade/placas_grupo_motorista',
          type: 'GET',
          dataType: 'json',
          success: function(retorno){
            placas = retorno.placas;
            var opts = '';
            for(i in placas){
              opts += "<option value='" + placas[i].vecodigo + "'>" + placas[i].veplaca + "</option>";
            }
            $("#selectPlaca").find('option').remove().end().append(opts).change();
          }
      });//fim ajax
    }
})
