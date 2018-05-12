$(document).ready(function (){
    $("#selectClientesComunicacao").change(function(){
        var empresas = $(this).val();
        $("#selectVeiculosComunicacao").html("");
        $.ajax({
            type:'post',
            url:ROOT+'/painel/relatorios/comunicacao/carrega/veiculos',
            data: {'empresas' : empresas},
            success: function(retorno){

                retorno = retorno[0];
                var option;
                option += "<option value=\"\" >Selecionar Todos</option>";
                for(var x in retorno){
                    option += "<option value='"+retorno[x].vecodigo+"' >"+retorno[x].veplaca+" | "+retorno[x].veprefixo+"</option>";
                }
                $("#selectVeiculosComunicacao").html(option);
                if(retorno.length > 0) $("#selectVeiculosComunicacao").attr("disabled",false);
            }
        })

       
    })
    $("#selectClientesComunicacao").trigger('change');
})
 
//***********************************************************************************************

$('#selectClientesComunicacao, #selectVeiculosComunicacao').on('change', function(){
    if($('#selectClientesComunicacao option:selected').length > 0
    || $('#selectVeiculosComunicacao option:selected').length > 0){
        $("#btnGerarComunicacao").attr('disabled', false);
        
    }else{
        $("#btnGerarComunicacao").attr('disabled', true);
        $('.exportar-comunicacao').attr('disabled',true);
        $("#tableControleComunicacao tbody").html('<td colspan="7"><span style="margin-top: 0px;" class="alert alert-info">Para gerar o relatório, selecione a data a placa e a região desejada.</span></td>');
    }
});

$('#btnGerarComunicacao').on('change', function(){
    if($('#btnGerarComunicacao option:selected').length > 0){
        $("#btnExportar").attr('disabled', false);
        $("#btnImprimir").attr('disabled', false);
    }else{
        $("#btnExportar").attr('disabled', true);
        $("#btnImprimir").attr('disabled', true);
        $('.exportar-comunicacao').attr('disabled',true);
        }
});


$("#btnGerarComunicacao").on('click',function(){
    $("#tableControleComunicacao tbody").html('<tr><td class="load" colspan="7"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
    
    $.ajax({
        type:'post',
        url:ROOT+'/painel/relatorios/comunicacao/gerar',
        data:{'tempo':$('.comunicacao-tempo').val(), 'veiculos':$('#selectVeiculosComunicacao').val()},
        success: function(retorno){
            $("#btnGerarComunicacao").removeAttr('disabled');
            $("#btnExportar").removeAttr('disabled');
            $("#btnImprimir").removeAttr('disabled');
            $("#load").find("tr").remove();
            dadosRelatorio = retorno;
            retorno = retorno[0];
            if(dadosRelatorio == false){
                html = "<tr><td colspan='7'><span style='margin-top: 0px;' class='alert alert-danger h3'>Nenhuma informação encontrada.</span></td></tr>";
            }else{
            var html = '';
            var i = 0;
            for( i in retorno){
                var tr = '';
                tr += '<tr>'
                tr += '<td class="badge " colspan="3">'+retorno[i].veplaca+ ' - ' +retorno[i].clfantasia+'</td>'
                tr += '</tr>';

                var data = moment.utc(retorno[i].moultimoevento).format('DD/MM/YYYY HH:mm');

                tr += '<tr>';
                tr += '<td>'+data+'</td>';
                tr += '<td>'+retorno[i].moultimoendereco+'</td>';
                tr += '<td>'+retorno[i].mocodigo+'</td>';
                tr += '<td>'+retorno[i].mmdescricao+'</td>';
                tr += '</tr>';
            html += tr;                      
            }    
        }
            $("#corpoTabelaComunicacao").html(html);
        }
    });
})
$(document).ready(function(){
    $("#clientesComunicacao").trigger('change')
})


$(".exportar-comunicacao").click(function (){
    var html = $(".divImprimir").html();
    var tipo = $(this).attr('data-type');
    var txtBtn = $(this).html();
    var botao = $(this);


    $.ajax({
        url: ROOT+'/painel/relatorios/comunicacao/exportar',
        type:'post',
        data:{'tipo' : tipo,'titulo' : 'Comunicação','html' : html,'arrayDados': JSON.stringify(dadosRelatorio)},
        beforeSend: function(){
                $(botao).html('<span class="fa fa-spinner fa-spin fa-3x fa-fw"></span>')
                $("#tableControleComunicacao tbody").html('<tr><td class="load" colspan="7"><span <span class="fa fa-spinner fa-spin fa-3x fa-fw"></span></span>Gerando</td></tr>')
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