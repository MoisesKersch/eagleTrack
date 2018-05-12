//botão de gerar relatório
$("#gerarRelatorioTempoIgnicaoLigada").on('click',function(){
    $("#tableTempoIgnicaoLigadaBody").html('<tr><td class="load" colspan="5"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
    ignicaoLigada();
});

//Ajax para buscar os dados do relatório e ignição Ligada
function ignicaoLigada() {
    var form = $('#tempoIgnicaoVeiculo').attr('data-form');
    $(form).ajaxForm({
        type:'post',
        beforeSubmit : function (){
            $("#tableTempoIgnicaoLigadaBody").html('<tr><td class="load" colspan="5"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
        },
        success: function(retorno){
                var dados = retorno.dados;
                if(dados.length == 0){
		          $("#tableTempoIgnicaoLigadaBody").html('<tr><td class="load" colspan="4">Nenhuma informação foi encontrada, verifique os filtros.</td></tr>');
                }else{
                    //habilita botoes de exportacao
                    $(".exportar-tempoIgnicao").attr('disabled',false);
                    dadosRelatorio = dados;//recebe dados para exportacao
                    var tr = '';
                    console.log(dados);
                    for(placa in dados){
                        tr += '<tr><td colspan="4"><span class="badge">'+placa+'</span></td></tr>';
                        datas = dados[placa];
                        for(data in datas){
                            tr += '<tr><td colspan="4"><b>'+data+'</b></td></tr>';
                            linhas = datas[data];
                            for(linha in linhas){
                                if(linha != 'tempoTotalDia'){
                                    tr += '<tr>';
                                    tr += '<td>'+linhas[linha].horaI+'</td>';
                                    tr += '<td>'+linhas[linha].horaF+'</td>';
                                    tr += '<td>'+linhas[linha].tempo+'</td>';
                                    tr += '<td>'+linhas[linha].motorista+'</td>';
                                    tr += '</tr>';
                                }
                            }
                            tr += '<tr><td colspan="2"><td><b>Total do Dia: '+datas[data]['tempoTotalDia']+'</b></td><td></td></tr>';
                        }
                    }
                }
            $("#tableTempoIgnicaoLigadaBody").html(tr);
        },
        error: function(request, status, error) {
           $("#tableTempoIgnicaoLigadaBody").html('<tr><td class="load" colspan="5">Selecione um período ou quantidade de veículos menor!!!</td></tr>');
		},
    }).submit();
}

//Habilitar e dasabilitar botões de gerar relatório e exportações
$('.select-cliente-ignicao, .ignicao-buscar').on('change', function(){
    if($('.select-cliente-ignicao option:selected').length > 0 && $('.ignicao-buscar option:selected').length > 0 ){
        $("#gerarRelatorioTempoIgnicaoLigada").attr('disabled', false);
    }else{
        $("#gerarRelatorioTempoIgnicaoLigada").attr('disabled', true);
        $(".exportar-ligada").attr('disabled', true);
        $(".ligada-exportar").attr('disabled', true);
        $("#tableTempoIgnicaoLigadaBody").html('<td colspan="6"><span style="margin-top: 0px;" class="alert alert-info">Para gerar o relatório, selecione a data e a placa desejada.</span></td>');
    }
});

$(".exportar-tempoIgnicao").click(function(e){
    var html = $(".divImprimir").html();
    var tipo = $(this).attr('data-type');
    var txtBtn = $(this).html();
    var botao = $(this);
    $.ajax({
        url: ROOT+'/painel/relatorios/tempo/ignicao/ligada/exportar',
        type:'post',
        data:{'tipo'       : tipo,
              'titulo'     : 'Tempo Ignição Ligada',
              'html'       : html,
              'arrayDados' : JSON.stringify(dadosRelatorio)
        },
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
