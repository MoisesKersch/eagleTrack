$(document).ready(function(){
    var listCliete = $("#listarClientes");
    var cadCliente = $("#cadastroCliente");
    cadCliente.each(function(idx, element){
        var elem = $(element);
        var inputApi = elem.find(".api-key-cliente");
        var mostrarApi = elem.find(".habilita-api");
        var blocKey = elem.find(".block-key-cliente");
        var newKey = elem.find(".bt-new-key");
        var semAjudanteEspera = elem.find(".sem-ajudante-espera")
        var semAjudanteTrabalhada = elem.find(".sem-ajudante-trabalhada")
        var parametrosJornada = elem.find(".parametrosJornada");
        var track = elem.find("#track");
        newKey.on('click', function(e){
            bts = '';
            bts += '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>';
            bts += '<button type="button" class="btn btn-primary bt-modal-disable">Confirmar</button>';
            $("#modalAlerta .modal-footer").html(bts)
            $("#modalAlerta .modal-title").html('Alerta!!!')
            $("#modalAlerta .modal-body").html('<span>Se você confirmar essa ação, uma nova chave será gerada, e os acessos do cliente serão bloqueados. Você deverá informar a nova chave para o cliente, para que ele possa implementar em suas integrações. Deseja mesmo continuar?</span>')
            e.preventDefault()
            $(".bt-modal-disable").click(function(){
                $("#modalAlerta").modal('hide');
                $.post(ROOT+'/painel/cadastros/clientes/key', function(data){
                    inputApi.val(data.key);
                })
            })
        })
        if($(parametrosJornada).data('param')){
            $('.parametrosJornada').trigger('click');
        }
        if($(semAjudanteTrabalhada).is(':checked')){
            $('.pontos-clientes').removeClass('hidden');
        }
        semAjudanteEspera.on('click', function(){
            $('.pontos-clientes').addClass('hidden');
        })
        semAjudanteTrabalhada.on('click', function(){
            $('.pontos-clientes').removeClass('hidden');
        })

        track.click(function(){
            desabilitarModulos($(this))
        })

        function desabilitarModulos(thad){
            if($(thad).is(':checked')){
                $('#eagleTrackApp').attr('disabled', false);
                $("#controleJornada").attr('disabled', false);
                $("#manutencao").attr('disabled', false);
                $("#rotaManual").attr('disabled', false);
                $("#rotaAutomatica").attr('disabled', false);
            }else{
                $('#eagleTrackApp').prop("checked", false).attr('disabled', true);
                $("#controleJornada").prop("checked", false).attr('disabled', true);
                $("#manutencao").prop("checked", false).attr('disabled', true);
                $("#rotaManual").prop("checked", false).attr('disabled', true);
                $("#rotaAutomatica").prop("checked", false).attr('disabled', true);
            }
        }

        mostrarApi.on('click', function(e){
            e.preventDefault();
            var thad = $(this);
            var id = $(this).data('id');
            if($('.block-key-cliente').hasClass('hidden')){
                $(thad).html('Desabilitar API');
                $('.block-key-cliente').removeClass('hidden');
                $(inputApi).attr('disabled', false);
            }else{
                $(thad).html('Habilitar API');
                $.post(ROOT+'/painel/cadastros/clientes/key/remove/'+id, function(data){
                    $('.block-key-cliente').addClass('hidden');
                    $(inputApi).attr('disabled', true);
                })
            }
        })
        var copyTextareaBtn = document.querySelector('.bt-copiar-key');
        copyTextareaBtn.addEventListener('click', function(event) {
            var copyTextarea = document.querySelector('.api-key-cliente');
            copyTextarea.select();
            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.log('Oops, unable to copy');
            }
        });
    })

    listCliete.each(function(index, element){
        var elem = $(element);
        var btStatus = elem.find('.bt-filtros-clientes');
        var btTipo = elem.find('.bt-tipo-pessoa');
        var tipoSelected = $(".bt-tipo-pessoa.btn-primary");
        var statusSelected = $(".bt-filtros-clientes.btn-primary");

        $(btTipo).on('click', function(e){
            e.preventDefault();
            $(this).removeClass('btn-default').addClass('btn-primary');
            btTipo.not(this).addClass('btn-default').removeClass('btn-primary');

            var statusCliente = $(statusSelected).attr('data-val');
            var tipoPessoa = $(this).attr('data-val');
            filtroCliente(statusCliente,tipoPessoa)
        })

        $(btStatus).on('click', function(e){
            e.preventDefault();
            $(this).removeClass('btn-default').addClass('btn-primary');
            btStatus.not(this).addClass('btn-default').removeClass('btn-primary');
            var statusCliente = $(this).attr('data-val');
            var tipoPessoa = $(tipoSelected).attr('data-val');
            filtroCliente(statusCliente,tipoPessoa)
        })

        $(tipoSelected).trigger('click')
    })
})

function filtroCliente(statusCliente,tipoPessoa) {
    $.post(ROOT+'/painel/cadastros/clientes/filtros', function(data){
        var clientes = data.clientes
        var nome = data.nome
        var dataSet = []
        for(i in clientes) {
            var tbody = '';
            var data = [];
            var hid = clientes[i].clstatus == 'A' ? '' : 'hidden';
            var hidden = clientes[i].clstatus == 'I' ? '' : 'hidden';
            if((clientes[i].clstatus == statusCliente || statusCliente == 'T') && (clientes[i].cltipo == tipoPessoa || tipoPessoa == 'T')) {
                var mail = typeof clientes[i].email[0] !== 'undefined' ? clientes[i].email[0].ememail : '';
                var tel = typeof clientes[i].telefones[0] !== 'undefined' ? clientes[i].telefones[0].tlnumero : '';
                //tbody += '<tr>';
                data.push(clientes[i].clnome)
                data.push(clientes[i].cidade.cinome+'-'+clientes[i].cidade.estado.esnome)
                data.push(clientes[i].cllogradouro)
                data.push(tel)
                data.push(mail)
                tbody += '<a title="Editar Cliente" class="btn btn-info" href="'+ROOT+'/painel/cadastros/clientes/editar/'+clientes[i].clcodigo+'"><span class="fa fa-pencil"></span></a>'
                tbody += '<a href="'+ROOT+'/painel/cadastros/clientes/desativar/'+clientes[i].clcodigo+'" title="Desativar cliente" class="btDelModal '+hid+' btn btn-danger desativar-cadastros" data-toggle="modal" data-target="#modal-desativar" data-delete-action="'+ROOT+'/painel/cadastros/clientes/'+clientes[i].clcodigo+'">'
                    tbody += '<span class="fa fa-ban"></span>'
                tbody += '</a>'
                tbody += '<a href="#" title="Ativar cliente" data-url="'+ROOT+'/painel/cadastros/clientes/ativar" data-id="'+clientes[i].clcodigo+'" class="btn '+hidden+' ativar-cadastros btn-success">'
                    tbody += '<span class="fa fa-check"></span>'
                tbody += '</a>'

                    //tbody += '</td>';
                data.push(tbody)
                dataSet.push(data)
            }
        }
        if($.fn.DataTable.isDataTable('#tableListarClientes')) {
            $("#tableListarClientes").DataTable().destroy()
        }
        table =  $('#tableListarClientes').DataTable({
            paging: false,
            retrieve: true,
            language: traducao,
            dom: 'Bfrtip',
            buttons:
                [{
                    extend: 'pdf',
                    message: 'Emitido por '+nome+' data da emissão '+moment().format('L')+'',
                    className: 'btn btn-lg btn-default exportar',
                    text: 'PDF',
                    pageSize: 'A4',
                    //download: 'open',
                    orientation: 'landscape',
                    exportOptions: { columns: [0,1,2,3,4] },
                    customize: function (doc) {
                        doc.defaultStyle.alignment = 'center';
                        doc.styles.tableHeader.alignment = 'center';
                        // doc.content[2].table.widths =
                        // Array(doc.content[2].table.body[0].length + 1).join('*').split('');
                   }
                },{
                   extend: 'excel',
                   footer: false,
                   className: 'btn btn-lg btn-default exportar',
                   exportOptions: { columns: [0,1,2,3,4]
                   }
               },{
                   extend: 'csv',
                   footer: false,
                   className: 'btn btn-lg btn-default exportar',
                   exportOptions: { columns: [0,1,2,3,4] }
               },{
                   extend: 'print',
                   text: 'Imprimir',
                   orientation: 'landscape',
                   footer: false,
                   className: 'btn btn-lg btn-default exportar',
                   exportOptions: { columns: [0,1,2,3,4] }
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
