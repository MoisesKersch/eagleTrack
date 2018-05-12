$('.checkbox-jt').click(function() {

    if ($(this).is(":checked") && !$('.td-jt-'+$(this).data('val')).is(":checked") ) {
        $('.' + $(this).val()).addClass('ip-checked').attr('disabled', false);
        var iniPri = $('.' + $(this).val())
        var ipri = '';
        iniPri.each(function(i){
            if($(iniPri[i]).hasClass('ip-ini-pri')){
                ipri = $(iniPri[i]);
            }
        })
        var iniPriCheck = $('.ip-ini-pri.ip-checked');
        var fimPriCheck = $('.ip-fim-pri.ip-checked');
        var iniSegCheck = $('.ip-ini-seg.ip-checked');
        var fimSegCheck = $('.ip-fim-seg.ip-checked');
        var valorIniPri = '';
        var valorFImPri = '';
        var valorIniSeg = '';
        var valorFimSeg = '';

        for (var i = 0; i < iniPriCheck.length; i++) {
            if($(iniPriCheck[i]).val() != ''){
                if(valorIniPri == ''){
                    valorIniPri = $(iniPriCheck[i]).val();
                }
            }else{
                var iniPriEmpty = $(iniPriCheck[i]);
            }
            if($(fimPriCheck[i]).val() != ''){
                if(valorFImPri == ''){
                    valorFImPri = $(fimPriCheck[i]).val();
                }
            }else{
                var fimPriEmpty = $(fimPriCheck[i]);
            }
            if($(iniSegCheck[i]).val() != ''){
                if(valorIniSeg == ''){
                    valorIniSeg = $(iniSegCheck[i]).val();
                }
            }else{
                var iniSegEmpty = $(iniSegCheck[i]);
            }
            if($(fimSegCheck[i]).val() != ''){
                if(valorFimSeg == ''){
                    valorFimSeg = $(fimSegCheck[i]).val();
                }
            }else{
                var fimSegEmpty = $(fimSegCheck[i]);
            }
        }
        $(iniPriEmpty).val(valorIniPri)
        $(fimPriEmpty).val(valorFImPri)
        $(iniSegEmpty).val(valorIniSeg)
        $(fimSegEmpty).val(valorFimSeg)
        return;
    }else{
        $(this).prop('checked', false);
    }
    $('.' + $(this).val()).removeClass('ip-checked').val('').attr('disabled', true);

});

$('.rd-jt').click(function(){
    var val = $(this).data('val');
    $('.checkbox-jt-'+val).prop('checked',false);

    //bloquear campos
    $('.checkbox-jt-'+val).trigger('click');
});

$(document).ready(function() {

    $('.select-pontos-clientes').select2({
        "language": "pt-BR",
        allowClear: true,
        placeholder: "Selecione os pontos"});

    $('.input-time').each(function(x, y) {
        if ($(y).val() || $(y).parent().hasClass('has-error')) {
            var checkbox = $(y).parent().parent().find('.checkbox-jt');
            if (!checkbox.is(":checked")) {
                checkbox.click();
            }
        }
    });

    var formJornadaLivre = $('#formJornadaLivre');

    var check = $(formJornadaLivre).find('.chek-trabalha input');

    check.click(function(){
        var nun = $(this).data('dia');
        if($(this).is(':checked') && !$('.rd-dsr-'+nun).is(':checked')){
            $('.dia-'+nun).attr('disabled', false);
            var ipInter = $('.ip-intervalo-jornada');
            var ipJor = $('.ip-total-horas');
            var valores = completaCampos(ipInter, ipJor);
            $(".intervalo-jornada-"+nun).addClass('inter-habilidado').val(valores[0]);
            $(".total-horas-jornada-"+nun).addClass('horas-habilidado').val(valores[1]);
        }else{
            $('.dia-'+nun).attr('disabled', true);
            $(".intervalo-jornada-"+nun).removeClass('inter-habilidado').val('');
            $(".total-horas-jornada-"+nun).removeClass('horas-habilidado').val('');
            $(this).prop("checked",false);
        }

    })

    $('.ip-total-horas').blur(function(){
        var thad = $(this);
        $('.ip-total-horas').each(function(){
            if($(this).val() == '' && !$(this).attr('disabled')){
                $(this).val($(thad).val());
            }
        });

        // $(".horas-habilidado :input[type='']").val($(this).val())
    })

    $('.ip-intervalo-jornada').blur(function(){
        var thad = $(this);
        $('.ip-intervalo-jornada').each(function(){
            if($(this).val() == '' && !$(this).attr('disabled')){
                $(this).val($(thad).val());
            }
        });
        // $('.inter-habilidado').val($(this).val())
    })

    $('.rd-dsr').click(function(){
        var val = $(this).data('dia');
        $('.ck-jt-livre-'+val).prop('checked',false);
        $('.ck-jt-livre-'+val).trigger('click');
    });

    $('.ck-jt-livre ').click(function(){
        var val = $(this).data('dia');
        if($('.rd-dsr-'+val).is(':checked')){
            $(this).prop('checked', false);
        }
    });

    $('.ip-ini-pri').blur(function(){
        var iniPrival = $(this).val();
        var iniPriBlur = $('.ip-ini-pri.ip-checked');
        iniPriBlur.each(function(i){
            if($(iniPriBlur[i]).val() == ''){
                $(iniPriBlur[i]).val(iniPrival);
            }
        })
    })
    $('.ip-fim-pri').blur(function(){
        var fimPrival = $(this).val();
        var fimPriBlur = $('.ip-fim-pri.ip-checked');
        fimPriBlur.each(function(i){
            if($(fimPriBlur[i]).val() == ''){
                $(fimPriBlur[i]).val(fimPrival)
            }
        })
    })
    $('.ip-ini-seg').blur(function(){
        var iniSegval = $(this).val();
        var iniSegBlur = $('.ip-ini-seg.ip-checked');
        iniSegBlur.each(function(i){
            if($(iniSegBlur[i]).val() == ''){
                $(iniSegBlur[i]).val(iniSegval)
            }
        })
    })
    $('.ip-fim-seg').blur(function(){
        var fimSegval = $(this).val();
        var fimSegBlur = $('.ip-fim-seg.ip-checked');
        fimSegBlur.each(function(i){
            if($(fimSegBlur[i]).val() == ''){
                $(fimSegBlur[i]).val(fimSegval)
            }
        })
    })


    function completaCampos(ipInter, ipJor){
        for (var i = 0; i < ipInter.length; i++) {
            if($(ipInter[i]).val() != ''){
                var intervalo = $(ipInter[i]).val();
                break;
            }
        }
        for (var i = 0; i < ipJor.length; i++) {
            if($(ipJor[i]).val() != ''){
                var horas = $(ipJor[i]).val();
                break;
            }
        }
        return [intervalo, horas];
    }

    $('.tipo-jornada').change(function(){
        if($(this).val() == 'F'){
            $('#formJornadaFixa').removeClass('hidden');
            $("#formJornadaLivre").addClass('hidden');
        }else{
            $('#formJornadaLivre').removeClass('hidden');
            $("#formJornadaFixa").addClass('hidden');
        }
    })
    $('.tipo-jornada').trigger('change');

    var jtstatus = 'ativo';
    var dataSet = [];

    function ajaxAtualizaTabela(){
        $.ajax({
            type: "POST",
            url: ROOT+'/painel/cadastros/jornadaTrabalho/listarTable',
            data: {
                status:jtstatus,
                clientesbusca:$('.buscar-clientes-jt').val()
            },
            dataType: "json",
            'success': function (data) {

                var ppeditar = $("#ppeditar").data('permissao');
                var ppexcluir = $("#ppexcluir").data('permissao');

                for (var jt in data) {
                    var html = [];
                    html.push(data[jt].jtdescricao);
                    var htjHtml = '';
                    var tipo = data[jt].jttipo == 'F' ? 'Fixa' : 'Livre';
                    var hjtData = data[jt].horas_jornada_trabalho;
                    html.push(tipo);
                    html.push(data[jt].cliente_jornada.clnome);
                    var acoes = '';

                    if(ppexcluir){
                        if (data[jt].jtstatus == 'A') {
                            acoes +=
                            '<a title="Desativar Jornada de Trabalho" class="btDelModal btn btn-danger desativar-cadastros btn-tb" data-toggle="modal" data-target="#modalDelataDesativa" data-delete-action="'+ROOT+'/painel/cadastros/jornadaTrabalho/desativar/'+data[jt].jtcodigo+'">'
                                +'<span class="fa fa-ban"></span>'
                            +'</a>';
                        } else {
                            acoes +=
                            '<a title="Ativar Jornada de Trabalho" data-url="'+ROOT+'/painel/cadastros/jornadaTrabalho/ativar" data-id="'+data[jt].jtcodigo+'" class="btn btn-success ativar-cadastros btn-tb">'
                                +'<span class="fa fa-check"></span>'
                            +'</a>';
                        }
                    }
                    if(ppeditar){
                        acoes +=
                        '<a title="Editar Jornada de Trabalho" class="btn btn-info btn-tb" href="'+ROOT+'/painel/cadastros/jornadaTrabalho/editar/'+data[jt].jtcodigo+'">'
                            +'<span class="fa fa-pencil"></span>'
                        +'</a>';
                    }

                    html.push(acoes);
                    dataSet.push(html);
                }

                $('#table-jornada-trabalho_filter').remove();
                $('#table-jornada-trabalho_paginate').remove();
                $('#table-jornada-trabalho_info').remove();

                if ($.fn.DataTable.isDataTable('#table-jornada-trabalho')) {
                    $('#table-jornada-trabalho').DataTable().destroy();
                }

                $('#table-jornada-trabalho').DataTable({
                    paging: false,
                    retrieve: true,
                    "aoColumnDefs": [
                		{ 'bSortable': false, 'aTargets': [ 3 ] }
                	],
                	"language": traducao,
                    data: dataSet,
                    columns: [
                        { title: "Descrição" },
                        { title: "Tipo" },
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
                });
            }
        });
    }

    $(".buscar-clientes-jt").on("change", function () {
       ajaxAtualizaTabela();
    });

    $('.jt-status').click(function() {
        $('.btn-group-altera-status button').removeClass('btn-primary');
        $('.btn-group-altera-status button').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-primary');
        jtstatus = $(this).val();
        ajaxAtualizaTabela();
    });

    function diasSemana()
    {
        return [
            'Domingo',
            'Segunda',
            'Terça',
            'Quarta',
            'Quinta',
            'Sexta',
            'Sábado',
            'Feriado'
        ];
    }

    $('.rd-dsr:checked').trigger('click');
    $('.rd-jt:checked').trigger('click');

});
