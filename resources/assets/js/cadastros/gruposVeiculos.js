$(document).ready(function() {
    var gvstatus = 'ativo';
    var dataSet = [];
    var table  = null;
    var options_selecteds = [];

    function ajaxAtualizaTabelaGV(){
        $.ajax({
            type: "POST",
            url: ROOT+'/painel/cadastros/gruposVeiculos/listarTable',
            data: {
                status:$('#status_gv').val(),
                clientesbusca:$('.buscar-clientes-gv').val()
            },
            dataType: "json",
            'success': function (data) {
                dataSet = [];

                var ppeditar = $("#ppeditar").data('permissao');
                var ppexcluir = $("#ppexcluir").data('permissao');

                for (var gv in data) {
                    var veiculos = data[gv].veiculos;
                    var html = [];
                    html.push("<td><td>");
                    html.push(data[gv].gvdescricao);
                    html.push(data[gv].cliente_gv.clnome);
                    var acoes = '';

                    if(ppeditar){
                        acoes +=
                            '<a title="Editar Grupo" class="btn btn-info btn-tb" href="'+ROOT+'/painel/cadastros/gruposVeiculos/editar/'+data[gv].gvcodigo+'">'
                                +'<span class="fa fa-pencil"></span>'
                            +'</a>';
                    }
                    if(ppexcluir){
                        acoes +=
                            `<a title="Remover Grupo" class="btDelModal btn btn-danger desativar-cadastros btn-tb tb-del-cadastro-`+data[gv].gvcodigo+`"
                                data-toggle="modal"
                                data-target="#modalDelataDesativa"
                                data-class="save-grupo-veiculo-status"
                                data-id="`+gv+`" data-delete-action="`+ROOT+`/painel/cadastros/gruposVeiculos/excluir/`+data[gv].gvcodigo+`">
                                <span class="glyphicon glyphicon-minus"></span>
                            </a>`;
                    }

                    html.push(acoes);

                    var htmlVeics = '';
                    for (var j in veiculos) {
                        var veiculo  = veiculos[j];
                        htmlVeics += '<span class="badge"> &nbsp;'+veiculo.veplaca+' | '+veiculo.veprefixo+' &nbsp; </span>'
                    }
                    html.push(htmlVeics);

                    var htmlVeicsExports = '';
                    for (var j in veiculos) {
                        var veiculo  = veiculos[j];
                        if(j == 0){
                            htmlVeicsExports += '<span> &nbsp;'+veiculo.veplaca+' | '+veiculo.veprefixo+' </span>'
                        }else{
                            htmlVeicsExports += '<span> &nbsp;-&nbsp;'+veiculo.veplaca+' | '+veiculo.veprefixo+' </span>'
                        }
                    }
                    html.push(htmlVeicsExports);

                    dataSet.push(html);
                }

                $('#table-grupo-veiculo_filter').remove()
                $('#table-grupo-veiculo_paginate').remove()
                $('#table-grupo-veiculo_info').remove()

                if ($.fn.DataTable.isDataTable('#table-grupo-veiculo')) {
                    $('#table-grupo-veiculo').DataTable().destroy();
                }
                    table = $('#table-grupo-veiculo').DataTable({
                        data: dataSet,
                        order: [[ 1, "asc" ]],
                        "columns": [
                            { "title": "Detalhes" },
                            { "title": "Descrição" },
                            { "title": "Empresa" },
                            { "title": "Ações" },
                            { "title": "Veiculos" },
                            { "title": "Veículos" },
                        ],
                        // columns : columns.AdoptionTaskInfo.columns,
                        paging: false,
                        retrieve: true,
                    	"language": traducao,
                        dom: 'Bfrtip',
                        columnDefs: [
                        {//esconder coluna veiculos
                            targets: 4,
                            "visible": false,
                            "searchable": true,
                        },
                        {//esconder coluna veiculos
                            targets: 5,
                            "visible": false,
                            "searchable": true,
                        },
                        {
                            "targets": 0 ,
                            "className": 'details-control',
                            "orderable": false
                        },
                        {//esconder coluna acoes da busca
                            "targets": 3,
                            "visible": true,
                            "searchable": false
                        },
                        ],
                        buttons:
                			[{
                	           extend: 'pdf',
                               orientation: 'landscape',
                               className: 'btn btn-lg btn-default exportar',
                	           exportOptions: { columns: [1,2,5]}

                            },{
                	           extend: 'excel',
                	           footer: false,
                               className: 'btn btn-lg btn-default exportar',
                               filename: 'Excel',
                			   exportOptions: { columns: [1,2,5] }
                		   },{
                              extend: 'csv',
                              footer: false,
                              className: 'btn btn-lg btn-default exportar',
                              exportOptions: { columns: [1,2,5] }
                           },{
                               extend: 'print',
                               text: 'Imprimir',
                               footer: false,
                               className: 'btn btn-lg btn-default exportar',
                               exportOptions: { columns: [1,2,5] }
                           }],
                       initComplete: function () {
                           $('.dt-buttons').prepend('<span class="label-botoes-table">Exportar para: </span>');
                           $('.exportar').removeClass("dt-button buttons-pdf buttons-csv buttons-excel buttons-html5");
                           $('.exportar').prepend("<span class='fa fa-save'></span>");
                       },
                       createdRow: function (row, data, index) {
                            $(row).addClass('class-remove-tr-'+index);
                        }
                    });

                $('td.details-control').addClass('bt-details fa fa-chevron-right');

                $('#table-grupo-veiculo tbody').on('click', 'td.details-control', function () {
                    var tr = $(this).closest('tr');
                    var row = table.row( tr );

                    if ( row.child.isShown() ) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                    }
                    else {
                        // Open this row
                        row.child( format(row.data()) ).show();
                        tr.addClass('shown');
                    }

                    $('.table-details').parent().css("background", "whitesmoke");
                    $('.table-details').children().children().css("background", "whitesmoke");
                    $('.table-details').css("background", "whitesmoke");
                } );
                dataSet = [];
            }
        });
    }

    function format (d) {
        return `<table cellpadding="5"  class="table-details" cellspacing="0" border="0" style="padding-left:50px;">
            <tr>
                <td>Veículos:</td>
                <td>`+d[4]+`</td>
            </tr>
        </table>`;
    }

    $(".buscar-clientes-gv").on("change", function () {
       ajaxAtualizaTabelaGV();
    });

    $(document).on('click','.save-grupo-veiculo-status',function(){
        var id = $(this).data('id');
        $('.class-remove-tr-'+id).remove();
        table.rows('.class-remove-tr-'+id).remove().draw();
        // ajaxAtualizaTabelaGV();
    });

    // $(document).on('click','.ativar-cadastros',function(){
    //     ajaxAtualizaTabelaGV();
    // });

    $(document).ready(function(){
        $('.buscar-clientes-gv').trigger('change');
    })

    $(".fl_gv_st").on("click",function(){
        if($(this).attr('id') == "at_gv"){
            $("#status_gv").val("ativo");
            $(".fl_gv_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else if($(this).attr('id') == "in_gv"){
            $("#status_gv").val("inativo");
            $(".fl_gv_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else{
            $("#status_gv").val("todos");
            $(".fl_gv_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }
        ajaxAtualizaTabelaGV();
    });

    $('.empresa-gv').change(function() {
        var id = $(this).val();
        $.ajax({
            url: ROOT+'/painel/cadastros/gruposVeiculos/veiculos',
            data: {
                'cliente': id
            },
            method: 'POST',
            success: function(retorno) {
                options = '';
                for (var x in retorno) {
                    options += '<option value="'+retorno[x].vecodigo+'">'+retorno[x].veplaca+' | '+retorno[x].vedescricao+'</option>'
                }
                $('.veiculos-grupo-veiculos').html(options);
            }
        })
    });

    $(document).on('change','.veiculos-grupo-veiculos', function(){
        var last_option_selected;
        var this_ptions = $(this).val();
        if(this_ptions.length >0){
            for (var i in this_ptions) {
                if(!options_selecteds.includes(this_ptions[i])){
                    last_options_selected = this_ptions[i];
                }
            }
            options_selecteds = this_ptions;
            $.ajax({
                url: ROOT+'/painel/cadastros/gruposVeiculos/checkVeiculo',
                data: {
                    'vecodigo': last_options_selected,
                    'gvcodigo': $('#gvcodigo').val()
                },
                method: 'POST',
                success: function(retorno) {
                    if(retorno.data != null && retorno.data != 'undefined' && retorno.data.grupo != null){
                        var grupo = retorno.data.grupo;
                        $("#modalClean .modal-title").html('<div class="conf-grupo-veiculo" data-clicked="none" data-id="'+retorno.data.vecodigo+'">Conflito de grupo de veículo </div>');
                        $("#modalClean .modal-body").html("O veículo "+retorno.data.veplaca+" | "+retorno.data.vedescricao+" está associado ao grupo "+grupo.gvdescricao+"");
                            bts =  '<button type="button" class=" btn btn-danger cancel-alteracao-grupo-veic" data-id="'+retorno.data.vecodigo+'" data-dismiss="modal">Cancelar <span class="glyphicon glyphicon-remove"></span></button>';
                            bts += '<button type="button" class=" btn btn-success save-alteracao-grupo-veic" data-id="'+retorno.data.vecodigo+'" data-dismiss="modal">Desassociar <span class="glyphicon glyphicon-ok"></span></button>';
                        $("#modalClean .modal-footer").html(bts);
                        $("#modalClean").modal('show');
                    }
                }
            })
        }else{
            options_selecteds = [];
        }
    });


    $(document).on('click','.save-alteracao-grupo-veic',function(){
        $.ajax({
            url: ROOT+'/painel/cadastros/gruposVeiculos/desassociarVeiculoGrupo',
            data: {
                'vecodigo': $(this).data('id')
            },method: 'POST',
            success: function(retorno) {
                $(".conf-grupo-veiculo").data('clicked','true');
            }
        })
    });


    $(document).on('hidden.bs.modal','#modalClean', function() {
        if($(".conf-grupo-veiculo").data('clicked') == 'none'){
            unselectOptionSelect2();
        }
    })

    $(document).on('click','.cancel-alteracao-grupo-veic',function(){
        unselectOptionSelect2();
    });

    function unselectOptionSelect2(){
        var id = $(".conf-grupo-veiculo").data('id');
        // remover selected from this option on select2;
        $('.veiculos-grupo-veiculos > option:selected').each(function() {
            if($(this).val() == id){
                $(this).prop("selected", false);
                $('.veiculos-grupo-veiculos').select2({
                    "language": "pt-BR",
                    allowClear: true
                });
            }
        });
    }

    $(document).on('click','td.details-control',function(){
        if($(this).hasClass('fa-chevron-right')){
            $(this).removeClass('fa fa-chevron-right');
            $(this).addClass('fa fa-chevron-down');
        }else{
            $(this).removeClass('fa fa-chevron-down');
            $(this).addClass('fa fa-chevron-right');
        }
    });
});
