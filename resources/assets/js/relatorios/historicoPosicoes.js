
$("#selectClienteHistoricoPosicoes").change(function(){
    var empresas = $("#selectClienteHistoricoPosicoes").val();
    $("#selectVeiculosHistoricoPosicoes").html("").attr("disabled",true);
    $("#selectGrpMotoristasHistoricoPosicoes").html("").attr("disabled",true);
    //carrega veiculos
    $.ajax({
        type:'post',
        url:ROOT+'/painel/relatorios/historico/posicoes/carrega/veiculos',
        data: {'empresas' : empresas},
        success: function(retorno){
            retorno = retorno[0];
            var option;
            option += "<option value=\"\" >Selecionar Todos</option>";
            for(var x in retorno){
                option += "<option value='"+retorno[x].vecodigo+"' >"+retorno[x].veplaca+" | "+retorno[x].veprefixo+"</option>";
            }
            $("#selectVeiculosHistoricoPosicoes").html(option);
            if(retorno.length > 0) $("#selectVeiculosHistoricoPosicoes").attr("disabled",false);
        }
    });
    //carrega grupos motoristas
    $.ajax({
        type:'post',
        url:ROOT+'/painel/relatorios/historico/posicoes/carrega/grupos/motoristas',
        data: {'empresas' : empresas},
        success: function(retorno){
            retorno = retorno[0];
            var option;
            for(var x in retorno){
                option += "<option value='"+retorno[x].gmcodigo+"' >"+retorno[x].gmdescricao+"</option>";
            }
            $("#selectGrpMotoristasHistoricoPosicoes").html(option);
            if(retorno.length > 0) $("#selectGrpMotoristasHistoricoPosicoes").attr("disabled",false);
        }
    });
});

$("#btnGerarHistoricoPosicoes").click(function(){
    var html;
    $(this).attr('disabled',true);
    var dataInicio = $("#inputDataInicio").val();
    var dataFim = $("#inputDataFim").val();
    var empresas = $("#selectClienteHistoricoPosicoes").val();
    var veiculos = $("#selectVeiculosHistoricoPosicoes").val();
    var grupos = $("#selectGrpMotoristasHistoricoPosicoes").val();
    $.ajax({
        type: 'POST',
        url: ROOT+'/painel/relatorios/historico/posicoes/gerar',
        data: {'dini':dataInicio,'dfim':dataFim,'empresas':empresas,'veiculos':veiculos,'grupos':grupos},
        beforeSend: function(){
            $("#corpoTabelaHistoricoPosicoes").html('<tr><td colspan="7"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span><span>Atenção: Devido a quantidade de dados analisados, esse relatório pode demorar para ser gerado.</span></td></tr>');
        },
        success: function(retorno){
                    $("#btnGerarHistoricoPosicoes").removeAttr('disabled');
                    var retorno = retorno[0];
                    dadosRelatorio = retorno;
                    if(retorno == false){
                        html = "<tr><td colspan='7'><span style='margin-top: 0px;' class='alert alert-danger h3'>Não encontramos nenhuma informação, verifique os filtros selecionados.</span></td></tr>";
                    }else{
                        $('.exportar-historico').attr('disabled',false);
                        for(var placa in retorno){
                            html += "<tr><td colspan='7' style='font-size:16px;!important line-height:40px;!important'><span class='badge'>"+placa+"</span></td></tr>";
                            data = retorno[placa];
                            for(var key in data){
                                html += "<tr><td colspan='7'><b>Data:"+key+"</b></td></tr>";
                                bilhetes = data[key];
                                for(var b in bilhetes){
                                    if(!isNaN(b)){
                                        //tratamento estilização linhas conforme tipo de evento
                                        var tr,tdEvento;
                                        if(bilhetes[b].evento == 'ID'){
                                            tr = "<tr class='danger'>";
                                            tdEvento = "<td><span style='color: red' class='glyphicon glyphicon-remove-sign'></span> Ignição Desligada</td>";
                                        }
                                        if(bilhetes[b].evento == 'IL'){
                                            tr = "<tr class='success'>";
                                            tdEvento = "<td><span style='color: green' class='glyphicon glyphicon-ok-sign'></span> Ignição Ligada</td>";
                                        }
                                        if(bilhetes[b].evento == 'TP'){
                                            tr = "<tr class='danger'>";
                                            tdEvento = "<td><span class='glyphicon glyphicon-stop'></span> Parado</td>";
                                        }
                                        if(bilhetes[b].evento == 'M'){
                                            tr = "<tr class='success'>";
                                            tdEvento = "<td><span class='glyphicon glyphicon-road'></span> Movimento</td>";
                                        }
                                        if(bilhetes[b].evento == 'P12'){
                                            tr = "<tr class='warning'>";
                                            tdEvento = "<td><span class='glyphicon glyphicon-clock'></span> Parado + 12Hrs</td>";
                                        }
                                        if(bilhetes[b].evento == 'PP'){
                                            tr = "<tr class='warning'>";
                                            tdEvento = "<td><span class='glyphicon glyphicon-map-marker'></span> Parado em Ponto</td>";
                                        }
                                        if(bilhetes[b].evento == 'P1'){
                                            tr = "<tr class='info'>";
                                            tdEvento = "<td><span style='margin-left: -22px' class='flaticon-icon029'></span> Acionamento Porta 1</td>";
                                        }
                                        if(bilhetes[b].evento == 'P2'){
                                            tr = "<tr class='info'>";
                                            tdEvento = "<td><span style='margin-left: -22px' class='flaticon-icon029'></span> Acionamento Porta 2</td>";
                                        }
                                        if(bilhetes[b].evento == 'P3'){
                                            tr = "<tr class='info'>";
                                            tdEvento = "<td><span style='margin-left: -22px' class='flaticon-icon029'></span> Acionamento Porta 3</td>";
                                        }
                                        if(bilhetes[b].evento == 'P4'){
                                            tr = "<tr class='info'>";
                                            tdEvento = "<td><span style='margin-left: -22px' class='flaticon-icon029'></span> Acionamento Porta 4</td>";
                                        }
                                        if(bilhetes[b].evento == 'FA'){
                                            tr = "<tr class='warning'>";
                                            tdEvento = "<td><span style='color: red' class='fa fa-lock'></span> Bloqueio Acionado</td>";
                                        }
                                        if(bilhetes[b].evento == 'EC'){
                                            tr = "<tr class='warning'>";
                                            tdEvento = "<td><span style='color: red' class='fa fa fa-flash' title='A energia externa que alimenta o módulo foi cortada'></span> Energia Cortada</td>";
                                        }
                                        if(bilhetes[b].evento == 'ER'){
                                            tr = "<tr class='warning'>";
                                            tdEvento = "<td><span style='color: green' class='fa fa fa-flash'></span> Energia Restaurada</td>";
                                        }
                                        html += tr
                                              +"<td>"+bilhetes[b].hora+"</td>"
                                              +"<td>"+bilhetes[b].tempo+"</td>"
                                              + tdEvento
                                              +"<td>"+bilhetes[b].endereco+"</td>"
                                              +"<td>"+bilhetes[b].cidade+"</td>"
                                              +"<td>"+bilhetes[b].ponto+"</td>"
                                              +"<td class='hidden-print'><a title='Clique para ver local no mapa' href='#' onClick='window.open(\"http://maps.google.com/maps?q=loc:"+bilhetes[b].latlon+"\",\"_blank\")'><span class='glyphicon glyphicon-screenshot'></span></a></td>"
                                              +"</tr>";
                                    }else{
                                        html +="<tr style='font-size: 15px'><td colspan='7'><b>Total eventos: </b>"
                                              +"<b>Parado + 12 horas: </b> <span class='badge'>" + bilhetes['contadores'].parado12 + "</span>"
                                              +"<b>  |  Parado em ponto:</b> <span class='badge'>" + bilhetes['contadores'].paradoPonto + "</span>"
                                              +"<b>  |  Movimento madrugada:</b> <span class='badge'>" + bilhetes['contadores'].madrugada + "</span>"
                                              +"<b>  |  Acionamento portas:</b> <span class='badge'>" + bilhetes['contadores'].porta + "</span>"
                                              +"<b>  |  Quilometragem Dia:</b> <span class='badge'>" + bilhetes['contadores'].km+"</span></td></tr>"
                                              +"<tr><td colspan='7'></td></tr>";
                                    }
                                }
                            }//for
                            $("#btnGerarHistoricoPosicoes").removeAttr('disabled');
                        }
                    }
                    $("#corpoTabelaHistoricoPosicoes").html(html);
        },//fim success
        error: function(){
            html = "<tr><td colspan='7'><span style='margin-top: 0px;' class='alert alert-danger h3'>Erro na requisição dos dados.</span></td></tr>";
        }
    });//fim ajax
});

$(".exportar-historico").click(function (){
    var html = $(".divImprimir").html();
    var tipo = $(this).attr('data-type');
    var txtBtn = $(this).html();
    var botao = $(this);
    $.ajax({
        url: ROOT+'/painel/relatorios/historico/posicoes/exportar',
        type:'post',
        data:{'tipo' : tipo,'titulo' : 'Histórico de Posições','html' : html,'arrayDados': JSON.stringify(dadosRelatorio)},
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
            $(botao).html("Erro :(");
        }
    });
});
