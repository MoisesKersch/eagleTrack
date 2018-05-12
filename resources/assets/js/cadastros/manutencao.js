$(document).ready(function() {

    var dataSet = [];
    var table;

    function ajaxAtualizaTabelaManutencao(){
        $("#tbListaManutencao tbody").html('<tr><td class="load" colspan="7"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>');
        $.ajax({
            type: "POST",
            url: ROOT+'/painel/manutencao/manutencao/listar',
            data: {clientesbusca:$('#selectClientesManutencao').val(),
                   filtro:$('#filtroManutencaoAgendada').val(),
                   tipo_manutencao:$('#selectListTipoManutencao').val()},
                   dataType: "json",
            'error': function (data){
                $("#tbListaManutencao tbody").html('<tr><td class="load" colspan="7"><span class="margin-bottom"></span>Nada Encontrado!!</td></tr>');
            },
            'success': function (data) {

                if(data.manutencoes.length > 0){
                    for (var map in data.manutencoes) {
                        var local = [];

                        if(data.manutencoes[map].biplaca == undefined){
                            data.manutencoes[map].biplaca = "";
                        }
                        local.push(data.manutencoes[map].biplaca)

                        if(data.manutencoes[map].veprefixo == undefined){
                            data.manutencoes[map].veprefixo = "";
                        }
                        local.push(data.manutencoes[map].veprefixo)

                        if(data.manutencoes[map].vedescricao == undefined){
                            data.manutencoes[map].vedescricao = "";
                        }
                        local.push(data.manutencoes[map].vedescricao)

                        if(data.manutencoes[map].timdescricao == undefined){
                            data.manutencoes[map].timdescricao = "";
                        }
                        local.push(data.manutencoes[map].timdescricao)

                        if(data.manutencoes[map].mohodometro == undefined){
                            data.manutencoes[map].mohodometro = "";
                        }
                        local.push(data.manutencoes[map].mohodometro)

                        if(data.manutencoes[map].mapkmprogramado == undefined){
                            data.manutencoes[map].mapkmprogramado = "";
                        }
                        local.push(data.manutencoes[map].mapkmprogramado)

                        if(data.manutencoes[map].clnome == undefined){
                            data.manutencoes[map].clnome = "";
                        }
                        local.push(data.manutencoes[map].clnome)

                        local.push(data.manutencoes[map].mapstatus);

                        var td = "";
                        var macodigo = data.manutencoes[map].macodigo;

                        if(data.manutencoes[map].mapstatus === "P"){
                            enabled = '';
                        }else{
                            enabled = 'disabled';
                        }

                          td += '<a title="Realizado" '+enabled+' data-km-padrao="'+data.manutencoes[map].timkmpadrao+'" \
                                data-vecodigo="'+data.manutencoes[map].vecodigo+'" \
                                data-clcodigo="'+data.manutencoes[map].mapcliente+'" \
                                data-macodigo="'+data.manutencoes[map].macodigo+'" \
                                data-ticodigo="'+data.manutencoes[map].ticodigo+'" \
                                data-km-padrao="'+data.manutencoes[map].timkmpadrao+'" \
                                data-id="'+macodigo+'" \
                                data-descricao="'+data.manutencoes[map].timdescricao+'" \
                                data-kms="'+data.manutencoes[map].mohodometro+'" \
                                id="mntcRealizada" class="btn btn-tb btn-success" ><span class="fa fa-check"></span></a>'
                          td += '<a title="Editar" id="editMntc" '+enabled+' data-id="'+macodigo+'" data-kms="'+data.manutencoes[map].mapkmprogramado+'" data-descricao="'+data.manutencoes[map].timdescricao+'"  class="btn btn-tb btn-info " ><span class="fa fa-pencil"></span></a>'
                          td += '<a title="Deletar" id="deleteMntc" class="btn btn-tb btn-danger" data-id="'+macodigo+'"  ><span class="fa fa-minus"></span></a>';

                        enabled = '';
                      local.push(td);
                      dataSet.push(local);
                    }
                $("#tbListaManutencao tbody").html('');
                }else{
                    $("#tbListaManutencao tbody").html('<tr><td class="load" colspan="7"><span class="margin-bottom"></span>Nada Encontrado!!</td></tr>');
                }

                $('#tbListaManutencao').DataTable().destroy();
                table =  $('#tbListaManutencao').DataTable({
                    columnDefs: [
                        { "width": "75px", "targets": 0 },
                        { "width": "200px", "targets": 6 },
                        { "width": "240px", "targets": 8 },
                        {
                            "targets":  7,
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    paging: false,
                    retrieve: true,
                    language: traducao,
                    dom: 'Bfrtip',
                    fnCreatedRow: function( nRow, aData, iDataIndex ) {
                        if((aData[5] - aData[4]) < 1000 && (aData[5] - aData[4]) > 0 && aData[7] == 'P'){
                            $('td:eq(4)', nRow).parent().addClass('warning');
                        }else if(aData[4] >= aData[5] && aData[7] == 'P'){
                            $('td:eq(4)', nRow).parent().addClass('danger');
                        }else if (aData[7] == 'P'){
                            $('td:eq(4)', nRow).parent().addClass('success');
                        }
                    },
            		buttons:
            			[{
            	           extend: 'pdf',
                           orientation: 'landscape',
                           className: 'btn btn-lg btn-default exportar',
            	           exportOptions: { columns: [0,1,2,3,4,5,6] },
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
            			   exportOptions: { columns: [0,1,2,3,4,5,6] }
            		   },{
                          extend: 'csv',
                          footer: false,
                          className: 'btn btn-lg btn-default exportar',
                          exportOptions: { columns: [0,1,2,3,4,5,6] }
                       },{
                          extend: 'print',
                          text: 'Imprimir',
                          orientation: 'landscape',
                          footer: false,
                          className: 'btn  btn-lg btn-default exportar',
                          exportOptions: { columns: [0,1,2,3,4,5,6] }
                       }
                    ],
                    data: dataSet,
                    initComplete: function () {
                        $('.dt-buttons').prepend('<span class="label-botoes-table">Exportar para: </span>');
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

    $(document).on('click','#deleteMntc',function(){
        $('#modalDeleta').modal('show');
        $('#btnDelModal').data('id',$(this).data('id'));
        $('#btnDelModal').addClass('delManutencao');
    });

    $(document).on('click','.delManutencao',function(){
        var id = $(this).data('id');
        $.post(ROOT+'/painel/manutencao/manutencao/excluir',
        {
            id:id
        },function(data){
            $('#modalDelata').modal('hide');
            ajaxAtualizaTabelaManutencao();
        })
    });


    //EDITAR MANUTENÇÃO
    //---------___-----------------------------------_----------------------------
    $(document).on('click','#btConfirmaManutencaoModal',function(){
        var thad = $(this);
        var kmManu = $('#confirma-manu').val();
        var id = $('#btConfirmaManutencaoModal').data('id');
        $.post(ROOT+'/painel/manutencao/manutencao/realiza_manutencao',
        {
            km_manu:kmManu,
            id:id
        }, function(data){
            $('#modalConfirmaManutencao').modal('toggle');
            $(".mensagem-suss").html('').fadeIn().append('Gravado com <strong>Sucesso!</strong>')
            setTimeout(function() {
                $(".mensagem-suss").fadeOut();
            },3000);

            $('#modalProximaManutencao').modal('show');
            $('#nova-manu').val(
                parseInt($('#btConfirmaManutencaoModal').data('km-padrao')) +
                parseInt($('#confirma-manu').val()));

            $('#btConfirmaProximaManutencaoModal').data('vecodigo',$('#btConfirmaManutencaoModal').data('vecodigo'));
            $('#btConfirmaProximaManutencaoModal').data('clcodigo',$('#btConfirmaManutencaoModal').data('clcodigo'));
            $('#btConfirmaProximaManutencaoModal').data('macodigo',$('#btConfirmaManutencaoModal').data('macodigo'));
            $('#btConfirmaProximaManutencaoModal').data('ticodigo',$('#btConfirmaManutencaoModal').data('ticodigo'));
            $('#btConfirmaProximaManutencaoModal').data('km-padrao',$('#btConfirmaManutencaoModal').data('km-padrao'));
            $('#btConfirmaProximaManutencaoModal').data('id',$('#btConfirmaManutencaoModal').data('id'));
            $('#span-nova-manu-txt').text($('#span-manu-txt').text());
        })
    });

    $(document).on('click','#btEditManutencaoModal',function(){
        var km_edit = $('#edit-manu').val();
        var id = $('#btEditManutencaoModal').data('id');
        $.post(ROOT+'/painel/manutencao/manutencao/edit_manutencao',
        {
            km_edit:km_edit,
            id:id
        }, function(data){
            $('#modalEditManutencao').modal('hide');
            $(".mensagem-suss").html('').fadeIn().append('Editado com <strong>Sucesso!</strong>')
            setTimeout(function() {
                $(".mensagem-suss").fadeOut();
            },3000);

            ajaxAtualizaTabelaManutencao();
        })
    });

    $(document).on('click','#btConfirmaProximaManutencaoModal',function(){
        var thad = $(this);
        var kmManu = $('#confirma-manu').val()
        var km_proxima = $('#nova-manu').val()
        var veic_man = $('#btConfirmaProximaManutencaoModal').data('vecodigo');
        var cliente_man = $('#btConfirmaProximaManutencaoModal').data('clcodigo');
        var ticodigo = $('#btConfirmaProximaManutencaoModal').data('ticodigo');
        var id = $('#btConfirmaProximaManutencaoModal').data('id');

        $.post(ROOT+'/painel/manutencao/manutencao/save_new',
        {
            km_manu:kmManu,
            veic_man:veic_man,
            ticodigo:ticodigo,
            cliente_man:cliente_man,
            km_proxima:km_proxima,
            id:id
        }, function(data){
            $('#modalProximaManutencao').modal('hide');
            $(".mensagem-suss").html('').fadeIn().append('Gravado com <strong>Sucesso!</strong>')
            setTimeout(function() {
                $(".mensagem-suss").fadeOut();
            },3000);

            ajaxAtualizaTabelaManutencao();

        })
    });


    $(document).on('click','.bt-cancela-modal',function(){
        $('#modalConfirmaManutencao').modal('hide');
        $('#modalProximaManutencao').modal('hide');
        $('#modalEditManutencao').modal('hide');
        $('#modalEditItensRota').modal('hide');
        ajaxAtualizaTabelaManutencao();
    });

    $(document).on('click','#mntcRealizada',function(){
        $('#modalConfirmaManutencao').modal('show');
        $('#confirma-manu').val($(this).data('kms'));
        $('#btConfirmaManutencaoModal').data('id',$(this).data('id'));
        $('#btConfirmaManutencaoModal').data('vecodigo',$(this).data('vecodigo'));
        $('#btConfirmaManutencaoModal').data('clcodigo',$(this).data('clcodigo'));
        $('#btConfirmaManutencaoModal').data('macodigo',$(this).data('macodigo'));
        $('#btConfirmaManutencaoModal').data('km-padrao',$(this).data('km-padrao'));
        $('#btConfirmaManutencaoModal').data('ticodigo',$(this).data('ticodigo'));
        $('#span-manu-txt').text($(this).data('descricao'));

    });

    $(document).on('click','#editMntc',function(){
        $('#modalEditManutencao').modal('show');
        $('#btEditManutencaoModal').data('id',$(this).data('id'));
        $('#span-edit-manu-txt').text($(this).data('descricao'));
        $('#edit-manu').val($(this).data('kms'));
    });

    $(document).on('click','#deleteMntc',function(){
            $('#myModalLabel').text("Deletar");

        // var id = $('#deleteMntc').data('id');
        // console.log(id);
        // $.post(ROOT+'/painel/manutencao/manutencao/destroy',
        // {
        //     id:id
        // },function(dados){
        //     $(".mensagem-suss").html('').fadeIn().append('Removido com <strong>Sucesso!</strong>')
        //     setTimeout(function() {
        //         $(".mensagem-suss").fadeOut();
        //     },3000);
        //
        // })
    });


    $("#selectClientesManutencao").on("change", function () {
            ajaxAtualizaTabelaManutencao();
    });

    $(".km-padrao").on("change", function () {
        if($(this).val() < 0){
            $(this).val(0);
        }
    });

    $(document).ready(function(){
        $('#selectClientesManutencao').trigger('change');
    })


    $("#selectClientesManutencao, #selectClientesManutencaoCadastro").change(function(){
        var clientes = $(this).val();

        $.post(ROOT+'/painel/manutencao/manutencao/tipos_manutencoes',
            {
                clientes: clientes
            },
        function(dados){
            console.log(dados);
            var d = dados
            var dados = d.tipo_manutencao
            var opt = '<option value="-1">Selecione</option>';
            for(i in dados) {
                opt += '<option data-km="'+dados[i].timkmpadrao+'" value="'+dados[i].ticodigo+'">'+dados[i].timdescricao+'</option>'
            }
            $("#selectListTipoManutencao, #selectTipoManutencaoCadastro").html(opt);

            var dados = d.veiculos
            var opt = '<option value="-1">Selecione</option>';
            for(i in dados) {
                opt += '<option data-placa="'+dados[i].veplaca+'" value="'+dados[i].vecodigo+'">'+dados[i].veplaca+' | '+dados[i].veprefixo+'</option>'
            }
            $("#selectPlacaManutencaoCadastro").html(opt);

        })
    })

    //CADASTRO------------------------------------------------------------------
    $('#selectTipoManutencaoCadastro').on('change', function(){
        var kmManutencao = $('#selectTipoManutencaoCadastro option:selected').data('km');
        $('#kmmanutencao').val(kmManutencao);

        calculaManutencao();
        checkBtSave();
    })

    $('#kmmanutencao').on('change', function(){
        calculaManutencao();
        checkBtSave();
    })

    function calculaManutencao(){
        $('#kmProximaManutencao').val(parseInt($('#kmmanutencao').val()) + parseInt($('#kmatual').val()));
    }

    function checkBtSave(){
        if($('#selectTipoManutencaoCadastro option:selected').val() != -1 && $('#selectPlacaManutencaoCadastro option:selected').val() != -1){
            $('.bt_save_manutencao').prop('disabled',false);
        }else{
            $('.bt_save_manutencao').prop('disabled',true);
        }
    }

    $('#selectPlacaManutencaoCadastro').on('change', function(){
        var thad = $(this);
        $.ajax({
            type: "POST",
            url: ROOT+'/painel/cadastros/veiculos/hodometro_horimetro',
            data: {placa:$('#selectPlacaManutencaoCadastro option:selected').data('placa')},
                   dataType: "json",
            'success': function (data) {
                if (data.mohodometro != null){
                    $('#kmatual').val(parseInt(data.mohodometro / 1000));
                }else{
                    $('#kmatual').val(0);
                }
                calculaManutencao();
                checkBtSave();
            }
        });

    });

    $("#selectClientesManutencaoCadastro").trigger('change');

    // // Colocar trigger para change em #selectTipoManutencao
    // $("#selectTipoManutencao").trigger('change');


    $(".fl_man_st").on("click",function(){
        if($(this).attr('id') == "todasMan"){
            $("#filtroManutencaoAgendada").val("todas");
            $(".fl_man_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
            $("#tbListaManutencao tbody").html('<tr><td class="load" colspan="7"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
        }else if($(this).attr('id') == "proximaMan"){
            $("#filtroManutencaoAgendada").val("proxima");
            $(".fl_man_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
            $("#tbListaManutencao tbody").html('<tr><td class="load" colspan="7"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
        }else if($(this).attr('id') == "vencidaMan"){
            $("#filtroManutencaoAgendada").val("vencida");
            $(".fl_man_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
            $("#tbListaManutencao tbody").html('<tr><td class="load" colspan="7"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
        }else{
            $("#filtroManutencaoAgendada").val("realizadas");
            $(".fl_man_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
            $("#tbListaManutencao tbody").html('<tr><td class="load" colspan="7"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gerando</td></tr>')
        }
        ajaxAtualizaTabelaManutencao();
    });

    $("#selectClientesManutencao, #selectListTipoManutencao").on('change', function(){
        ajaxAtualizaTabelaManutencao();
    })

    $("#kmatual").change(function(){
        calculaManutencao();
        checkBtSave();
    });

});
