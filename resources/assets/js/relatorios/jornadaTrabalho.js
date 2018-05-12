var dataSet = [];

$(".select-cliente-jornada").change(function(){
	var id = $(this).val();
	var todos = id.indexOf('00') >= 0
    if(todos) {
        $('.select-cliente-jornada').children('option').attr('selected', true)
        $(".select-cliente-jornada").select2({
		    "language": "pt-BR",
            allowClear: true
        })
		id = $(this).val();
    }
	$.post(ROOT+'/painel/relatorios/jornada/trabalho/clientes',{id:id},function(dados){
		var mot = dados.motoristas
        var opt = '<option value="00">Selecionar todos</option>'
        for(i in mot) {
		        opt += '<option value="'+mot[i].mtcodigo+'">'+mot[i].mtnome+'</option>'
		}
        $('.jornada-motorista').html(opt)
	})
})

$(".jornada-motorista").change(function(){
    var id = $(this).val()
    var todos = id.indexOf('00') >= 0
    if(todos) {
        $('.jornada-motorista').children('option').attr('selected', true)
        $(".jornada-motorista").select2({
            "language": "pt-BR",
            allowClear: true
        })
    }
    $("#gerarRelatorioJornadaTrabalho").attr('disabled', false);
})

$("#gerarRelatorioJornadaTrabalho").click(function(dados){
    jornadaTrabalho()
})

function jornadaTrabalho(thad) {
    var todos = $(thad).val()
    var form = $(".form-jornada");
    $(form).ajaxForm({
        type:'post',
        success: function(dados) {
            var jornada = dados.jornada
            user = dados.usuario
            var tr = ''
            var opt = '';
            var motoristas = dados.motoristas
                if($(thad).hasClass('select-cliente-jornada')){
                    for(j in motoristas) {
                        opt += '<option '+selected+' value="'+motoristas[j].mtcodigo+'">'+motoristas[j].mtnome+'</option>'
                    }
                $(".jornada-motorista").html(opt)
                }
			dataSet = [];
            for(i in jornada) {
                console.log(jornada)
                var inicio = moment(jornada[i].afedataentrada, 'DD/MM/YYYY H:m:s').format('DD/MM/YYYY HH:mm')
                var moto = jornada[i].bmtnome
                var semana = moment(jornada[i].afedataentrada, 'DD/MM/YYYY H:m:s').format('dddd')
                var porCento = '00:00:00'
                if(jornada[i].fedsr == 1) {
                    porCento = jornada[i].trabalhadas
                }
                var dados = [];
                dados.push(moment(inicio, 'DD/MM/YYYY H:m').format('DD/MM/YYYY'));
                dados.push(moto);
                dados.push(semana)
                dados.push(jornada[i].trabalhadas);
                dados.push(jornada[i].efehorafalta);
                dados.push();
                dados.push(jornada[i].ffehoraextra);
                dados.push(porCento)
                dados.push(jornada[i].hfehoranoturna);
                dados.push(jornada[i].ifeextranoturno);
                dados.push(jornada[i].jfehoraespera);
                dados.push (jornada[i].kfeintervalo);
                dados.push(jornada[i].totalhoras);
                dataSet.push(dados)
            }
            if ($.fn.DataTable.isDataTable('#relatorioJornadaTable')) {
                $('#relatorioJornadaTable').DataTable().destroy();
            }
            ///////////////////////
            if(!jQuery.isEmptyObject(jornada)) {
                $(".exportar-jornada").attr('disabled', false);
                html = " ";
				var old = '';
                for(x in dataSet){
					if(dataSet[x][1] != old){
						html += `<tr>
									<td class="badge" id="NomeMotor" colspan="11">`+dataSet[x][1]+`</td>
								</tr>`;
					}
                    html += "<tr id='layoutreltr'>";
	                    html += "<td>" + dataSet[x][0] + "</td>";
	                    html += "<td>" + dataSet[x][2] + "</td>";
	                    html += "<td>" + dataSet[x][3] + "</td>";
	                    html += "<td>" + dataSet[x][4] + "</td>";
	                    html += "<td>" + dataSet[x][5] + "</td>";
	                    html += "<td>" + dataSet[x][6] + "</td>";
	                    html += "<td>" + dataSet[x][7] + "</td>";
	                    html += "<td>" + dataSet[x][8] + "</td>";
	                    html += "<td>" + dataSet[x][9] + "</td>";
	                    html += "<td>" + dataSet[x][10] + "</td>";
	                    html += "<td>" + dataSet[x][11] + "</td>";
                    html += "</tr>";
					old = dataSet[x][1];
                }
                $("#relatorioJornadaTable tbody").html(html)
            }else{
                $(".exportar-jornada").attr('disabled', true);
                $(".exportar-jornada").attr('disabled', true);
                $("#relatorioJornadaTable tbody").html('<tr><td class="load" colspan="5">Nada encontrado!!!</td></tr>')
            }
            ///////////////////////
            if(!jQuery.isEmptyObject(jornada)) {
                // dadosJornadaTrabalhoExport('#relatorioJornadaTable', 11)
                $(".bt_gerar_relatorio").attr('disabled', false);
            }else{
                $("#relatorioJornadaTable tbody").html('<tr><td class="load" colspan="11">Nada encontrado!!!</td></tr>')
            }
        }
    }).submit();
}

$( document ).ready(function() {
    $(".select-cliente-jornada").trigger('change');
});

$(".exportar-jornada").click(function (){
    var html = $(".divImprimir").html();
    var tipo = $(this).data('type');
    var txtBtn = $(this).html();
    var botao = $(this);
    $.ajax({
        url: ROOT+'/painel/relatorios/jornada/trabalho/exportar',
        type:'post',
        data:{'tipo' : tipo, 'titulo' : 'Jornada de Trabalho','html' : html,'arrayDados': JSON.stringify(dataSet)},
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
