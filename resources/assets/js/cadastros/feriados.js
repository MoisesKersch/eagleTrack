$(document).ready(function(){
	var listagem = $("#listagemFeriados");
	$(listagem).each(function(idx, element){
		var elem = $(element);
        var buscaCli = elem.find("#buscaCliente");
        var filtros = elem.find(".filtros_feriados");

        $(filtros).click(function(){
        	var tipo = $(this).data('tipo');
        	var id = $(buscaCli).val();
        	buscarDados(id, tipo)
        })

        buscaCli.change(function(){
    	    var id = $(this).val();
    	    var tipo = $('.filtros_feriados.btn-primary').val();
    	    buscarDados(id, tipo)
		})

        function buscarDados(id, tipo){
	        var table = elem.find("#feriadosTable");
    	    var dataSet = [];

    	    $.post(ROOT+'/painel/cadastros/feriados/listagem',{id:id, tipo:tipo}, function(data){
    	    	var feriados = data.feriados;
    	    	var master = data.user;

				var ppeditar = $("#ppeditar").data('permissao');
				var ppexcluir = $("#ppexcluir").data('permissao');

    	    	for(i in feriados){
    	    		var tipo = feriados[i].frtipo == 'N' ? 'Nacional' : 'Regional';
    	    		var data = moment(feriados[i].frdata).format('DD/MM');

    	    		var bt = '';
    	    		if(master == 'S' || feriados[i].frtipo == 'R'){
						if(ppeditar){
							bt  +='<a class="btn btn-tb btn-info" href="'+ROOT+'/painel/cadastros/feriados/editar/'+feriados[i].frcodigo+'"><i class="fa fa-pencil"></i></a>'
						}
						if(ppexcluir){
							bt +='<a class="btn btn-tb btn-danger excluir-feriado" data-id="'+feriados[i].frcodigo+'" href="'+ROOT+'/painel/cadastros/feriados/excluir/'+feriados[i].frcodigo+'"><i class="glyphicon glyphicon-minus"></i></a>'
						}
    	    		}

    				var local = [];
    				local.push(data)
    				local.push(feriados[i].frdescricao);
    				local.push(tipo);
    				local.push(feriados[i].cliente.clfantasia);

    	    		var tr = '';
    	    		tr += '<td>'+bt+'</td>'
    	    		local.push(tr);

    	    		dataSet.push(local);
    	    	}
    	    	if($.fn.DataTable.isDataTable('#feriadosTable')) {
    	    		$('#feriadosTable').DataTable().destroy();
    	    	}
    	    	table = $('#feriadosTable').DataTable({
    	    		paging: false,
    	            retrieve: true,
    	            language: traducao,
    	            dom: 'Bfrtip',
    	            data: dataSet,
    	            buttons:
    	                [{
    	                    extend: 'pdf',
                           	className: 'btn btn-lg btn-default exportar',
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
    	                   className: 'btn btn-lg btn-default exportar',
    	                   exportOptions: { columns: [0,1,2,3] }
    	               },{
    	                   extend: 'csv',
    	                   footer: false,
    	                   className: 'btn btn-lg btn-default exportar',
    	                   exportOptions: { columns: [0,1,2,3] }
    	               }],
    	            initComplete: function () {
    	                $('.dt-buttons').prepend('<span class="label-botoes-table">Exportar para: </span>');
    	                $('.exportar').removeClass("dt-button buttons-pdf buttons-csv buttons-excel buttons-html5");
    	            }
    	    	})
    	    	dataSet = [];
    	    })
        }

        $(buscaCli).trigger('change');
	})

    $("#ipDataFeriado").datepicker({
		format: 'dd/mm',
		language: 'pt-BR',
	})

	$(document).on('click', '.excluir-feriado', function(e){
		e.preventDefault()
		var modal = $('#modalDeleta');
		var body = $('#modalDeleta').find('.modal-body');
		var bt = $('#modalDeleta').find('#btnDelModal');
		var footer = $("#modalDeleta").find('.modal-footer');
		var id = $(this).data('id');

		$(body).prepend('Ao remover esse feriado não será mais possível recuperá-lo <br />');
		var bts = '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>'
          bts += '<a id="btnDelModal" href="'+ROOT+'/painel/cadastros/feriados/excluir/'+id+'" class="btn btn-primary">Salvar alterações</a>'
        $(footer).html(bts);
		$(modal).modal('show');
	})
	$(".data-feriado").change(function(){
		console.log(this.value)
		console.log(this.getAttribute("data-date-format"))
		var data = $(this).val();
		var id = $('#frcliente').val();
		this.setAttribute(
			"data-date",
			moment(this.value, "YYYY-MM-DD")
			.format( this.getAttribute("data-date-format") )
		)
		duplicados(data, id);
	})

	$('#frcliente').change(function(){
		var data = $('.data-feriado').val();
		var id = $(this).val();
		duplicados(data, id);
	})

	function duplicados(data, id){
		if(data.length > 0){
			var idfr = $(".data-feriado").data('id');
			$.post(ROOT+'/painel/cadastros/feriados/duplicados', {data:data, id:id}, function(data){
				feriados = data.length
				$('.sm-4-data').find('.help-block').remove();
				if(feriados > 0){
					var thad = true;
					for(i in data){
						if(data[i].frcodigo == idfr){
							thad = false;
						}
					}
					if(thad){
						$('.sm-4-data').addClass('has-error')
						.append('<p class="help-block">Já existe feriado para a data selecionada: ('+data[0].frdescricao+')</p>');
						$("#salvarFeriado").attr('disabled', true);
					}else{
						$('.sm-4-data').removeClass('has-error');
						$("#salvarFeriado").attr('disabled', false);
					}
				}else{
					$('.sm-4-data').removeClass('has-error');
					$("#salvarFeriado").attr('disabled', false);
				}
			})
		}
	}

	$('.filtros_feriados').click(function(){
		var tipo = $(this).data('tipo');
		$('.filtros_feriados')
		.removeClass('btn-primary')
		.addClass('btn-default');
		$(this).addClass('btn-primary');

		// var table = $('#feriadosTable');
		// if(tipo.length > 0){
		// 	$(table).find('tbody tr').hide();
		// 	$(table).find('tbody .tipo-'+tipo).show();
		// }else{
		// 	$(table).find('tbody tr').show();
		// }
	})
})
