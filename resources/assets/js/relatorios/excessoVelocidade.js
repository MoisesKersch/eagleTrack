
//variavais
dadosRelatorio = [];
//funcoes para exportacao
$(".exportar-excesso").click(function(e){
    var html = $(".divImprimir").html();
    var tipo = $(this).attr('data-type');
    var txtBtn = $(this).html();
    var botao = $(this);
    $.ajax({
        url: ROOT+'/painel/relatorios/excesso/velocidade/exportar',
        type:'post',
        data:{'tipo' : tipo,'titulo' : 'Excesso Velocidade','html' : html,'arrayDados': JSON.stringify(dadosRelatorio)},
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
//fim exportacoes

$(".excesso-velocidade").click(function(){
   ExcessoVelocidade()
})

$(".excesso-clientes").change(function(){
    buscaVeiculosExcesso($(this));
    buscaGrupoMotoristaExcesso($(this).val());
})


$('.excesso-clientes, #excessoVelocidadeVeiculo').on('change', function(){
    if($('.excesso-clientes option:selected').length > 0
    && $('#excessoVelocidadeVeiculo option:selected').length > 0 ){
        $("#relatorioExcessoVelocidade").attr('disabled', false);
    }else{
        $("#relatorioExcessoVelocidade").attr('disabled', true);
        $(".exportar-excesso").attr('disabled', true);
        $(".excesso-exportar").attr('disabled', true);
        $("#excessoVelocidadeBody").html('<td colspan="8"><span style="margin-top: 0px;" class="alert alert-info">Para gerar o relatório, selecione a data e a placa ou o grupo de motorista desejado.</span></td>');
    }
    // if($('select-cliente'))
});

$("#excessoVelocidadeVeiculo").change(function(){
    var todos = $(this).val()
    var t = todos.indexOf("0")
    if(Array.isArray(todos) && t == 0) {
        $(this).children().attr('selected', true)
        $(this).select2({
		    "language": "pt-BR",
            allowClear: true
		})
    }

    changeBtnRelatorioExcesso();
});

$('#excessoVelocidadeGm').change(function() {
    changeBtnRelatorioExcesso();
});

function changeBtnRelatorioExcesso() {
    if ($('#excessoVelocidadeGm').val().length > 0 || $('#excessoVelocidadeVeiculo').val().length > 0) {
        $(".bt-relarorio-excesso").attr('disabled', false)
    } else {
        $(".bt-relarorio-excesso").attr('disabled', true)
        $("#excessoVelocidadeBody").html('<tr><td colspan="8"><span class="alert alert-info" style="margin-top: 0px;">Para gerar o relatório, selecione a data e a placa ou o grupo de motorista desejado.</span></td></tr>')
    }
}

function buscaVeiculosExcesso(thad) {
    var id = $(thad).val()
    var todos = $(thad).val()
    var t = todos.indexOf("0")
    if(Array.isArray(todos) && t == 0) {
        $(thad).children().attr('selected', true)
        $(thad).select2({
		    "language": "pt-BR"
		})
    }
    todosClientesExcesso()
}

function todosClientesExcesso() {
    id = $(".excesso-clientes").val()
    $.post(ROOT+'/painel/relatorios/excesso/velocidade/clientes',
        {
            id:id,
        },
        function(dados) {
            var placas = dados.placas
            opt = '<option value="0" class="todos-excesso-placa">Selecionar todos</option>';
            for(i in placas) {
                opt += '<option value="'+placas[i].vecodigo+'">'+placas[i].veplaca+' | '+placas[i].veprefixo+'</option>'
            }
            $("#excessoVelocidadeVeiculo").html(opt);
        }
    )
}

function buscaGrupoMotoristaExcesso(clientes) {
    $.ajax({
        url: ROOT+'/painel/relatorios/excesso/velocidade/grupoMotorista',
        type: 'post',
        data: { 'clientes': clientes },
        success: function(data) {
            var gm = data.grupoMotorista;
            if (gm)
                optgm = '<option value="0" class="todos-excesso-gm">Selecionar todos</option>';
            for(i in gm) {
                optgm += '<option value="'+gm[i].gmcodigo+'">'+gm[i].gmdescricao+'</option>'
            }
            $("#excessoVelocidadeGm").html(optgm);
        }
    });
}

function montaTabelaExcessoVelocidade(velos){
	var tr = '';

	if(velos.constructor === Object){
		for(i in velos) {
			if(jQuery.isEmptyObject(velos[i])){
				return tr;
			}

            console.log(velos[i]);

			tr += '<tr>'
				tr += '<td font-size: 30px class="badge placa-relatorio" colspan="8">'+i+' | '+velos[i][0].veprefixo+'</td>'
			tr += '</tr>';

			for(j in velos[i]){
				tr += '<tr>';
				tr += '<td>'+velos[i][j].adata+' '+velos[i][j].bhora+'</td>';
				tr += '<td>'+velos[i][j].cmtnome+'</td>';
				tr += '<td>'+velos[i][j].endereco+'</td>';
				tr += '<td>'+velos[i][j].evelmax+'</td>';
				tr += '<td>'+velos[i][j].fbivelocidade+'</td>';
				tr += '<td>'+velos[i][j].gexcedido+'</td>';
				tr += '<td>'+velos[i][j].porcentagem+'</td>';
				tr += '<td title="Visualizar no mapa" class="hidden-print"><span style="cursor:crosshair" class="glyphicon glyphicon-screenshot" onclick="window.open(&quot;http://maps.google.com/maps?q=loc:'
					+velos[i][j].lat+','
					+velos[i][j].long+'&quot;,&quot;_blank&quot;);"></span></td>';
				tr += '</tr>';
			}
		}
	}
	return tr;
}

function ExcessoVelocidade() {
    thad = $(".excesso-clientes")
    if((thad.val()).length == 0)
         return '';
    $('.form-excesso').ajaxForm({
        type:'post',
        beforeSubmit : function (){
            $("#relatorioExcessoVelocidadeBody").html('<tr><td class="load" colspan="7"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
        },
        success: function(dados) {
            var velos = dados.dados;
            dadosRelatorio = velos;
            var tr = '';

			tr = montaTabelaExcessoVelocidade(velos);
            $("#relatorioExcessoVelocidadeBody").html(tr)
            if(!jQuery.isEmptyObject(velos)) {
                $(".exportar-excesso").attr('disabled', false);
                $(".excesso-exportar").attr('disabled', false);
            }else{
                $(".exportar-excesso").attr('disabled', true);
                $(".excesso-exportar").attr('disabled', true);
                $("#relatorioExcessoVelocidadeBody").html('<tr><td class="load" colspan="7">Nada encontrado!!!</td></tr>')
            }
        },
		error: function(request, status, error) {
		   $("#relatorioExcessoVelocidadeBody").html('<tr><td class="load" colspan="7">Selecione um período ou quantidade de veículos menor!!!</td></tr>');
		},
    }).submit();
}

$(document).on('click', '.order-column', function(){
    sortTable($(this).attr('data-order'))
})

function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("relatorioExcessoVelocidade");
  switching = true;
  dir = "asc";
  while (switching) {
    switching = false;
    rows = table.getElementsByTagName("TR");
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[n];
        y = rows[i + 1].getElementsByTagName("TD")[n];
        if (dir == "asc") {
            if($.isNumeric(x.innerHTML.toLowerCase())) {
                if (parseInt(x.innerHTML.toLowerCase()) > parseInt(y.innerHTML.toLowerCase())) {
                    shouldSwitch= true;
                    break;
                }
            }else if(moment(x.innerHTML.toLowerCase(), 'DD/MM/YYYY').isValid()) {
                var data_1 = new moment(x.innerHTML.toLowerCase(), 'DD/MMYYYY');
                var data_2 = new moment(y.innerHTML.toLowerCase(), 'DD/MM/YYYY');
                if (data_1 > data_2)  {
                    shouldSwitch= true;
                    break;
                }
            }else {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch= true;
                    break;
                }
            }
      } else if (dir == "desc") {
            if($.isNumeric(x.innerHTML.toLowerCase())) {
                if (parseInt(x.innerHTML.toLowerCase()) < parseInt(y.innerHTML.toLowerCase())) {
                shouldSwitch= true;
                break;
            }
            }else if(moment(x.innerHTML.toLowerCase(), 'DD/MM/YYYY').isValid()) {
                var data_1 = new moment(x.innerHTML.toLowerCase(), 'DD/MM/YYYY');
                var data_2 = new moment(y.innerHTML.toLowerCase(), 'DD/MM/YYYY');
                if (data_1 < data_2)  {
                    shouldSwitch= true;
                    break;
                }
            }else {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                  shouldSwitch= true;
                  break;
                }
            }
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount ++;
    } else {
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
