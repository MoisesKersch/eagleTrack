$('#clientesKmPercorrido, .select-selecionar-todos, #kmPercorridoVeiculo').on('change', function(){
    if($('#clientesKmPercorrido option:selected').length > 0
    && $('#kmPercorridoVeiculo option:selected').length > 0 ){
        $("#gerarRelatorioKmPercorrido").attr('disabled', false);
    }else{
        $("#gerarRelatorioKmPercorrido").attr('disabled', true);
        $("#tableTempoKmPercorridoBody").html('<td colspan="6"><span style="margin-top: 0px;" class="alert alert-info">Para gerar o relatório, selecione a data e a placa desejada.</span></td>');
    }
    // if($('select-cliente'))
});

$("#gerarRelatorioKmPercorrido").on('click',function(){
    $("#tableTempoKmPercorridoBody").html('<tr><td class="load" colspan="5"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
    kmPercorrido($('.km-percorrido'));
})



function montaTabelaKmPercorrido(placas_datas){
	var tr = '';

	if(placas_datas.constructor === Object){
		for(i in placas_datas) {
			if(jQuery.isEmptyObject(placas_datas[i])){
				return tr;
			}

			tr += '<tr>'
				tr += '<td class="badge placa-relatorio" colspan="6">'+i+' | '+placas_datas[i][0].veprefixo+'</td>'
			tr += '</tr>';

			for(j in placas_datas[i]){
                if(Object.getOwnPropertyNames(placas_datas[i][j]).length > 1){
    				tr += '<tr>';
    				tr += '<td>'+placas_datas[i][j].data+'</td>';
    				tr += '<td>'+placas_datas[i][j].veprefixo+'</td>';
    				tr += '<td>'+placas_datas[i][j].vedescricao+'</td>';
    				tr += '<td>'+placas_datas[i][j].total+'</td>';
    				tr += '</tr>';
                }
			}
		}
	}
	return tr;
}

function kmPercorrido(thad) {
    var todos = $(thad).val()

    var form = $(thad).attr('data-form');
    $(form).ajaxForm({
        type:'post',
        beforeSubmit : function (){
            $("#kmPercorrido table tbody").html('<tr><td class="load" colspan="5"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
        },
        success: function(dados) {
            placas_datas = dados.dados;

            var tr = '';

            if(placas_datas != null){
                tr = montaTabelaKmPercorrido(placas_datas);
            }

            $("#kmPercorrido table tbody").html(tr)
            if(!jQuery.isEmptyObject(placas_datas)) {
                $(".exportar_km").attr('disabled', false);
                dadosKmPercorridoExport('tableKmPercorrido', 7)
            }else{
                $(".exportar_km").attr('disabled', true);
                $("#tableTempoKmPercorridoBody").html('<tr><td class="load" colspan="5">Nada encontrado!!!</td></tr>')
            }
        },
        error: function(request, status, error) {
           $("#tableTempoKmPercorridoBody").html('<tr><td class="load" colspan="5">Selecione um período ou quantidade de veículos menor!!!</td></tr>');
        },
    }).submit();
}


function dadosKmPercorridoExport(table, column){
    table = document.getElementById(table);
    rows = table.getElementsByTagName("TR");
    var dados = ''
    for (i = 1; i < (rows.length); i++) {
        cols = rows[i].getElementsByTagName("TD");
        dados[i] = []
        for(j = 0; j < (cols.length); j++) {
            if(j < column) {
                dados += $(cols[j]).html()+'*i&'
            }
        }
        dados += ';'
    }
    $('.exportar-dados').val(dados)
}


$(".exportar_km").click(function(){
    var id = $(this).attr('data-id');
    var nCol = $(this).attr('data-col');
    var url = $(this).attr('data-url');
    table = document.getElementById(id);
    rows = table.getElementsByTagName("TR");
    var dados = []
    var excel = ''
    for (i = 1; i < (rows.length); i++) {
        cols = rows[i].getElementsByTagName("TD");
        dados[i] = []
        for(j = 0; j < (cols.length); j++) {
            if(j < nCol) {
                if(j > 0){
                    dados[i].push($(cols[j]).html())
                    excel += $(cols[j]).html()+','
                }else{
                    placa = cols[j].getElementsByTagName("span");
                    dados[i].push($(placa).html())
                    excel += $(placa).html()+',';
                }
            }
        }
        excel += ';'
    }
    dados.shift()
    var thad = $(this)
    var type = $(this).attr('data-type')
	var dtinicio = $('.data-inicio').val()+" "+$('.hora-inicio').val()
	var dtfim = $('.data-fim').val()+" "+$('.hora-fim').val()

    $(this).html('<span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>')
        $.post(url,{
            type: type,
            dados: {dados:dados, data_inicio:dtinicio, data_fim:dtfim}
        },
        function(data){
            if(type == 'pdf'){
                window.open(ROOT+'/'+data.dados+'.pdf');
                $(thad).html('PDF')
            }
        })
})

$("#grupoMotorista").change(function(){
    var gm_codigo = $(this).val();

    if(gm_codigo > -1){
      $.ajax({
          url: ROOT+'/painel/relatorios/kmspercorridos/placas_grupo_motorista',
          type: 'GET',
          dataType: 'json',
          data: {'gm_codigo' : gm_codigo},
          success: function(retorno){
            placas = retorno.placas;
            var opts = '';
            for(i in placas){
              opts += "<option selected value='" + placas[i].vecodigo + "'>" + placas[i].veplaca + " | " + placas[i].veprefixo +"</option>";
            }
            $("#selectPlaca").find('option').remove().end().append(opts).change();
          }
      });//fim ajax
    }else{
      $.ajax({
          url: ROOT+'/painel/relatorios/kmspercorridos/get_veiculos_cliente',
          type: 'GET',
          dataType: 'json',
          success: function(retorno){
            placas = retorno.placas;
            var opts = '';
            for(i in placas){
              opts += "<option value='" + placas[i].vecodigo + "'>" + placas[i].veplaca + " | " + placas[i].veprefixo +"</option>";
            }
            $("#selectPlaca").find('option').remove().end().append(opts).change();
          }
      });//fim ajax
    }
})
