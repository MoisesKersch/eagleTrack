//variavais
dadosRelatorio = [];

$("#gerarRelatorioAcionamentoPortas").on('click',function(){
    $(this).attr('disabled', true);
    $("#tableAcionamentoPortasBody").html('<tr><td class="load" colspan="7"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
    acionamentoDasPortas($('.tempo-data-inicio'));
})

$(".selecao-portas").click(function() {
    $('.selecao-portas').removeClass('btn-primary').addClass('btn-default').blur();
    $(this).addClass('btn-primary');
    $('.input-portas').val($(this).data('value'));
    blockBtnAcp();
});

$('.select-cliente, .tempo-buscar-acp, .tempo-data-inicio, .tempo-data-fim').change(function() {
    blockBtnAcp();
});

function blockBtnAcp() {
    if ($('.select-cliente option:selected').length > 0 && $('.tempo-buscar-acp option:selected').length > 0 && $('.input-portas').val() != '') {
        $("#gerarRelatorioAcionamentoPortas").attr('disabled', false);
    } else {
        $("#gerarRelatorioAcionamentoPortas").attr('disabled', true);
        $(".exportar-acionamento-portas").attr('disabled', true);
        $(".tempo-exportar").attr('disabled', true);
        $("#tableAcionamentoPortaBody").html('<td colspan="6"><span style="margin-top: 0px;" class="alert alert-info">Para gerar o relatório, selecione a data e a placa desejada.</span></td>');
    }
}

function montaTabelaAcionamentoPortas(dados_acp) {
	var tr = '';
    var countDados = 0;

	// if (dados_acp.constructor === Object) {
        console.log(i, dados_acp);

		for (i in dados_acp) {
			if (jQuery.isEmptyObject(dados_acp[i])) {
				return tr;
			}


			tr += '<tr>';
                tr += '<td colspan="3"><span class="badge placa-relatorio">'+i+'</span></td>';
                tr += '<td colspan="4"><span class=""> Soma do tempo da porta acionada: '+dados_acp[i].tempoAbertoPlaca+'</span></td>';
			tr += '</tr>';

			for (j in dados_acp[i]) {
                if ((Object.keys(dados_acp[i]).length-1) == countDados || j == "tempoAbertoPlaca") continue;
                if (Object.getOwnPropertyNames(dados_acp[i][j]).length > 1) {
                    tr += '<tr>';
        				tr += '<td class="data-relatorio" colspan="3"><strong>Data: '+j+'</strong></td>';
        				tr += '<td class="data-relatorio" colspan="4"><strong>Soma do tempo da porta acionada: '+dados_acp[i][j].tempoAbertoData+' - Abriu '+dados_acp[i][j].countPortasAc+' vez(es)</strong></td>';
        			tr += '</tr>';
        			for (k in dados_acp[i][j]) {
                        if (!(dados_acp[i][j][k].data === undefined)) {
            				tr += '<tr>';
                            tr += '<td>'+dados_acp[i][j][k].numPorta+'</td>';
                            tr += '<td>'+dados_acp[i][j][k].horaInicio+'</td>';
                            tr += '<td>'+dados_acp[i][j][k].horaFinal+'</td>';
            				tr += '<td style="display:none">'+dados_acp[i][j][k].dataFinal+'</td>';
            				tr += '<td>'+dados_acp[i][j][k].tmpPortaAberta+'</td>';
            				tr += '<td>'+dados_acp[i][j][k].endereco+'</td>';
            				tr += '<td>'+dados_acp[i][j][k].localposicao+'</td>';
                            tr += '<td class="hidden-print"><a title="Clique para ver local no mapa" href="#" onClick="window.open(\'http://maps.google.com/maps?q=loc:'+dados_acp[i][j][k].posicao+'\',\'_blank\')"><span class="glyphicon glyphicon-screenshot"></span></a></td>';
            				tr += '</tr>';
                        }
        			}
                }
                countDados++;
			}
		}
	// }

	return tr;
}

function acionamentoDasPortas(thad) {
    // form = $(thad).attr('data-form');
	$('.form-acionamento-porta').ajaxForm({
        type:'post',
        success: function(dados) {
            dadosRelatorio = dados.acionamentoPortas.original.array;
            var tr = '';

            if (dadosRelatorio != null) {
                tr = montaTabelaAcionamentoPortas(dadosRelatorio);
            }

            $("#tableAcionamentoPortasBody").html(tr);

            if (!jQuery.isEmptyObject(dadosRelatorio)) {
                $(".exportar-acionamento-portas").attr('disabled', false);
                $(".tempo-exportar").attr('disabled', false);
            } else {
                $(".exportar-acionamento-portas").attr('disabled', true);
                $(".tempo-exportar").attr('disabled', true);
                $("#tableAcionamentoPortasBody").html('<tr><td class="load" colspan="7">Nada encontrado!!!</td></tr>')
            }
        },
        error: function(request, status, error) {
            $("#tableAcionamentoPortaBody").html('<tr><td class="load" colspan="7">Selecione um período ou quantidade de veículos menor!!!</td></tr>');
        },
    }).submit();
}

$(".exportar-acionamento-portas").click(function(e) {
    var html = $(".divImprimir").html();
    var tipo = $(this).attr('data-type');
    var txtBtn = $(this).html();
    var botao = $(this);
    $.ajax({
        url: ROOT+'/painel/relatorios/acionamentoPortas/exportar',
        type:'post',
        data:{'tipo' : tipo,'titulo' : 'Acionamento de Porta','html' : html,'arrayDados': JSON.stringify(dadosRelatorio)},
        beforeSend: function(){
                $(botao).html('<span class="fa fa-spinner fa-spin fa-3x fa-fw"></span>')
                        .attr('disabled',true);
        },
        success: function(dados){
                window.open(ROOT+'/'+dados.dados);
            $(botao).html(txtBtn)
                    .attr('disabled',false);
        },
        error: function(data){
            $(botao).html(txtBtn)
                    .attr('disabled',false);
            // $(botao).html("Erro :(");
        }
    });
});
