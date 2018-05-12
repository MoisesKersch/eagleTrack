$(document).ready(function(){
    var itens = $('li.itens-menu-painel');

    itens.each(function(i){
        if($(itens[i]).find('li').length < 1){
            $(itens[i]).remove();
        }
    });

    $('#divPrevisaoTempo').trigger('click');
    $('#divDataHoraPainel').trigger('click');

    $('#parceiros').click(function(){
        $(this).popover();
    })
});

$('#divPrevisaoTempo').ready(function(){
    contregiao();
    setInterval(function(){
        contregiao();
    }, 60000)
})


    //executar somente se o link fori para painel
    // contregiao();
    // if($('li').hasClass('itens-menu-painel')) {
    //     setInterval(function(){
    //         atualizaContregiao();
    //     }, 35000)
    // }


function contregiao(){
    $.ajax({
        url: ROOT+'/painel/regiao/veiculos',
        success: function(retorno){
            var regioes=retorno.veiculosregioes
            var tr = ''
            var but = ''
            var cont = 0
            var contb = 0
            if(regioes != null){
                for(i in regioes) {
                var veiculos=regioes[i]
                but = "<div class='centralizar'> Veiculos Dentro da Região</div>"
                but += "<div class='centralizar' id='ez-"+regioes[i][0].recodigo+"'><strong>"+regioes[i][0].redescricao+"</strong></div>"
                    for(j in veiculos){
                        but += '<li>'+veiculos[j].veplaca+' | '+veiculos[j].veprefixo+'</li>'
                        cont++
                    }
                tr += '<tr>';
                tr += '<td id="iz-'+regioes[i][0].recodigo+'">'+regioes[i][0].redescricao+'</td>';
                tr += '<td class="centralizar"> <a data-html="true" class="btn btn-primary" data-toggle="popover" data-content="'+but+'" >'+cont+'</a> </td>';
                tr += '</tr>'
                cont = 0
                }
            }else{
                tr += '<tr>Não há regiões com veículos</tr>'
            }
            $("#tableVeicnaRegi tbody").html(tr)
            var pop = $('[data-toggle="popover"]').popover();
        }
    })
}

function atualizaContregiao(){
    $.ajax({
        url: ROOT+'/painel/regiao/veiculos',
        success: function(retorno){
            var regioes=retorno.veiculosregioes
            var i = 0;
            var j = 0;
            for(i in regioes){
                console.log(regioes[i][0].redescricao)
                $("#ez-"+regioes[i][0].recodigo+"").html(regioes[i][0].redescricao);
                $("#iz-"+regioes[i][0].recodigo+"").html(regioes[i][0].redescricao);
                var veiculos = regioes[i]
            }
            var pop = ''
            pop = "<div class='centralizar'> Veiculos Dentro da Região</div>"
            pop += "<div class='centralizar' id='ez-"+regioes[i][0].recodigo+"'><strong>"+regioes[i][0].redescricao+"</strong></div>"
            for (j in veiculos){
                pop += '<li>'+veiculos[j].veplaca+' | '+veiculos[j].veprefixo+'</li>'
            }

            $("#tableVeicnaRegi .popover-content").html(pop)
        }
    })
}


$('#divDataHoraPainel').click(function(){
    $('#divDataHoraPainel').html(pegaDataHoje());
});

$('#divPrevisaoTempo').click(function(){
    rankingKm();
    atualizarDados();
    $('#divPrevisaoTempo').unbind("click");
});

$('.alertas-painel').on('change',function(){
    buscaAlertasManutencao();
    buscaAlertasCnhVencida();

})

$(".alertas-painel").trigger('change');

function atualizarDados() {
    $.ajax({
        url: ROOT+'/painel/previsao/tempo',
        dataType: 'json',
        success: function(dados) {
                $("#cidadeTemperatura").html(dados.cidade);
                $("#temperaturaAgora").html(dados.temperatura);
        }
    });
}

function pegaDataHoje(){
    var data = new Date();
    var diaSemana = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sabado'];
    var mes = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    var hoje = diaSemana[data.getDay()]+", "+data.getDate()+" de "+mes[data.getMonth()]+".";
    return hoje;
}


function contregiao(){
    $.ajax({
        url: ROOT+'/painel/regiao/veiculos',
        success: function(retorno){
            var regioes=retorno.veiculosregioes
            var tr = ''
            var but = ''
            var cont = 0
            var contb = 0
            var i = 0
            for(i in regioes) {
                if(regioes[i] != null){
                    var veiculos=regioes[i]
                    but = "<div class='centralizar'> Veiculos Dentro da Região</div>"
                    but += "<div class='centralizar' id='ez-"+regioes[i][0].recodigo+"'><strong>"+regioes[i][0].redescricao+"</strong></div>"
                        for(j in veiculos){
                            but += '<li>'+veiculos[j].veplaca+' | '+veiculos[j].veprefixo+'</li>'
                            cont++
                        }
                    tr += '<tr>';
                    tr += '<td id="iz-'+regioes[i][0].recodigo+'">'+regioes[i][0].redescricao+'</td>';
                    tr += '<td class="centralizar"> <a data-html="true" data-trigger="hover" class="btn btn-primary" data-toggle="popover" data-content="'+but+'" >'+cont+'</a> </td>';
                    tr += '</tr>'
                    cont = 0
                
                }
            }
            if (i  == 0) {
                tr += '<tr><td>Não há regiões com veículos<td></tr>'
            }
            $("#tableVeicnaRegi tbody").html(tr)
            var pop = $('[data-toggle="popover"]').popover();
        }
    })

}
    

function rankingKm(){
    $.ajax({
        url: ROOT+'/painel/ranking/kms',
        success: function(retorno){
            $('.divCarregaGrafico').hide('slow');
            var dados = JSON.parse(retorno);
            var tamanhoArr = dados.length;
            tamanhoArr = tamanhoArr - 1;
            if(tamanhoArr <= 0){
                $('#divGraficoQuilometragem').html('<div style="margin-top: 0px;" class="alert alert-warning">Nenhuma informação do dia de hoje :(</div>');
            }else{
                var parametros = '[';
                for(var x in dados){
                    if(x == tamanhoArr){
                        parametros += '{ "Placa": "'+dados[x].biplaca+'", "total": '+dados[x].total+'}';
                    }else{
                        parametros += '{ "Placa": "'+dados[x].biplaca+'", "total": '+dados[x].total+'},';
                    }
                }
                parametros += ']';
                parametros = JSON.parse(parametros);
                Morris.Bar({
                    element: 'divGraficoQuilometragem',
                    data: parametros,
                    xkey: 'Placa',
                    ykeys: ['total'],
                    labels: ['Kms']
                });
            }
        }
    });
}

var dataSet = [];

function buscaAlertasManutencao(){
    $.ajax({
        url: ROOT+'/painel/alertas/manutencao',
        success: function(data){
            console.log(data);
            for (var manutencao in data.manutencoes) {
                var local = [];
                if(data.manutencoes[manutencao].biplaca == undefined){
                    data.manutencoes[manutencao].biplaca = "";
                }
                local.push(data.manutencoes[manutencao].biplaca+' | '+data.manutencoes[manutencao].veprefixo);

                if(data.manutencoes[manutencao].timdescricao == undefined){
                    data.manutencoes[manutencao].timdescricao = "";
                }
                local.push(data.manutencoes[manutencao].timdescricao);

                if(data.manutencoes[manutencao].mapstatus == undefined){
                    data.manutencoes[manutencao].mapstatus = "";
                }else{
                    if(data.manutencoes[manutencao].mapstatus == 'P' ) {
                        data.manutencoes[manutencao].mapstatus = 'Pendente'
                    }else if(data.manutencoes[manutencao].mapstatus == 'R') {
                        data.manutencoes[manutencao].mapstatus = 'Realizada'
                    }
                }
                local.push(data.manutencoes[manutencao].mapstatus);

                if(data.manutencoes[manutencao].km_ate_manutencao == undefined){
                    data.manutencoes[manutencao].km_ate_manutencao = "";
                }
                local.push(data.manutencoes[manutencao].km_ate_manutencao);

                dataSet.push(local);
            }

            $('#tablePainelAlertaManutencao').DataTable().destroy();

            table =  $('#tablePainelAlertaManutencao').DataTable({
                paging: false,
                retrieve: false,
                searching: false,
                language: traducao,
                data: dataSet,
                ordering: false,
                columnDefs: [{
                    "targets":  3,
                    "visible": false,
                    "searchable": false
                }],
                fnCreatedRow: function( nRow, aData, iDataIndex ) {
                    if(aData[3] < 500 && aData[3] >= 0){
                        $('td', nRow).addClass('warning');
                    }else if(aData[3] < 0){
                        $('td', nRow).addClass('danger');
                    }
                },
            });
            dataSet = [];
        }
    });
}


function buscaAlertasCnhVencida(){
    $.ajax({
        url: ROOT+'/painel/alertas/cnhvencida',
        success: function(data){
            var v = data.motoristas;

            for (var d in v) {
                // var StartDate = moment(new Date(v[d].mtcnhvalidade)).format('DD/MM/YYYY');
                var local = [];
                if(v[d].mtnome == undefined){
                    v[d].mtnome = "";
                }
                local.push(v[d].mtnome);

                local.push(moment(new Date(v[d].mtcnhvalidade)).format('DD/MM/YYYY'));

                dataSet.push(local);
            }

            $('#tablePainelCnhVencida').DataTable().destroy();

            table =  $('#tablePainelCnhVencida').DataTable({
                paging: false,
                retrieve: false,
                searching: false,
                ordering: false,
                language: traducao,
                data: dataSet,
                fnCreatedRow: function( nRow, aData, iDataIndex ) {
                    var data = moment(aData[1],'DD/MM/YYYY');
                    var baseDate = moment(aData[1],'DD/MM/YYYY').subtract(61, 'days');
                    var nowDate = moment(new Date(),'DD/MM/YYYY');

                    if(data.isBefore(nowDate) || data.isSame(nowDate)){
                        $('td', nRow).addClass('danger');
                    }else if(data.isAfter(nowDate) && nowDate.isAfter(baseDate)){
                        $('td', nRow).removeClass('danger');
                        $('td', nRow).addClass('warning');
                    }else{
                        $('td', nRow).removeClass('danger');
                        $('td', nRow).removeClass('warning');
                    }
                },
            });
            dataSet = [];
        }
    });
}

// Start of Tawk.to Script
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/59f0c2fdc28eca75e46282c0/default';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
})();
// End of Tawk.to Script
