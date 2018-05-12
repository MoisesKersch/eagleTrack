$(document).ready(function() {
    var gmstatus = 'ativo';
    var dataSet = [];

    function ajaxAtualizaTabelaGM(){
        $.ajax({
            type: "POST",
            url: ROOT+'/painel/cadastros/gruposMotoristas/listarTable',
            data: {
                status:gmstatus,
                clientesbusca:$('.buscar-clientes-gm').val()
            },
            dataType: "json",
            'success': function (data) {

                var ppeditar = $("#ppeditar").data('permissao');
                var ppexcluir = $("#ppexcluir").data('permissao');

                for (var gm in data) {
                    var html = [];
                    html.push(data[gm].gmdescricao);
                    html.push(data[gm].cliente_gm.clnome);
                    var acoes = '';
                    if(ppexcluir){
                        if (data[gm].gmstatus == 'A') {
                            acoes +=
                            '<a title="Desativar Grupo" class="btDelModal btn btn-danger desativar-cadastros btn-tb" data-toggle="modal" data-target="#modalDelataDesativa" data-delete-action="'+ROOT+'/painel/cadastros/gruposMotoristas/desativar/'+data[gm].gmcodigo+'">'
                                +'<span class="fa fa-ban"></span>'
                            +'</a>';
                        } else {
                            acoes +=
                            '<a title="Ativar Grupo" data-url="'+ROOT+'/painel/cadastros/gruposMotoristas/ativar" data-id="'+data[gm].gmcodigo+'" class="btn btn-success ativar-cadastros btn-tb">'
                                +'<span class="fa fa-check"></span>'
                            +'</a>';
                        }
                    }
                    if(ppeditar){
                        acoes +=
                        '<a title="Editar Grupo" class="btn btn-info btn-tb" href="'+ROOT+'/painel/cadastros/gruposMotoristas/editar/'+data[gm].gmcodigo+'">'
                            +'<span class="fa fa-pencil"></span>'
                        +'</a>';
                    }
                    html.push(acoes);
                    dataSet.push(html);
                }

                $('#table-grupo-motorista_filter').remove()
                $('#table-grupo-motorista_paginate').remove()
                $('#table-grupo-motorista_info').remove()

                if ($.fn.DataTable.isDataTable('#table-grupo-motorista')) {
                    $('#table-grupo-motorista').DataTable().destroy();
                }

                $('#table-grupo-motorista').DataTable({
                    paging: false,
                    retrieve: true,
                    "aoColumnDefs": [
                		{ 'bSortable': false, 'aTargets': [ 2 ] }
                	],
                	"language": traducao,
                    // dom: 'Bfrtip',
                    data: dataSet,
                    columns: [
                        { title: "Descrição" },
                        { title: "Empresa" },
                        { title: "Ações" }
                    ]
                });

                dataSet = null;
                dataSet = [];

                $('.ativar-cadastros').click(function(e){
                	e.preventDefault();
                	var thad = $(this)
                	var url = $(this).attr('data-url')
                	var id = $(this).attr('data-id');
                	$.post(url, {id:id}, function(data){
                		thad.hide()
                		thad.siblings('.desativar-cadastros').removeClass('hidden')
                	})
                    $(this).parent().parent().css('display', 'none');
                })
            }
        });
    }

    $(".buscar-clientes-gm").on("change", function () {
       ajaxAtualizaTabelaGM();
    });

    $('.grumot-status').click(function() {
        $('.btn-group-altera-status button').removeClass('btn-primary');
        $('.btn-group-altera-status button').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-primary');
        gmstatus = $(this).val();
        ajaxAtualizaTabelaGM();
    });
});
