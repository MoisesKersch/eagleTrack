$(document).ready(function () {

    $('.clientesbusca').select2({
        placeholder: "Selecione",
        allowClear: true
    });


});

$(document).ready(function() {

    var dataSet = [];
    var table;

    function ajaxAtualizaTabela(){
        $.ajax({
            type: "POST",
            url: ROOT+'/painel/cadastros/modulos/listar/reload',
            data: {clientesbusca:$('.clientesbusca').val(),
    			   status:$('#status').val(),
    			   chip:$('#chip').val()},
            dataType: "json",
            'success': function (data) {
                for (var modulo in data.modulos) {

                    var local = [];
                    if(data.modulos[modulo].mocodigo == undefined){
                        data.modulos[modulo].mocodigo = "";
                    }
                    local.push(data.modulos[modulo].mocodigo)
                    if(data.modulos[modulo].moimei == undefined){
                        data.modulos[modulo].moimei = "";
                    }
                    local.push(data.modulos[modulo].moimei)
                    if(data.modulos[modulo].mosim == undefined){
                        data.modulos[modulo].mosim = "";
                    }
                    local.push(data.modulos[modulo].mosim)
                    if(data.modulos[modulo].momodelo == undefined){
                        data.modulos[modulo].momodelo = "";
                    }
                    local.push(data.modulos[modulo].momodelo)
                    if(data.modulos[modulo].moproprietario == undefined){
                        data.modulos[modulo].moproprietario = "";
                    }
                    local.push(data.modulos[modulo].moproprietario)

                    if(data.modulos[modulo].mostatus == undefined){
                        data.modulos[modulo].mostatus = "";
                    }

                    var td = "";
                    var mocodigo = data.modulos[modulo].mocodigo;

				    if(data.modulos[modulo].mostatus == 'A'){
					   td = td+"<td>"
						   +"<a id='"+mocodigo+"' title='Alterar Status' class='btstatus btn-tb btn btn-success' >"
						   +"<span class='fa fa-check'></span></a>";
				    }else{
					   td = td+"<td>"
						   +"<a id='"+mocodigo+"' title='Alterar Status' class='btstatus btn-tb btn btn-danger'>"
						   +"<span class='fa fa-ban'></span></a>";
				    }

			        td = td+"<a title='Editar Módulo' class='btn-tb btn btn-info' href='"+ROOT+"/painel/cadastros/modulos/editar/"+mocodigo+"' >"
					   +"<span class='fa fa-pencil'></span></a></td>";

                    local.push(td)

                    dataSet.push(local);
                }

                $('#modulosTable').DataTable().destroy();

                table =  $('#modulosTable').DataTable({
                    paging: false,
                    retrieve: true,
                    language: traducao,
                    dom: 'Bfrtip',
            		buttons:
            			[{
            	           extend: 'pdf',
                           className: 'btn btn-default exportar',
            	           exportOptions: { columns: [0,1,2,3,4] }
            	   		},{
            	           extend: 'excel',
            	           footer: false,
                           className: 'btn btn-default exportar',
            			   exportOptions: { columns: [0,1,2,3,4] }
            		   },{
            	           extend: 'csv',
            	           footer: false,
                           className: 'btn btn-default exportar',
            			   exportOptions: { columns: [0,1,2,3,4] }
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

    // ajaxAtualizaTabela();
    //
    // $(".clientesbusca").on("change", function () {
    //        window.location.href = ROOT+"/painel/cadastros/modulos?clientesbusca="+$('.clientesbusca').val()
    //         +"&chip="+$('#chip').val()
    //         +"&status="+$('#status').val();
    // });


    $(".clientesbusca").on("change", function () {
            ajaxAtualizaTabela();
    });

    $(".filtros_modulo").on("click",function(){
        if($(this).attr('id') == "com_chip"){
            $("#chip").val("com");
            $(".filtros_modulo").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else if($(this).attr('id') == "sem_chip"){
            $("#chip").val("sem");
            $(".filtros_modulo").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else{
            $("#chip").val("todos");
            $(".filtros_modulo").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }
        ajaxAtualizaTabela();
    });

    $(".fl_mo_st").on("click",function(){
        if($(this).attr('id') == "at_chip"){
            $("#status").val("ativo");
            $(".fl_mo_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else if($(this).attr('id') == "in_chip"){
            $("#status").val("inativo");
            $(".fl_mo_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else{
            $("#status").val("todos");
            $(".fl_mo_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }
        ajaxAtualizaTabela();
    });

    $(document).on('click','.btstatus', function(e){
        var thad = $(this);
        $.ajax({
            type: 'POST',
            url: ROOT+'/painel/cadastros/modulos/status',
            data: {'mocodigo': $(this).attr('id')},
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
                ajaxAtualizaTabela();
            }
        });
    });

    $(document).ready(function(){
        $('.clientesbusca').trigger('change');
    })


    // $(function(){
    //     var modlat = $('#modulolat').html()
    //     var modlon = $('#modulolon').html()
    //     locveiculo = L.marker([modlat,modlon],{
    //         draggable:false,
    //     }).addTo(mapa);
    // })
    // function attmonito(){
    //     var id = $(".moduloid").html()
    //     $.ajax({
    //         type: "POST",
    //         url: ROOT+'/painel/cadastros/modulos/monitor',
    //         data: {'id': id},
    //         success: function(dados){
    //             console.log(dados)
    //             console.log("aqui")
    //         }
    //     })
    // }
});
