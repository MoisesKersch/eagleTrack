$(document).ready(function(){
    var lisUser = $("#listarUsuarios")

    lisUser.each(function(index, element) {
        var elem = $(element)
        var selectCliente = elem.find('.cliente-usuario');
        var status = elem.find('.bt-filtros-update.btn-primary')
        selectCliente.on('change', function(e){
            e.preventDefault();
            var thad = $(this);
            var id = $(this).val();
            var t = id.indexOf("00")
            if(t == 0) {
                $(selectCliente).children().attr('selected', true)
                $(selectCliente).select2({
                    "language": "pt-BR"
                })
            }
            populaTabelaUsuario(thad)
        })
    })

    function populaTabelaUsuario(thad){
        var id = $(thad).val()
        $.post(ROOT+'/painel/cadastros/usuarios/clientes',
        {
            id:id,
        },
        function(dados){
            var user = dados.usuarios;
            var nome = dados.nome;
            var dataSet = []
            var status = $('.bt-filtros-update.btn-primary').attr('data-val')

            var ppeditar = $("#ppeditar").data('permissao');
            var ppexcluir = $("#ppexcluir").data('permissao');

            for(i in user) {
                if(user[i].usuativo == status || status == 'T') {
                    var hid = user[i].usuativo == 'S' ? 'hidden' : ''
                    var hidden = user[i].usuativo == 'N' ? 'hidden' : ''
                    var tr = ''
                    var data = []
                    data.push(user[i].name);
                    data.push(user[i].email);
                    data.push(user[i].clnome);

                    if(ppexcluir){
                        tr += '<a href="#" title="Desativar usuário" class="btDelModal btn-tb btn btn-danger '+hidden+' desativar-cadastros" data-toggle="modal" data-target="#modalDelataDesativa" data-delete-action="'+ROOT+'/painel/cadastros/usuarios/desativar/'+user[i].id+'">'
                            tr += '<span class="fa fa-ban"></span>'
                        tr += '</a>'
                        tr += '<a href="#" title="Ativar usuário" data-url="'+ROOT+'/painel/cadastros/usuarios/ativar/'+user[i].id+'" data-id="'+user[i].id+'" class="btn btn-tb '+hid+' ativar-cadastros btn-success">'
                            tr += '<span class="fa fa-check"></span>'
                        tr += '</a>'
                    }
                    if(ppeditar){
                        tr += '<a title="Editar Usuário" class="btn btn-info" href="'+ROOT+'/painel/cadastros/usuarios/editar/'+user[i].id+'">'
                            tr += '<span class="fa fa-pencil"></span>'
                        tr += '</a>'
                    }
                    data.push(tr)
                    dataSet.push(data)
                }
            }
            console.log(dataSet);
            if ($.fn.DataTable.isDataTable('#tableListarUsuario')) {
                $('#tableListarUsuario').DataTable().destroy();
            }
            table =  $('#tableListarUsuario').DataTable({
                paging: false,
                retrieve: true,
                language: traducao,
                dom: 'Bfrtip',
                buttons:
                    [{
                        extend: 'pdf',
                        message: 'Emitido por '+nome+' data da emissão '+moment().format('L')+'',
                        className: 'btn btn-lg  btn-default exportar',
                        text: 'PDF',
                        pageSize: 'A4',
                        //download: 'open',
                        orientation: 'portrait',
                        exportOptions: { columns: [0,1,2] },
                        customize: function (doc) {
                            doc.defaultStyle.alignment = 'center';
                            doc.styles.tableHeader.alignment = 'center';
                            doc.content[2].table.widths =
                            Array(doc.content[2].table.body[0].length + 1).join('*').split('');
                       }
                    },{
                       extend: 'excel',
                       footer: false,
                       className: 'btn btn-lg btn-default exportar',
                       exportOptions: { columns: [0,1,2]
                       }
                   },{
                       extend: 'csv',
                       footer: false,
                       className: 'btn btn-lg btn-default exportar',
                       exportOptions: { columns: [0,1,2] }
                   },{
                       extend: 'print',
                       text: 'Imprimir',
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
        })
    }
    $(".bt-filtros-usuario").click(function(){
        var thad = $(".cliente-usuario")
        populaTabelaUsuario(thad)
        $(this).removeClass('btn-default').addClass('btn-primary')
        $(this).siblings().addClass('btn-default').removeClass('btn-primary')
    })


    $('.multcliente').on('change',function(){
        $.ajax({
            type: 'POST',
            url: ROOT+'/painel/cadastros/usuarios/perfis',
            data: {'empresas': $(this).val()},
            success: function(response){
                var perfis = response.perfis;

                var oldPerfilSelected = $('#usuPerfil').val();
                var html = '';
                for (var i in perfis) {
                    var perfil = perfis[i];
                    if(perfil.pecodigo == $('#usuPerfil').val() || $('.usuperfil-cadastrado-edicao').val() == perfil.pecodigo){
                        html += `<option selected value="`+perfil.pecodigo+`">`+perfil.pedescricao+`</option>`;
                    }else{
                        html += `<option value="`+perfil.pecodigo+`">`+perfil.pedescricao+`</option>`;
                    }
                }
                $('#usuPerfil').html(html);
            }
        });
    });

    $(document).ready(function(){
        $('.multcliente').trigger('change');
        $('.cliente-usuario').trigger('change');
    })
})
