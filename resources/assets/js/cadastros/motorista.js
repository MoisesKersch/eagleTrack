
$(document).ready(function () {

    $("#pontosRelacionados").select2({
        "language": "pt-BR"
    });

    //preencher o grupomotorista e os pontos relacionados somente quando selecionar o cliente
    $('.cliente-motorista').change(function(){

        var mtcodigo = $('#mtcodigo').val();
        var mtgrupo = $('#mtgrupo').val();
        var pontosmt = $('#pontosMt').val();
        var idJornada = $(".mtjornada").val();
        pontosmt = pontosmt.split(",");
        var tbMaisLicenca = $('.mais-licenca');

        var id = $(this).val()
        if(id > 0){
            $(tbMaisLicenca).removeClass('disabled-bt')
            .attr('title', 'Cadastrar nova licença');
            $(tbMaisLicenca).children('a').removeClass('disabled-bt');
        }else{
            $(tbMaisLicenca).addClass('disabled-bt')
            .attr('title', 'Primeiramente selecione um cliente');
            $(tbMaisLicenca).children('a').addClass('disabled-bt');
        }
        $.post(ROOT+'/painel/cadastros/motoristas/dados/cadastro',
            {
                id:id,
                mtcodigo:mtcodigo
            },
            function(dados){
                var pontos = dados.pontos
                var grupos = dados.grupos
                var pontosMotorista = dados.pontosMotorista
                var jornada = dados.jornadas
                var licenca = dados.licencas
                var opt = '';
                for(k in licenca) {
                    opt += '<option value="'+licenca[k].licodigo+'">'+licenca[k].lidescricao+'</option>';
                }
                $("#selectMtLicenca").append(opt);

                var option = '<option value="">Selecione</option>';
                for(i in jornada){
                    var selected = jornada[i].jtcodigo == idJornada ? 'selected' : ''
                    option += '<option '+selected+' value="'+jornada[i].jtcodigo+'">'+jornada[i].jtdescricao+'</option>'
                }
                $("#mtjornada").html(option)

                var selectbox = $('#pontosRelacionados').select2();
                 selectbox.find('option').remove();
                 for(j in pontos) {

                     if(pontosmt != "" && ($.inArray(pontos[j].pocodigo.toString(), pontosmt) > -1)){
                         $('<option selected>').val(pontos[j].pocodigo).text(pontos[j].podescricao).appendTo(selectbox);
                     }else{
                         $('<option>').val(pontos[j].pocodigo).text(pontos[j].podescricao).appendTo(selectbox);
                     }
                 }

                var opt = '<option value="null">Nenhum grupo</option>'

                for(i in grupos) {
                    if(mtgrupo == grupos[i].gmcodigo){
                        opt += '<option selected value="'+grupos[i].gmcodigo+'">'+grupos[i].gmdescricao+'</option>'
                    }else{
                        opt += '<option value="'+grupos[i].gmcodigo+'">'+grupos[i].gmdescricao+'</option>'
                    }
                }

                $('#mtgrupo').html(opt)
            })
    })

    $(".tb-mais-licenca").click(function(e){
        e.preventDefault()
        var id = $('.cliente-motorista').val();
        var form = '<form class="form form-licenca" action="'+ROOT+'/painel/cadastros/motoristas/mais/licenca" type="post">'
        form += '<input placeholder="Descrição" type="text" name="lidescricao" class="form-control lidescricao">'
        form += '<input type="hidden" name="licliente" value="'+id+'">'
        form += '<button type="submit" data-dismiss="modal" disabled class="btn btn-primary salvar-licenca">Salvar</button>'
        form += '</form>'
        var modal = $("#modalAlerta");
        $(modal).find(".modal-title").html('Adicionar licença/certificações');
        $(modal).find(".modal-body").html(form);
        $(".lidescricao").keyup(function(){
            if($(this).val().length > 0) {
                $(".salvar-licenca").attr('disabled', false)
            }else{
                console.log('asdf')
                $(".salvar-licenca").attr('disabled', true)
            }
        })

        $('.salvar-licenca').click(function(e){
            e.preventDefault()
            var form = $(this).parents('.form-licenca');
            $(form).ajaxForm({
                type:'post',
                success: function(dados) {
                    console.log(dados);
                    var licenca = dados.licenca;

                    if(licenca.licodigo != undefined){
                        opt = '<option value="'+licenca.licodigo+'">'+licenca.lidescricao+'</option>';
                        $("#selectMtLicenca").append(opt);
                    }
                }
            }).submit();
        })
    });

    $(".tb-add-licenca").click(function(e){
        e.preventDefault();
        var desc = $("#selectMtLicenca option:selected").text();
        var idLicenca = $("#selectMtLicenca").val();
        var val = $("#ipLivalidade").val();
        if(val.length > 0) {
            var td = '<tr>';
            td += '<td class="licenca-desc">'+desc+'</td>';
            td += '<input type="hidden" name="mllicenca[]" value="'+idLicenca+'">'
            td += '<input type="hidden" name="mlvalidade[]" value="'+val+'">'
            td += '<td>'+val+'</td>';
            td += '<td><a href="#" class="btn btn-danger remover-licenca" title="Desassociar licença"><span class="fa fa-minus"></span></a></td>'
            td += '</tr>';

        var liText = $(".table-licenca .licenca-desc");

            if($(liText).length > 0) {
                var duplicate = false;
                $(liText).each(function(i){
                    if($(liText[i]).html() == desc) {
                        duplicate = true;
                    }
                })
            }else {
                duplicate = false
            }
            if(!duplicate){
                $(".table-licenca tbody").prepend(td);
            }

        }
        $(".remover-licenca").click(function(e){
            e.preventDefault()
            removeLicenca($(this));
        })
    })

    $(".remover-licenca").click(function(e){
        e.preventDefault()
        removeLicenca($(this));
    })

});

function removeLicenca(thad) {
    var id = $(thad).data('id');
    var mot = $(thad).data('motorista');

    if(typeof id != 'undefined') {
        $.post(ROOT+'/painel/cadastros/motoristas/desassociar/licenca',
            {
                id:id,
                mot:mot
            },
            function(data){

            }
        )
    }
    $(thad).parents('tr').remove();
}

$(document).ready(function() {

    var dataSet = [];
    var table;

    function ajaxAtualizaTabelaMotoristaAjudante(){
        $.ajax({
            type: "POST",
            url: ROOT+'/painel/cadastros/motoristas/listar',
            data: {clientesbusca:$('.select-cliente-motoristas').val(),
    			   status:$('#flg_status_ma').val(),
    			   flg_ma:$('#flg_ma').val()},
            dataType: "json",
            'success': function (data) {
                var moto = data.motoristas;

                var ppeditar = $("#ppeditar").data('permissao');
                var ppexcluir = $("#ppexcluir").data('permissao');

                for(i in moto){
                    var local = [];
                    (moto[i].mtnome == undefined)? moto[i].mtnome = "" : '' ;
                    local.push(moto[i].mtnome);

                    (moto[i].mtcracha == undefined)? moto[i].mtcracha = "" : '' ;
                    local.push(moto[i].mtcracha);

                    (moto[i].mttelefone == undefined)? moto[i].mttelefone = "" : '' ;
                    local.push(moto[i].mttelefone);

                    (moto[i].mtcnh == undefined)? moto[i].mtcnh = "" : '' ;
                    local.push(moto[i].mtcnh);

                    (moto[i].mtcnhvalidade == undefined)? moto[i].mtcnhvalidade = "" : '' ;
                    local.push(moto[i].mtcnhvalidade);

                    (moto[i].cliente == undefined)? moto[i].cliente = "" : '' ;
                    local.push(moto[i].cliente.clnome);

                    var td = "";
                    var mtcodigo = moto[i].mtcodigo;

                    if(ppexcluir){
                        if(moto[i].mtstatus == 'A'){
                           td = td+"<td>"
                               +"<a id='"+mtcodigo+"' title='Alterar Status' class='btstatus_ma btn-tb btn btn-success' >"
                               +"<span class='fa fa-check'></span></a>";
                        }else{
                           td = td+"<td>"
                               +"<a id='"+mtcodigo+"' title='Alterar Status' class='btstatus_ma btn-tb btn btn-danger'>"
                               +"<span class='fa fa-ban'></span></a>";
                        }
                    }
                    if(ppeditar){
                        td = td+"<a title='Editar Motorista/Ajudante' class='btn-tb btn btn-info' href='"+ROOT+"/painel/cadastros/motoristas/editar/"+mtcodigo+"' >"
                           +"<span class='fa fa-pencil'></span></a></td>";
                    }
                    local.push(td);
                    dataSet.push(local);
                }

                $('#cadastroMotoAjudante').DataTable().destroy();

                table =  $('#cadastroMotoAjudante').DataTable({
                    paging: false,
                    retrieve: true,
                    language: traducao,
                    dom: 'Bfrtip',
            		buttons:
            			[{
            	           extend: 'pdf',
                           className: 'btn btn-default exportar',
            	           exportOptions: { columns: [0,1,2,3,4,5] }
            	   		},{
            	           extend: 'excel',
            	           footer: false,
                           className: 'btn btn-default exportar',
            			   exportOptions: { columns: [0,1,2,3,4,5] }
            		   },{
            	           extend: 'csv',
            	           footer: false,
                           className: 'btn btn-default exportar',
            			   exportOptions: { columns: [0,1,2,3,4,5] }
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


    $(".select-cliente-motoristas").on("change", function () {
            ajaxAtualizaTabelaMotoristaAjudante();
    });

    $(".filtros_ma").on("click",function(){
        if($(this).attr('id') == "mt_mot"){
            $("#flg_ma").val("motoristas");
            $(".filtros_ma").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else if($(this).attr('id') == "mt_aju"){
            $("#flg_ma").val("ajudantes");
            $(".filtros_ma").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else{
            $("#flg_ma").val("todos");
            $(".filtros_ma").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }
        ajaxAtualizaTabelaMotoristaAjudante();
    });

    $(".status_ma").on("click",function(){
        if($(this).attr('id') == "at_mt"){
            $("#flg_status_ma").val("ativo");
            $(".status_ma").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else if($(this).attr('id') == "in_mt"){
            $("#flg_status_ma").val("inativo");
            $(".status_ma").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else{
            $("#flg_status_ma").val("todos");
            $(".status_ma").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }
        ajaxAtualizaTabelaMotoristaAjudante();
    });

    $(document).on('click','.btstatus_ma', function(e){
        var thad = $(this);
        $.ajax({
            type: 'POST',
            url: ROOT+'/painel/cadastros/motoristas/status',
            data: {'mtcodigo': $(this).attr('id')},
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
                ajaxAtualizaTabelaMotoristaAjudante();
            }
        });
    });
});


$(document).ready(function(){
    $('#at_mt').trigger('click');
    $('.cliente-motorista').trigger('change');
});
