function somartempos(tempo1, tempo2) {

    var array1 = tempo1.split(':');
    var tempo_seg1 = (parseInt(array1[0]) * 3600) + (parseInt(array1[1]) * 60) + parseInt(array1[2]);
    var array2 = tempo2.split(':');
    var tempo_seg2 = (parseInt(array2[0]) * 3600) + (parseInt(array2[1]) * 60) + parseInt(array2[2]);
    var tempofinal = parseInt(tempo_seg1) + parseInt(tempo_seg2);
    var hours = Math.floor(tempofinal / (60 * 60));
    var divisorMinutos = tempofinal % (60 * 60);
    var minutes = Math.floor(divisorMinutos / 60);
    var divisorSeconds = divisorMinutos % 60;
    var seconds = Math.ceil(divisorSeconds);
    var contador = "";
    if (hours < 10) { contador = "0" + hours + ":"; } else { contador = hours + ":"; }
    if (minutes < 10) { contador += "0" + minutes + ":"; } else { contador += minutes + ":"; }
    if (seconds < 10) { contador += "0" + seconds; } else { contador += seconds; }


    return contador;
}


$('#clientesControleHorarios, #veiculosHorarioControle, #motoristasHorarioControle').on('change', function(){
    if($('#clientesControleHorarios option:selected').length > 0
    || $('#veiculosHorarioControle option:selected').length > 0
    || $('#motoristasHorarioControle option:selected').length > 0 ){
        $("#btnGerarControleHorario").attr('disabled', false);
    }else{
        $("#btnGerarControleHorario").attr('disabled', true);
        $('.exportar-cont-horario').attr('disabled',true);
        $("#tableControleHorario tbody").html('<td colspan="7"><span style="margin-top: 0px;" class="alert alert-info">Para gerar o relatório, selecione a data a placa e a região desejada.</span></td>');
    }
});

$("#btnGerarControleHorario").on('click',function(){
    $("#tableControleHorario tbody").html('<tr><td class="load" colspan="7"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
    controleHoras();
})

$(document).ready(function(){
    $("#clientesControleHorarios").trigger('change')
})

function controleHoras() {
    $("#tableControleHorario tbody").html('<tr><td class="load" colspan="5"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
    $(".form-controle-horarios").ajaxForm({
        type:'post',
        success: function(dados) {
            var placa = dados.placa;
            var tr = ''
            var data = ''
            dadosRelatorio = [];
            linhas = [];
            colunas = [];

            try {
                for(i in placa) {
                    data = placa[i];
                    console.log("bahh",data);
                    var count = 0;

                    tr += '<tr>'
        				tr += '<td font-size: 30px class="badge placa-relatorio" colspan="8">'+i+' | '+placa[i].prefixo+'   '+placa[i].motorista+'</td>'
        			tr += '</tr>';

                    for(j in data){
                        somaTempo = "00:00:00";
                        var dados = data[j].ini
                        var cont = 0;

                        for(k in dados) {
                            cont++
                            count++;
                            ponto = ''
                            codigo = ''

                            var dd = dados[k].substr(0, 5)
                            if(dd != old && dados[k] != "") {
                                ponto = '<strong>Início jornada</strong>';
                                codigo = 1;
                            }
                            if(dd == old && cont == Object.getOwnPropertyNames(dados).length || dd == ""){
                                dados[k] = '';
                                ponto = '<strong>Fim jornada</strong>';
                                codigo = 5
                                if(data[j].tempo[k].toString() != '' && somaTempo != ''){
                                    somaTempo = somartempos(somaTempo, data[j].tempo[k].toString());
                                }
                            }else if ((dd == old) && data[j].ponto[k].tipo == 'P') {
                                ponto = 'Refeição';
                                if(data[j].tempo[k].toString() != '' && somaTempo != ''){
                                    somaTempo = somartempos(somaTempo, data[j].tempo[k].toString());
                                }
                                codigo = 3
                            }else if ((dd == old) &&  data[j].ponto[k].tipo != 'P' && data[j].ponto[k].tipo != ''){
                                ponto = 'Espera';
                                if(data[j].tempo[k].toString() != '' && somaTempo != ''){
                                    somaTempo = somartempos(somaTempo, data[j].tempo[k].toString());
                                }
                                codigo = 4;
                            }else if ((dd == old) && data[j].ponto[k].tipo == '') {
                                ponto = 'Intervalo';
                                if(data[j].tempo[k].toString() != '' && somaTempo != ''){
                                    somaTempo = somartempos(somaTempo, data[j].tempo[k].toString());
                                }
                                codigo = 2
                            }

                            if(count > 1 && codigo == 1){
                                tr += '<tr> <td height="26px" colspan="6"></td></tr>';
                            }

                            tr += '<tr>'
                                tr += '<td>'+dados[k]+'</td>'
                                tr += '<td>'+data[j].fi[k]+'</td>'
                                if(cont == Object.getOwnPropertyNames(dados).length){
                                    tr += '<td>'+somaTempo+'</td>'
                                }else{
                                    tr += '<td>'+data[j].tempo[k]+'</td>'
                                }
                                tr += '<td>'+codigo+'</td>'
                                tr += '<td>'+ponto+'</td>'
                                tr += '<td>'+data[j].ponto[k].nome+'</td>'
                            tr += '</tr>'

                            //Organiza dados para exportação
                            colunas.push(i +' '+ placa[i].motorista)
                            colunas.push(dados[k]);
                            colunas.push(data[j].fi[k]);
                            colunas.push(data[j].tempo[k]);
                            colunas.push(codigo);
                            colunas.push(ponto);
                            colunas.push(data[j].ponto[k].nome);

                            linhas.push(colunas);
                            colunas = [];

                            var old = dd
                        }
                    }
                    dadosRelatorio.push(linhas);
                    linhas = [];
                }
                $("#tableControleHorario tbody").html(tr)
                $('.exportar-cont-horario').attr('disabled',false);
            }catch(err) {
                $(".exportar-cont-horario").attr('disabled', true);
                $("#tableControleHorario tbody").html('<tr><td class="load" colspan="5">Nada encontrado!!!</td></tr>')
            }
        },
        error: function(request, status, error) {
		   $("#tableControleHorario tbody").html('<tr><td class="load" colspan="5">Selecione um período ou quantidade de veículos menor!!!</td></tr>');
		},
    }).submit()
}

$("#clientesControleHorarios").change(function(){
    var clientes = $(this).val();
    $.post(ROOT+'/painel/relatorios/controle/horario/dados_filtros',
        {
            clientes: clientes
        },
    function(dados){

        var d = dados
        var dados = d.motoristas
        if(dados.length > 0){
            var opt = '<option value="0">Selecionar Todos</option>';
            for(i in dados){
                opt += '<option value="'+dados[i].mtcodigo+'">'+dados[i].mtnome+'</option>'
            }
            $("#motoristasHorarioControle").html(opt);
        }

        var dados = d.veiculos
        if(dados.length > 0){
            var opt = '<option value="0">Selecionar Todos</option>';
            for(i in dados) {
                opt += '<option value="'+dados[i].vecodigo+'">'+dados[i].veplaca+' | '+dados[i].veprefixo+'</option>'
            }
            $("#veiculosHorarioControle").html(opt);
        }
    })
})

$(".exportar-cont-horario").click(function (){
    var html = $(".divImprimir").html();
    var tipo = $(this).attr('data-type');
    var txtBtn = $(this).html();
    var botao = $(this);


    $.ajax({
        url: ROOT+'/painel/relatorios/controle/horario/exportar',
        type:'post',
        data:{'tipo' : tipo,'titulo' : 'Controle de Horários','html' : html,'arrayDados': JSON.stringify(dadosRelatorio)},
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
