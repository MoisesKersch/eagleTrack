//botão de gerar relatório
$("#gerarRelatorioTempoFuncionamento").on('click',function(){
    $("#tableTempoFuncionamentoBody").html('<tr><td class="load" colspan="5"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
    tempoFuncionamento();
});

//Ajax para buscar os dados do relatório e ignição Ligada
function tempoFuncionamento() {
    $('#formTempoFuncionamento').ajaxForm({
        type:'post',
        beforeSubmit : function (){
            $("#tableTempoFuncionamentoBody").html('<tr><td class="load" colspan="5"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
        },
        success: function(retorno){
                var dados = retorno.dados;
                if(dados.length == 0){
		          $("#tableTempoFuncionamentoBody").html('<tr><td class="load" colspan="5">Nenhuma informação foi encontrada, verifique os filtros.</td></tr>');
                }else{
                    //habilita botoes de exportacao
                    $(".exportar-tempoFuncionamento").attr('disabled',false);
                    dadosRelatorio = dados;//recebe dados para exportacao
                    var tr = '';
                    for(prefixo in dados){
                        tr += '<tr><td colspan="5"><span class="badge">'+prefixo+'</span></td></tr>';
                        datas = dados[prefixo];
                        for(data in datas){
                            tr += '<tr><td colspan="5"><b>'+data+'</b></td></tr>';
                            linhas = datas[data];
                            for(linha in linhas){
                                if(linha != 'tempoTotal'){
                                    tr += '<tr>';
                                    tr += '<td>'+linhas[linha].inicio+'</td>';
                                    tr += '<td>'+linhas[linha].fim+'</td>';
                                    tr += '<td>'+linhas[linha].tempo+'</td>';
                                    tr += '<td>'+linhas[linha].ponto+'</td>';
                                    tr += '<td>'+linhas[linha].referencia+'</td>';
                                    tr += '</tr>';
                                }
                            }
                            tr += '<tr><td colspan="2"><td><b>Total do Dia: '+datas[data]['tempoTotal']+'</b></td><td></td></tr>';
                        }
                    }
                }
            $("#tableTempoFuncionamentoBody").html(tr);
        },
        error: function(request, status, error) {
           $("#tableTempoFuncionamentoBody").html('<tr><td class="load" colspan="5">Selecione um período ou quantidade de veículos menor!!!</td></tr>');
		},
    }).submit();
}

//Habilitar e dasabilitar botões de gerar relatório e exportações
$('.select-cliente-funcionamento, .funcionamento-buscar').on('change', function(){
    if($('.select-cliente-funcionamento option:selected').length > 0 && $('.funcionamento-buscar option:selected').length > 0 ){
        $("#gerarRelatorioTempoFuncionamento").attr('disabled', false);
    }else{
        $("#gerarRelatorioTempoFuncionamento").attr('disabled', true);
        $(".exportar-tempoFuncionamento").attr('disabled', true);
        // $(".ligada-exportar").attr('disabled', true);
        $("#tableTempoFuncionamentoBody").html('<td colspan="6"><span style="margin-top: 0px;" class="alert alert-info">Para gerar o relatório, selecione a data e a placa desejada.</span></td>');
    }
});

$(".exportar-tempoFuncionamento").click(function(e){
    var html = $(".divImprimir").html();
    var tipo = $(this).attr('data-type');
    var txtBtn = $(this).html();
    var botao = $(this);
    $.ajax({
        url: ROOT + '/painel/relatorios/tempo/funcionamento/exportar',
        type:'post',
        data:{'tipo'       : tipo,
              'titulo'     : 'Tempo de Funcionamento',
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