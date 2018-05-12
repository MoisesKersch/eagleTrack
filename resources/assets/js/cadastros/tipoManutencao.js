$(document).ready(function() {

    var dataSet = [];
    var table;

    function ajaxAtualizaTabelaTipoManutencao(){
        $.ajax({
            type: "POST",
            url: ROOT+'/painel/manutencao/tipo_manutencao/listar',
            data: {clientesbusca:$('#selectClientesTipoManutencao').val()},
                   dataType: "json",
            'success': function (data) {
                for (var tim in data.tipos_manutencao) {

                    var local = [];

                    if(data.tipos_manutencao[tim].timdescricao == undefined){
                        data.tipos_manutencao[tim].timdescricao = "";
                    }
                    local.push(data.tipos_manutencao[tim].timdescricao)

                    if(data.tipos_manutencao[tim].timkmpadrao == undefined){
                        data.tipos_manutencao[tim].timkmpadrao = "";
                    }
                    local.push(data.tipos_manutencao[tim].timkmpadrao)

                    if(data.tipos_manutencao[tim].clnome == undefined){
                        data.tipos_manutencao[tim].clnome = "";
                    }
                    local.push(data.tipos_manutencao[tim].clnome)

                    var td = "";
                    var ticodigo = data.tipos_manutencao[tim].ticodigo;

                  td += '<a title="Editar Tipo de Manutenção" class="btn btn-tb btn-info" href="'+ROOT+'/painel/manutencao/tipo_manutencao/show/'+ticodigo+'"><span class="fa fa-pencil"></span></a>'
                  td += '<a title="Deletar" id="deleteTipoMntc" class="btn btn-tb btn-danger" data-id="'+ticodigo+'"  ><span class="fa fa-minus"></span></a>';
                  //
                //   td += '<a href="'+ROOT+'/painel/manutencao/tipo_manutencao/destroy/'+ticodigo+'" title="Excluir Tipo de Manutenção" class=" btn btn-tb btn-danger">'
                //       td += '<span class="glyphicon glyphicon-minus"></span>'
                //   td += '</a>';

                  local.push(td);

                  dataSet.push(local);
                }

                $('#tbListaTipoManutencao').DataTable().destroy();

                table =  $('#tbListaTipoManutencao').DataTable({
                    paging: false,
                    retrieve: true,
                    language: traducao,
                    dom: 'Bfrtip',
            		buttons:
            			[{
            	           extend: 'pdf',
                           className: 'btn btn-lg btn-default exportar',
            	           exportOptions: { columns: [0,1,2] },
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
                           filename: 'Excel',
            			   exportOptions: { columns: [0,1,2] }
            		   },{
                          extend: 'csv',
                          footer: false,
                          className: 'btn btn-lg btn-default exportar',
                          exportOptions: { columns: [0,1,2] }
                       },{
                           extend: 'print',
                           text: 'Imprimir',
                           orientation: 'landscape',
                           footer: false,
                           className: 'btn btn-lg btn-default exportar',
                           exportOptions: { columns: [0,1,2] }
                       }],
                    data: dataSet,
                    initComplete: function () {
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


    $(document).on('click','#deleteTipoMntc',function(){
        $('#modalDeleta').modal('show');
        $('#btnDelModal').data('id',$(this).data('id'));
        $('#btnDelModal').addClass('delTipoManutencao');
    });

    $(document).on('click','.delTipoManutencao',function(){
        var id = $(this).data('id');
        $.post(ROOT+'/painel/manutencao/tipo_manutencao/excluir',
        {
            id:id
        },function(data){
            $('#modalDelata').modal('hide');
            ajaxAtualizaTabelaTipoManutencao();
        })
    });

    $("#selectClientesTipoManutencao").on("change", function () {
            ajaxAtualizaTabelaTipoManutencao();
    });

    $(document).ready(function(){
        $('#selectClientesTipoManutencao').trigger('change');
    })

});
