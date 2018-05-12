$('#clientesRegiao, .select-selecionar-todos, #regiaoVeiculo').on('change', function(){
    if($('#clientesRegiao option:selected').length > 0
    && $('#regiaoVeiculo option:selected').length > 0
    && $('#regiaoRegiao option:selected').length > 0 ){
        $("#gerarRelatorioRegiao").attr('disabled', false);
    }else{
        $("#gerarRelatorioRegiao").attr('disabled', true);
        $('.exportar-rel-regiao').attr('disabled',true);
        $("#relatorioRegiaoBody").html('<td colspan="7"><span style="margin-top: 0px;" class="alert alert-info">Para gerar o relatório, selecione a data a placa e a região desejada.</span></td>');
    }
    // if($('select-cliente'))
});

$("#gerarRelatorioRegiao").on('click',function(){
    $("#relatorioRegiaoBody").html('<tr><td class="load" colspan="7"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
    relRegiao($('.rel-regiao'));
})

$(document).ready(function(){
    $("#clientesRegiao").trigger('change')
})

function montaTabelaRelRegiao(placas){
	var tr = '';
    var motorista = 'Sem motorista';

	if(placas.constructor === Object){
		for(i in placas) {
			if(jQuery.isEmptyObject(placas[i])){
				return tr;
			}
            motorista = ''

			tr += '<tr>'
				tr += '<td class="badge placa-relatorio" colspan="7">'+i+'</td>'
			tr += '</tr>';

			for(j in placas[i]){
                if(Object.getOwnPropertyNames(placas[i][j]).length > 1){
                    motorista = placas[i][j].motorista;
    				tr += '<tr>';
                    // tr += '<td>'+placas[i][j].data+'</td>';
    				tr += '<td>'+placas[i][j].hora_entrada+'</td>';
    				tr += '<td>'+placas[i][j].hora_saida+'</td>';
                    if(motorista != null && motorista != undefined)
                        tr += '<td>'+motorista+'</td>';
                    else
                        tr += '<td>'+"Sem motorista"+'</td>';
    				tr += '<td>'+placas[i][j].redescricao+'</td>';
    				tr += '<td>'+placas[i][j].kms+'</td>';
    				tr += '<td>'+placas[i][j].velocidade_media+" km/h"+'</td>';
    				tr += '<td>'+placas[i][j].qtd_paradas+'</td>';
    				tr += '</tr>';
                }
			}
		}
	}
	return tr;
}


function relRegiao(thad) {
    var todos = $(thad).val()

    var form = $(thad).attr('data-form');
    $(form).ajaxForm({
        type:'post',
        beforeSubmit : function (){
            $("#relatorioRegiaoBody").html('<tr><td class="load" colspan="7"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
        },
        success: function(dados) {
            placas = dados.dados;

            dadosRelatorio = dados.dados;
            var tr = '';

            if(placas != null){
                tr = montaTabelaRelRegiao(placas);
                $('.exportar-rel-regiao').attr('disabled',false);
            }

            $("#relatorioRegiaoBody").html(tr)
            if(!jQuery.isEmptyObject(placas)) {
                // $(".exportar_km").attr('disabled', false);
                // dadosKmPercorridoExport('tableKmPercorrido', 7)
            }else{
                $('.exportar-rel-regiao').attr('disabled',true);
                $("#relatorioRegiaoBody").html('<tr><td class="load" colspan="5">Nada encontrado!!!</td></tr>')
            }
        },
        error: function(request, status, error) {
           $("#relatorioRegiaoBody").html('<tr><td class="load" colspan="7">Selecione um período ou quantidade de veículos menor!!!</td></tr>');
        },
    }).submit();
}



$("#clientesRegiao").change(function(){
    var clientes = $(this).val();
    $.post(ROOT+'/painel/relatorios/regiao/dados_filtros',
        {
            clientes: clientes
        },
    function(dados){

        var d = dados
        var dados = d.regioes
        if(dados.length > 0){
            var opt = '<option value="0">Selecionar Todos</option>';
            for(i in dados) {
                opt += '<option value="'+dados[i].recodigo+'">'+dados[i].redescricao+'</option>'
            }
            $("#regiaoRegiao").html(opt);
        }

        var dados = d.veiculos
        if(dados.length > 0){
            var opt = '<option value="0">Selecionar Todos</option>';
            for(i in dados) {
                opt += '<option value="'+dados[i].vecodigo+'">'+dados[i].veplaca+' | '+dados[i].veprefixo+'</option>'
            }
            $("#regiaoVeiculo").html(opt);
        }

    })
})

$(".exportar-rel-regiao").click(function (){
    var html = $(".divImprimir").html();
    var tipo = $(this).attr('data-type');
    var txtBtn = $(this).html();
    var botao = $(this);
    $.ajax({
        url: ROOT+'/painel/relatorios/regiao/exportar',
        type:'post',
        data:{'tipo' : tipo,'titulo' : 'Regiões','html' : html,'arrayDados': JSON.stringify(dadosRelatorio)},
        beforeSend: function(){
                $(botao).html('<span class="fa fa-spinner fa-spin fa-3x fa-fw"></span>')
                        // .attr('disabled',true);
        },
        success: function(dados){
                window.open(ROOT+'/'+dados.dados);
            $(botao).html(txtBtn)
                    .attr('disabled',false);
        },
        error: function(){
            $(botao).html("Erro :(");
        }
    });
});
