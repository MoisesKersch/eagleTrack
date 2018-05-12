
$(document).ready(function() {

    var dataSet = [];
    var table;

    function ajaxAtualizaTabelaChips(){
        $.ajax({
            type: "POST",
            url: ROOT+'/painel/cadastros/chips/listar',
            data: {
			   status:$('#status_chip').val(),
			   modulo:$('#modulo_chip').val()},
            dataType: "json",
            'success': function (data) {
                for (var chip in data.chips) {
                    var local = [];
                    if(data.chips[chip].iccid == undefined){
                        data.chips[chip].iccid = "";
                    }
                    local.push(data.chips[chip].iccid)

                    if(data.chips[chip].chnumero == undefined){
                        data.chips[chip].chnumero = "";
                    }
                    local.push(data.chips[chip].chnumero)

                    if(data.chips[chip].choperadora == undefined){
                        data.chips[chip].choperadora = "";
                    }
                    local.push(data.chips[chip].choperadora)

                    if(data.chips[chip].modulo == undefined ){
                        data.chips[chip].modulo = "";
                    }else{
                        td = " <a title='"+data.chips[chip].modulo.mocodigo+"' href='"+ROOT+"/painel/cadastros/modulos/editar/"+data.chips[chip].modulo.mocodigo+"' >"
    					               +""+data.chips[chip].modulo.mocodigo+"</a></td>";

                        data.chips[chip].modulo = td;
                    }
                    local.push(data.chips[chip].modulo)


                    if(data.chips[chip].mostatus == undefined){
                        data.chips[chip].mostatus = "";
                    }

                    var td = "";
                    var chcodigo = data.chips[chip].chcodigo;

				    if(data.chips[chip].chstatus == 'A'){
					   td = td+"<td>"
						   +"<a id='"+chcodigo+"' title='Alterar Status' class='btstatusch btn-tb btn btn-success' >"
						   +"<span class='fa fa-check'></span></a>";
				    }else{
					   td = td+"<td>"
						   +"<a id='"+chcodigo+"' title='Alterar Status' class='btstatusch btn-tb btn btn-danger'>"
						   +"<span class='fa fa-ban'></span></a>";
				    }

			        td = td+"<a title='Editar Chip' class='btn-tb btn btn-info' href='"+ROOT+"/painel/cadastros/chips/editar/"+chcodigo+"' >"
					   +"<span class='fa fa-pencil'></span></a></td>";

                    local.push(td)

                    dataSet.push(local);
                }

                $('#chipsTable').DataTable().destroy();

                table =  $('#chipsTable').DataTable({
                    paging: false,
                    retrieve: true,
                    language: traducao,
                    dom: 'Bfrtip',
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
                    data: dataSet,
                    initComplete: function () {
                        $('.dt-buttons').prepend('<span class="label-botoes-table">Exportar para: </span>');
                        $('.exportar').removeClass("dt-button buttons-pdf buttons-csv buttons-excel buttons-html5");
                    }
                });

                dataSet = null;
                dataSet = [];

            }
        });
    }


    $(".filtros_chip").on("click",function(){
        if($(this).attr('id') == "com_modulo"){
            $("#modulo_chip").val("com_modulo");
            $(".filtros_chip").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else if($(this).attr('id') == "sem_modulo"){
            $("#modulo_chip").val("sem_modulo");
            $(".filtros_chip").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else{
            $("#modulo_chip").val("todos_modulo");
            $(".filtros_chip").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }
        ajaxAtualizaTabelaChips();
    });

    $(".fl_ch_st").on("click",function(){
        if($(this).attr('id') == "at_chip"){
            $("#status_chip").val("ativo");
            $(".fl_ch_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else if($(this).attr('id') == "in_chip"){
            $("#status_chip").val("inativo");
            $(".fl_ch_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else{
            $("#status_chip").val("todos");
            $(".fl_ch_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }
        ajaxAtualizaTabelaChips();
    });

    $(document).on('click','.btstatusch', function(e){
        var thad = $(this);
        $.ajax({
            type: 'POST',
            url: ROOT+'/painel/cadastros/chips/status',
            data: {'chcodigo': $(this).attr('id')},
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
                ajaxAtualizaTabelaChips();
            }
        });
    });

    $("#btload").on("click", function () {
            ajaxAtualizaTabelaChips();
    });

    $(document).ready(function(){
        $('#btload').trigger('click');
    })

});
