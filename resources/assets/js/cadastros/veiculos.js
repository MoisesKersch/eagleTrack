
$(".data-hora-inicio-veiculo").timepicker({
	minuteStep: 5,
	showMeridian: false,
	defaultTime: '08:00'
});

$(".data-hora-fim-veiculo").timepicker({
	minuteStep: 5,
	showMeridian: false,
	defaultTime: '18:00'
});

//onchange proprietários, carregar
$('#veicProprietario').on('change', function(){
	var ids_regioes = $('#veRegioes').data('regioes')+"";
	if(ids_regioes.indexOf(',') > 0){
		ids_regioes = $('#veRegioes').data('regioes').split(",");
	}

	$.ajax({
		type: "POST",
		url: ROOT+'/painel/cadastros/veiculos/regioes_cliente',
		data: {cliente:$('#veicProprietario').val()},
		dataType: "json",
		'success': function (data) {
			var regioes = data.regioes;
			var opt = ''
	        for(i in regioes) {
				if(ids_regioes.includes(""+regioes[i].recodigo)){
	            	opt += '<option selected value="'+regioes[i].recodigo+'">'+regioes[i].redescricao+'</option>'
				}else{
					opt += '<option value="'+regioes[i].recodigo+'">'+regioes[i].redescricao+'</option>'
				}
	        }
	        $("#veRegioes").html(opt);
		}
	});
})

$(document).ready(function() {
	$("#veicProprietario").trigger('change');

    var dataSet = [];
    var table;

    function ajaxAtualizaTabelaVeiculos(){
        $.ajax({
            type: "POST",
            url: ROOT+'/painel/cadastros/veiculos/buscar',
            data: {clientesbusca:$('.select-cliente-veiculo').val(),
    			   modulo:$('#flg_modulo_ve').val(),
    			   status:$('#flg_status_ve').val()},
            dataType: "json",
            'success': function (data) {
                var veiculo = data.veiculos;

				var ppeditar = $("#ppeditar").data('permissao');
				var ppexcluir = $("#ppexcluir").data('permissao');

                for(i in veiculo){
                    var local = [];
                    (veiculo[i].veplaca == undefined)? veiculo[i].veplaca = "" : '' ;
                    local.push(veiculo[i].veplaca);

                    (veiculo[i].veprefixo == undefined)? veiculo[i].veprefixo = "" : '' ;
                    local.push(veiculo[i].veprefixo);

                    (veiculo[i].mocodigo == undefined)? veiculo[i].mocodigo = "" : "" ;
                    local.push(veiculo[i].mocodigo);

                    (veiculo[i].vedescricao == undefined)? veiculo[i].vedescricao = "" : '' ;
                    local.push(veiculo[i].vedescricao);

                    (veiculo[i].veproprietario == undefined)? veiculo[i].veproprietario = "" : '' ;
                    local.push(veiculo[i].veproprietario);

                    var td = "";
                    var vecodigo = veiculo[i].vecodigo;
					if(ppexcluir){
	                    if(veiculo[i].vestatus == 'A'){
	                       td = td+"<td>"
	                           +"<a id='"+vecodigo+"' title='Alterar Status' class='btstatus_ve btn-tb btn btn-success' >"
	                           +"<span class='fa fa-check'></span></a>";
	                    }else{
	                       td = td+"<td>"
	                           +"<a id='"+vecodigo+"' title='Alterar Status' class='btstatus_ve btn-tb btn btn-danger'>"
	                           +"<span class='fa fa-ban'></span></a>";
	                    }
					}
					if(ppeditar){
	                    td = td+"<a title='Editar Motorista/Ajudante' class='btn-tb btn btn-info' href='"+ROOT+"/painel/cadastros/veiculos/editar/"+vecodigo+"' >"
	                       +"<span class='fa fa-pencil'></span></a></td>";
				   }
                local.push(td);

                dataSet.push(local);
                }


                $('#tb_lista_veiculos').DataTable().destroy();

                table =  $('#tb_lista_veiculos').DataTable({
                    paging: false,
                    retrieve: true,
                    language: traducao,
                    dom: 'Bfrtip',
                    data: dataSet,
                    buttons:
                        [{
                           extend: 'pdf',
                           className: 'btn btn-default exportar',
                           exportOptions: { columns: [0,1,2,3] },
						   customize: function (doc) {
                               doc.defaultStyle.alignment = 'center';
                               doc.styles.tableHeader.alignment = 'center';
                               doc.content[1].table.widths =
                               Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                          }
                        },{
                           extend: 'excel',
                           footer: false,
                           className: 'btn btn-default exportar',
                           exportOptions: { columns: [0,1,2,3] }
                       },{
                           extend: 'csv',
                           footer: false,
                           className: 'btn btn-default exportar',
                           exportOptions: { columns: [0,1,2,3] }
                       }],
                    initComplete: function () {
                        // $('.dt-buttons').prepend('<span class="label-botoes-table">Exportar para: </span>');
                        // $('.exportar').removeClass("dt-button buttons-pdf buttons-csv buttons-excel buttons-html5");
						$('.exportar').removeClass("dt-button buttons-pdf buttons-csv buttons-excel buttons-html5");
						$('.exportar').parent().addClass('cabecalho-exportacoes');
						$('.exportar').prepend("<span class='fa fa-save'></span>");
                    }
                });

                dataSet = null;
                dataSet = [];

            }
        });
    }



    $(".filtro_mo_ve").on("click",function(){
        if($(this).attr('id') == "ve_mo_sim"){
            $("#flg_modulo_ve").val("sim");
            $(".filtro_mo_ve").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else if($(this).attr('id') == "ve_mo_nao"){
            $("#flg_modulo_ve").val("nao");
            $(".filtro_mo_ve").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else{
            $("#flg_modulo_ve").val("todos");
            $(".filtro_mo_ve").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }
        ajaxAtualizaTabelaVeiculos();
    });

    $(".status_ve").on("click",function(){
        if($(this).attr('id') == "ativos_ve"){
            $("#flg_status_ve").val("ativo");
            $(".status_ve").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else if($(this).attr('id') == "inativos_ve"){
            $("#flg_status_ve").val("inativo");
            $(".status_ve").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else{
            $("#flg_status_ve").val("todos");
            $(".status_ve").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }
        ajaxAtualizaTabelaVeiculos();
    });

    $(document).on('click','.btstatus_ve', function(e){
        var thad = $(this);
        $.ajax({
            type: 'POST',
            url: ROOT+'/painel/cadastros/veiculos/status',
            data: {'vecodigo': $(this).attr('id')},
            success: function(response){
                if(response == 'A'){
                    $(thad).removeClass('btn-danger');
                    $(thad).children('span').removeClass('fa-ban');
                    $(thad).addClass('btn-success');
                    $(thad).children('span').addClass('fa-check');
                }else{
                    $(thad).removeClass('btn-success');
                    $(thad).children('span').removeClass('fa-check');
                    $(thad).addClass('btn-danger');
                    $(thad).children('span').addClass('fa-ban');
                }
                ajaxAtualizaTabelaVeiculos();
            }
        });
    });

    $("#ativos_ve").on("click", function () {
            ajaxAtualizaTabelaVeiculos();
    });

    $(document).ready(function(){
        $('#ativos_ve').trigger('click');
    });


	$(".select-cliente-veiculo").on("change", function () {
			ajaxAtualizaTabelaVeiculos();
	});

	//verificar se módulo já esta vinculado a um veículo;
	$(".select-veiculo-modulo").on('change', function(){
		var thad = $(this);
		if($(this).val() != -1){ //se for diferente de sem módulo
	        $.ajax({
	            type: 'POST',
	            url: ROOT+'/painel/cadastros/veiculos/modulo_usado',
	            data: {
						'mocodigo': $(this).val()
					},
	            success: function(response){
					var veiculo = response.veiculo;
					var modulo = response.modulo;
					if(veiculo != 0){

						$('#modalAlerta').modal('show');
						bts = '';
						bts += '<button type="button" id="cancel_modulo" class="btn btn-default" data-dismiss="modal">Cancelar</button>';
						bts += '<button type="button" id="desassociar_modulo" value="'+veiculo.vecodigo+'" class="btn btn-primary ">Desassociar</button>';

						$("#modalAlerta .modal-footer").html(bts);

						texto = 'O veículo ('+veiculo.veplaca+' - '+veiculo.veproprietario+') está vinculado a este módulo! Deseja desassociá-lo?';
						$("#modalAlerta .modal-body").html(texto);

						$(document).on('click','#cancel_modulo',function(){
							$('.select-veiculo-modulo').val('-1').trigger('change');
						});
                        // $(document).on('click','#modalAlerta',function(){
                        //     if(!$(this).hasClass('btn-primary')){
                        //     }
                        // });

                        $(function() {
                            var div = $("#desassociar_modulo"); // seleciona a div específica
                            $("#modalAlerta").on("click", function (e) {
                                if (div.has(e.target).length || e.target == div[0]){
                                    return;
                                }else{
                                    $('.select-veiculo-modulo').val('-1').trigger('change');
                                }
                            });
                        })

						$(document).on('click','#desassociar_modulo',function(e){
							$.ajax({
								type: 'POST',
								url: ROOT+'/painel/cadastros/veiculos/desvincular_modulo_usado',
								data: {
									'vecodigo': $(this).val()
								},success: function(response){
									//pegar os valores do novo modulo
									$('#hodometroAtual, #origHodometroAtual').val(modulo.mohodometro != null? parseInt(modulo.mohodometro)/1000 : 0 );
									$('#horimetroAtual, #origHorimetroAtual').val(modulo.mohorimetro != null? modulo.mohorimetro : 0 );
									$('#horimetroAtual, #hodometroAtual').attr('disabled', false);

								}
							});
							$('#modalAlerta').modal('hide');
						})
					}else{
						$('#hodometroAtual, #origHodometroAtual').val(modulo.mohodometro != null? parseInt(modulo.mohodometro)/1000 : 0 );
						$('#horimetroAtual, #origHorimetroAtual').val(modulo.mohorimetro != null? modulo.mohorimetro : 0 );
						$('#horimetroAtual, #hodometroAtual').attr('disabled', false);
					}
	            }
	        });
		}else{
			$('#hodometroAtual, #origHodometroAtual').val('0');
			$('#horimetroAtual, #origHorimetroAtual').val('0');
			$('#horimetroAtual, #hodometroAtual').attr('disabled', true);
		}
	});

	$('#salvarCliente').click(function(){
		$('#formCadastroVeiculo').submit();
	})

	$('#hodometroAtual').blur(function(){
		if($('#hodometroAtual').val() != $('#origHodometroAtual').val()){
			modalHodometroHorimetro("hodômetro", 'bt-nao-hodometro')
		}
	})

	$('#horimetroAtual').blur(function(){
		if($('#horimetroAtual').val() != $('#origHorimetroAtual').val()){
			modalHodometroHorimetro("horímetro", 'bt-nao-horimetro')
		}
	})

	$(document).on('click','.bt-nao-horimetro',function(){
		$('#horimetroAtual').val($('#origHorimetroAtual').val())
	})

	$(document).on('click','.bt-nao-hodometro',function(){
		$('#hodometroAtual').val($('#origHodometroAtual').val())
	})

});

function modalHodometroHorimetro(texto, classe){

	$("#modalClean .modal-body").html(`<p>O valor do `+texto+` do rastreador será alterado! Deseja continuar?</p>`);
	$("#modalClean .modal-footer").html(`<button type="button" class="btn btn-default" data-dismiss="modal">Sim</button>
										<button type="button" class="btn btn-default `+classe+` " data-dismiss="modal">Não</button>`)

	$("#modalClean").modal("show");
}
