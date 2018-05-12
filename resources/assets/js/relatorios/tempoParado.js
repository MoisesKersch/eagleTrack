//variavais
dadosRelatorio = [];

$("#gerarRelatorioTempoParado").on('click',function(){
    $("#tableTempoParadoBody").html('<tr><td class="load" colspan="8"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
    tempoParado($('.tempo-data-inicio'));
})

$(".tempo-opcoes").click(function(){
    var input = $(this).attr('data-att')
    if($(this).hasClass('btn-success')) {
        $(this).removeClass('btn-success')
        $(this).addClass('btn-primary')
        $(this).siblings('.'+input).val('on')
    }else{
        $(this).removeClass('btn-primary')
        $(this).addClass('btn-success')
        $(this).siblings('.'+input).val('off')

    }
    $(this).siblings('.'+input)
})

function montaTabelaTempoParado(placas_datas){
	var tr = '';

	if(placas_datas.constructor === Object){
		for(i in placas_datas){
			if(jQuery.isEmptyObject(placas_datas[i])){
				return tr;
			}

			tr += '<tr>';
				tr += '<td colspan="8"><span class="badge placa-relatorio">'+i+'</span></td>';
			tr += '</tr>';
			for(j in placas_datas[i]){
                if(Object.getOwnPropertyNames(placas_datas[i][j]).length > 1){
                    tr += '<tr>';
                        tr += '<td class="data-relatorio" colspan="3"><strong>Data: '+j+'</strong></td>';
        				tr += '<td class="data-relatorio" colspan="5"></td>';
                    tr += '</tr>';
                    console.log(placas_datas);
                    for(k in placas_datas[i][j]){
                        if(!(placas_datas[i][j][k].bidataevento === undefined)){
                            tr += '<tr>';
                            tr += '<td>'+placas_datas[i][j][k].dataInicio+'</td>';
                            tr += '<td>'+placas_datas[i][j][k].dataFim+'</td>';
                            tr += '<td>'+placas_datas[i][j][k].tempo+'</td>';
                            tr += '<td>'+placas_datas[i][j][k].biendereco+'</td>';
                            tr += '<td>'+placas_datas[i][j][k].ponto+'</td>';
                            tr += '<td>'+placas_datas[i][j][k].regiao+'</td>';
                            if(placas_datas[i][j][k].biignicao == 1){
                                tr += '<td>Ligada</td>';
                            }else{
                                tr += '<td>Desligada</td>';
                            }
                            tr += '<td class="hidden-print"><a title="Clique para ver local no mapa" href="#" onClick="window.open(\'http://maps.google.com/maps?q=loc:'+placas_datas[i][j][k].bilatlog+'\',\'_blank\')"><span class="glyphicon glyphicon-screenshot"></span></a></td>';
                            tr += '</tr>';
                        }
        			}
                    tr += '<tr>';
                        tr += '<td class="data-relatorio" colspan="3"><strong>Total de tempo parado: '+placas_datas[i][j].totalizadorTempo+'</strong></td>';
                        tr += '<td class="data-relatorio" colspan="5"></td>';
                    tr += '</tr>';
                    tr += '<tr><td colspan="8"></td></tr>';
                }
			}
		}
	}
	return tr;
}


function tempoParado(thad) {
    form = $(thad).attr('data-form');
	$(form).ajaxForm({
        type:'post',
        success: function(dados) {
            var placas_datas = dados.tempoParado;
            dadosRelatorio = dados.tempoParado;
            var tr = '';
            if(placas_datas != null){
                tr = montaTabelaTempoParado(placas_datas);
            }
            $("#tempoParado table tbody").html(tr)
            if(!jQuery.isEmptyObject(placas_datas)) {
                $(".exportar-parado").attr('disabled', false);
                $(".tempo-exportar").attr('disabled', false);
            }else{
                $(".exportar-parado").attr('disabled', true);
                $(".tempo-exportar").attr('disabled', true);
                $("#tableTempoParadoBody").html('<tr><td class="load" colspan="8">Nada encontrado!!!</td></tr>')
            }
        },
        error: function(request, status, error) {
               $("#tableTempoParadoBody").html('<tr><td class="load" colspan="8">Selecione um período ou quantidade de veículos menor!!!</td></tr>');
        },
    }).submit();
}

$('.select-cliente, .tempo-buscar').on('change', function(){
    if($('.select-cliente option:selected').length > 0
    && $('.tempo-buscar option:selected').length > 0 ){
        $("#gerarRelatorioTempoParado").attr('disabled', false);
    }else{
        $("#gerarRelatorioTempoParado").attr('disabled', true);
        $(".exportar-parado").attr('disabled', true);
        $(".tempo-exportar").attr('disabled', true);
        $("#tableTempoParadoBody").html('<td colspan="8"><span style="margin-top: 0px;" class="alert alert-info">Para gerar o relatório, selecione a data e a placa desejada.</span></td>');
    }
});

$(".exportar-parado").click(function(e){
    var html = $(".divImprimir").html();
    var tipo = $(this).attr('data-type');
    var txtBtn = $(this).html();
    var botao = $(this);
    $.ajax({
        url: ROOT+'/painel/relatorios/tempo/parado/exportar',
        type:'post',
        data:{'tipo' : tipo,'titulo' : 'Tempo Parado','html' : html,'arrayDados': JSON.stringify(dadosRelatorio)},
        beforeSend: function(){
                $(botao).html('<span class="fa fa-spinner fa-spin fa-3x fa-fw"></span>')
                        .attr('disabled',true);
        },
        success: function(dados){
                window.open(ROOT+'/'+dados.dados);
            $(botao).html(txtBtn)
                    .attr('disabled',false);
        },
        error: function(){
            console.log("Erro");
            $(botao).html("Erro :(");
        }
    });
});

$('.time-parado').on('click', function(){
    if($(this).hasClass('activated')){
        $(this).removeClass('activated');
        $('.inp-time-parado').val('0');
    }else{
        $('.time-parado').removeClass('activated');
        $(this).addClass('activated');
        $('.inp-time-parado').val($(this).data('time'));
    }

});
