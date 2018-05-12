var count = 0;
var count_scroll = 0;
var flag = true;
var scrollsPositions = [];

var teste_countapagar = true;

$(document).ready(function(){
    buscarRotas();
    $('#clientesAcompanhamento').trigger('change');
    setInterval(function(){
        $('#clientesAcompanhamento').trigger('change');
        pegaAtrazados();
    }, 2000); //20
    // }, 2000);

    var acomp = $('#acompanhamento');

    var ipData = $(acomp).find('#dataRota');

    $(ipData).datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        // startDate: "today",
        minDate: 0
    });

    function pegaAtrazados()
    {
        var itens = $(".panel-acompanhamento");
        itens.sort(function(a, b){
            return $(a).data("acom") - $(b).data("acom");
        })
        $('.list-acompanhamentos').html(itens)

        $(itens).each(function(i){
            if($(itens[i]).data('status') == 'P'){
                $('.list-acompanhamentos').append(itens[i])
            }
        })
    }

    function buscarRotas(){
        $('#clientesAcompanhamento').on('change',function(){
            $(".popover").popover('hide');

            if($('#dataRota').val() != '' && $('#clientesAcompanhamento').val() != ''){
                $.get(ROOT+'/painel/roteirizador/acompanhamento/buscar/rotas',
                {
                    'clientes': $('#clientesAcompanhamento').val(),
                    'busca': $('#buscaAcompanhamento').val(),
                    'data': $('#dataRota').val(),
                    'status': $('#status_acomp_rota').val()
                },function(data){
                    motarVisualizacaoRotas(data);
                });
            }else{
                restartList();
            }
        });
    }

    $('.flt-change-data').change(function(){
        restartList();
        $('#clientesAcompanhamento').trigger('change');
    });
});

$('.flt-change-busca').on('keyup',function(){
    var bval = $(this).val();
    var thad = $('.serch-acomp');
    if(bval.length > 2){
        $(thad).parents('.to-hidden').hide();
        $(thad).each(function(i){
            var valCampo = $(thad[i]).html().toUpperCase()
            if(valCampo.indexOf(bval.toUpperCase()) != -1) {
                $(thad[i]).parents('.to-hidden').show()
            }
        });
    }else{
        $(thad).parents('.to-hidden').show()
    }
});

function motarVisualizacaoRotas(rotas){
    var data = rotas;
    rotas = rotas.dados;
    for (var rota in rotas) {
        var contItens = rotas[rota].item.length;
        var contII = 0
        rota = rotas[rota];

        var orig_data_inicio;
        var orig_data_hora_inicio;
        var data_hora_inicio_previsto
        var data_hora_previsto = moment(rota['rodatahorainicio'], 'YYYY-MM-DD HH:mm');
        var data_hora_fim_rota = moment(rota['rodatahorafim'], 'YYYY-MM-DD HH:mm');
        //

        var data_hora_inincio_rota = orig_data_hora_inicio = moment(rota['rodatahorainicio'], 'YYYY-MM-DD HH:mm');
        var data_hora_fim_rota_previsto = moment(rota['rodatahorainicio'], 'YYYY-MM-DD HH:mm'); // para exibir o fim previsto da rota.. Esta variavel sera iniciada com o a data inicial da rota e incremamentada com o tempo de cada item + 15 minutos de tempo médio parado

        // var now = data_hora_inincio_rota.format('DD/MM/YYYY');
        // orig_data_inicio = now;
        // if(data_hora_inincio_rota.isValid()){
        //     var data_inicio = data_hora_inincio_rota.format('DD/MM/YYYY');
        //     var hora_inicio = data_hora_inincio_rota.format('HH:mm');
        // }else{
        //     var data_inicio = 'Não iniciado';
        //     var hora_inicio = 'Não iniciado';
        // }

        var kms_previsto = parseFloat(rota['rokm']/1000);

        var veic_margin = 0;
        var whidthScroll = 200 * (rota['item'].length + 1) ;

        if((rota['rohodometrofim'] != null) && (rota['rohodometroinicio'] != null)){
            km_rota_realizado = String(parseFloat(parseFloat(rota['rohodometrofim']) - parseFloat(rota['rohodometroinicio'])).toFixed(1)).replace('.',',');
        }else{
            km_rota_realizado = '0';
        }

        if(count < Object.size(rotas)){
            count = count + 1;

            var html = '';
            var mot = rota['mtnome'].length > 0 ? rota['mtnome'] : 'Sem Motorista'
            var aju = rota['mtajudante'].length > 0 ? rota['mtajudante'] : 'Sem Ajudante'

            html = `
                <div data-acom="0" data-status="`+rota['rostatus']+`" class="panel to-hidden panel-acompanhamento acomp-rota-`+rota['rocodigo']+` panel-default">
                    <div class="panel-heading cust-sm col-sm-12">
                        <span class="progresso rota-`+rota['rocodigo']+`"></span>
                            <a class="ic-inerface-acomp col-sm-1" data-toggle="collapse" aria-expanded="true" data-target=".to-collapse-`+rota['rocodigo']+`">
                                <span class="fa fa-chevron-down"></span>
                                <span title="`+rota['veprefixo']+`" class="title-placa">`+rota['roplaca']+` | `+rota['veprefixo']+`</span>
                            </a>
                            <div class="col-sm-8">
                                <span class="col-sm-4 panel-mot-aju">
                                    <span class='serch-acomp' >Motorista: `+mot+`</span>
                                    <span class='serch-acomp'> Ajudante: `+aju+`</span>
                                </span>
                                <span class="col-sm-3 panel-title-empresa serch-acomp">Realizado: <span class="realizados"></span></span>
                                <span class="col-sm-4 panel-dados-pontos">
                                    <span class="serch-acomp ultimo-ponto"></span>
                                    <span class="serch-acomp prox-ponto"></span>
                                </span>
                                `;
                                   // for(i in data['dados']){
                                        // var valor = data['dados'];
                                        // if(valor[0].rostatus != 'F'){
                                        //     //$('.btn-encerrar-rotas').addClass('hidden');
                                        //     console.log('aaa ' , valor[0].rostatus);
                                        // }
                                   // }
                                    if(rota['rostatus'] != "F"){
                                            html = html+`
                                        <button id="btn-encerrar-rotas-id" class="btn btn-danger btn-s col-sm-1 form-group btn-encerrar-rotas" data-id="`+rota['rocodigo']+`" title="Encerrar rota">
                                            <span id="imgfa-encerrar-rotas" class="fa fa-power-off"/>
                                        </button>`
                                    }

                                 html = html+`
                            </div>
                    </div>
                    <div class="panel-body body-acomp collapse in to-collapse-`+rota['rocodigo']+`">
                        <!-- Data de início -->
                        <div class="cust-sm primeiro">
                            <div class="cust-sm col-sm-12">
                                <span class="ic fa fa-sign-in"></span>
                                <div class="cust-sm">
                                    <span class="cust-sm col-sm-12 data-hora-inicio-rota-`+rota['rocodigo']+`"></span>
                                </div>
                            </div>
                        </div>
                        <div class="cust-sm altura col-sm-8">
                            <div class="acomp-margin acomp-margin-`+rota['rocodigo']+` nopadding  col-sm-12 scroll-y" data-rota="`+rota['rocodigo']+`">
                                <div class=" nopadding acomp-itens-line acomp-line-`+rota['rocodigo']+`" style="width: `+whidthScroll+`px;" >

                                </div>
                            </div>
                        </div>
                        <div class="cust-sm col-sm-2 final-linha">
                            <div class="cust-sm block-data-km-prev col-sm-12">
                                    <span class="ic fa fa-home"></span>
                                    <div class="data-km-prev">
                                        <span class="cust-sm p-data p-data-`+rota['rocodigo']+`"></span>
                                        <span class="cust-sm p-km-rota">Rota: `+String(kms_previsto.toFixed(1)).replace('.',',')+` Km</span>
                                    </div>
                            </div>

                            <div class="cust-sm col-sm-12 realisado-invisible realisado-invisible-`+rota['rocodigo']+` hidden">
                                <span class="ic fa fa-sign-out"></span>
                                <div class="data-km-fim">
                                    <span class="cust-sm r-data-fim">Data: `+ (data_hora_fim_rota.isValid() ? data_hora_fim_rota.format('DD/MM/YYYY - HH:mm') : 'Não iniciado') +`</span>
                                    <span class="cust-sm r-km-rota">Rota: `+ km_rota_realizado +`Km</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

            $(".list-acompanhamentos").append(html);
        }

        $(`.acomp-line-`+rota['rocodigo']).html('');

        if(rota['rostatus'] == 'C'){
            $('.panel-carregando-'+rota['rocodigo']).text('Carregando');
        }

        //Ícone do caminhao
        var ic_truck =
            `<div class="icon-truck truck-rota-`+rota['rocodigo']+`">
                <span class="fa fa-check invisible veic-status veic-status-`+rota['rocodigo']+`"></span>
                <span class="fa fa-truck fa-5 fa-truck-esquerda"></span>
            </div>`;

        $(`.acomp-line-`+rota['rocodigo']).append(ic_truck);


        var hora_item_previsto = 0;
        var data_hora_item = orig_data_hora_inicio;
        var veic_hodometro_partida = rota['item'][0]['irhodometro']; //o hodômetro de partida é o hodômetro do primeiro ítem da rota

        data_hora_inicio_previsto = data_hora_previsto;
        var soma = 0
        var posicaoUltimoItemAlterado = 0;
        for (var i in rota['item']) {
            itemRota = rota['item'][i];
            var color = '';
            var mColor = '';
            var mIcon = '';
            var obs = '';
            var posicao = 0;
            var obs_content = '';
            var status_time = 0; //0 para atrasado e 1 para adiantado

            //Setar hora de início caso já tenha iniciada
            var data_hora_inincio_rota = orig_data_hora_inicio = moment(rota['rodatahorainicio'], 'YYYY-MM-DD HH:mm');
            var data_hora_fim_rota_previsto = moment(rota['rodatahorainicio'], 'YYYY-MM-DD HH:mm'); // para exibir o fim previsto da rota.. Esta variavel sera iniciada com o a data inicial da rota e incremamentada com o tempo de cada item + 15 minutos de tempo médio parado

            var now = data_hora_inincio_rota.format('DD/MM/YYYY');
            orig_data_inicio = now;
            if(data_hora_inincio_rota.isValid()){
                var data_inicio = data_hora_inincio_rota.format('DD/MM/YYYY');
                var hora_inicio = data_hora_inincio_rota.format('HH:mm');
            }else{
                var data_inicio = 'Não iniciado';
                var hora_inicio = 'Não iniciado';
            }
            $('.data-hora-inicio-rota-'+rota['rocodigo']).html('Data: '+data_inicio+'-'+hora_inicio);

            // calcular se ele está atrasado ou adiantado do ponto
            var duration_item = itemRota['irtempoprevisto'];
            hora_item_previsto = (parseFloat(hora_item_previsto) + parseFloat(duration_item)).toFixed(2);
            data_hora_previsto = data_hora_item.add(hora_item_previsto, 'seconds');

            //alterar hora fim prevista da rota
            data_hora_fim_rota_previsto = data_hora_fim_rota_previsto.add(15, 'minutes'); //Adicionar 15 minutos para cada ponto no tempo previsto
            data_hora_fim_rota_previsto = data_hora_fim_rota_previsto.add(itemRota['irtempoprevisto'], 'seconds');

            if(rota['rodatahorainicio'] == null){
                $('.p-data-'+rota['rocodigo']).text('Data: Não iniciado');
            }
            if(data_hora_fim_rota_previsto.isValid()){
                $('.p-data-'+rota['rocodigo']).text('Data: '+data_hora_fim_rota_previsto.format('DD/MM/YYYY - HH:mm'));
            }

            var hora_entrega  = '';
            var now_hora_entrega = moment(itemRota['irdata_hora_evento'], 'YYYY-MM-DD HH:mm');

            var distancia = String((itemRota['irdistancia'] / 1000).toFixed(1)).replace('.',',');
            soma = parseFloat(parseFloat(String((itemRota['irdistancia'] / 1000).toFixed(1))) + parseFloat(soma)).toFixed(1);
            //cor barra superior collapse

            if(itemRota['irstatus'] != 'R') {
                if(itemRota['irstatus'] == 'F')
                    contII = contII + 1;
                posicao = i;
                posicaoUltimoItemAlterado++;
            }
            // Define a extrutura do ícone e o símbolo.
            if(itemRota["irstatus"] == "F"){  // checar o status e se esta atrasado, para definir a coloração
                obs_content = `
                    <div><span class='fa fa-clock-o'> </span> `+
                         moment().startOf('day').seconds(itemRota["irtempoparado"]).format('HH:mm')
                    +` h </div>`;
                obs_content += `<div><span class='fa fa-tachometer'> </span> `+distancia+` km </div>`;
                obs_content += `<div><span class='fa fa-cubes'> </span> `+itemRota['irqtde']+` vol. </div>`;
                color = " acomp-success ";
                mColor = " marker-up ";
                mBorder = "b-success";
                mIcon = ' fa-thumbs-o-up ';

                var hora_entrega = now_hora_entrega.isValid() ? now_hora_entrega.format('HH:mm') + 'h' : '';
                if( i < (rota['item'].length)){
                    veic_margin = i * 200 + 100 + 200;
                }
            }else if (itemRota["irstatus"] == "D") {
                obs = '';
                obs_content = `
                    <div><span class=' fa fa-clock-o'> </span> `+
                         moment().startOf('day').seconds(itemRota["irtempoparado"]).format('HH:mm')
                    +`h </div>`;
                color = " acomp-adiantado ";
                mColor = " marker-tack ";
                mBorder = "b-adiantado";
                mIcon = ' fa-thumb-tack ';
                if( i < (rota['item'].length)){
                    veic_margin = i * 200 + 180;
                }
            }else if (itemRota["irstatus"] == "R") {
                obs = ' (Prev.)';
                obs_content = `
                    <div><span class=' fa fa-clock-o'> </span> `+ (data_hora_previsto.isValid() ? data_hora_previsto.format('HH:mm') +'h' : 'Não Definido')
                    +`</div>`;
                obs_content += `<div><span class='fa fa-tachometer asdf'> </span> <span title='Distância do ponto anterior'>`+distancia+`</span>/<span title='Distância acumulada'>`+soma+`</span> km </div>`;
                color = " acomp-adiantado ";
                mColor = " marker-tack ";
                mBorder = "b-adiantado";
                mIcon = ' fa-thumb-tack ';
            }else if (itemRota["irstatus"] == "P") {
                obs_content = `<span class='fa fa-bullhorn'> O veículo não passou por este ponto </span>`;
                color = " acomp-atrasado ";
                mColor = " marker-down ";
                mBorder = "b-atrasado";
                mIcon = ' fa-thumbs-o-down ';

                if( i < (rota['item'].length)){
                    veic_margin = i * 200 + 100 + 200;
                }
            }

            var data_hora_item_previsto = data_hora_item;

            color = defineCorToLine(data_hora_item_previsto, now_hora_entrega);
            var tempo = color.tempo;
            var color = color.cor;

            if(itemRota["irstatus"] == "R"){
                color = '';
            }

            if(obs == ''){
                var nome_rota = itemRota["irnome"].substr(0, 12) +'..';
            }else{
                var nome_rota = itemRota["irnome"].substr(0, 7) +'..';
            }

            var line = `<div data-tempo="`+tempo+`" class="acomp-item acomp-item-`+i+'-'+rota['rocodigo']+` nomargin `+color+`" > </div>`;
            icon = '';
            var icon = `<div style="position:relative">
                    <div class="icons-maker `+mBorder+`"  data-html="true"
                        data-container="body"
                        data-toggle="popover"
                        data-placement="top"
                        data-animation="true"
                        data-title="
                            <div class='title' title='`+itemRota["irnome"] + obs+`'>
                                <div style='display: inline-block;'> `+nome_rota + obs+`</div>
                                <div style='display: inline-block; float: right;'>
                                    <a class='close-popover glyphicon glyphicon-remove'></a>
                                </div>
                            </div>" data-content="<div class='acomp-item-popover`+i+'-'+rota['rocodigo']+`'>`+obs_content+`</div>">
                        <span class="icon-ico fa `+mIcon+` "></span>
                        <span class="acomp-hora-entrega">`+hora_entrega +`</span>
                        <span title="`+itemRota["irnome"]+`" class="acomp-hora-nome">`+nome_rota + obs+`</span>
                    </div>
                </div>`;

            if(i >= (rota['item'].length - 1)){// se for ultima linha
                $('.acomp-line-'+rota['rocodigo']).append(line);
                $(`.acomp-item-`+i+'-'+rota['rocodigo']).append(icon);
                $('.acomp-line-'+rota['rocodigo']).append(line);
            }else{
                $('.acomp-line-'+rota['rocodigo']).append(line);
                $(`.acomp-item-`+i+'-'+rota['rocodigo']).append(icon);
            }
            if(tempo < 0) {
                var panelAcon = $('.acomp-line-'+rota['rocodigo']).parents('.panel-acompanhamento');
                $(panelAcon).data('acom', tempo);
            }else if(tempo > 0) {
                var panelAcon = $('.acomp-line-'+rota['rocodigo']).parents('.panel-acompanhamento');
                $(panelAcon).data('acom', tempo);
            }
        }

        var progs = (contII * 100) / contItens;
        if($('.rota-'+rota['rocodigo']).parents('.panel-acompanhamento').find('.acomp-item').hasClass('acomp-adiantado')) {
            var mProgrs = 'p-adiantado';
        }else if($('.rota-'+rota['rocodigo']).parents('.panel-acompanhamento').find('.acomp-item').hasClass('acomp-atrasado')) {
            var mProgrs = 'p-atrasado';
        }else if($('.rota-'+rota['rocodigo']).parents('.panel-acompanhamento').find('.acomp-item').hasClass('acomp-success')) {
            var mProgrs = 'p-success';
        }

        atual = 'Nenhum';
        prox = 'Nenhum';

        if(rota['rostatus'] == 'P') {
            atual = 'Nenhum';
            prox = rota['item'][posicao]['irnome'];
        }else if(typeof rota['item'][parseInt(posicao) + 1] != undefined) {
            posicaoUltimoItemAlterado == 0 ? atual = "Nenhum" : atual = rota['item'][posicaoUltimoItemAlterado-1]['irnome'];
            posicaoUltimoItemAlterado >= rota['item'].length ? prox = "Nenhum" : prox = rota['item'][posicaoUltimoItemAlterado]['irnome'];
        }else if(rota['rostatus'] == 'F') {
            atual = rota['item'][posicao]['irnome'];
            prox = 'Nenhum';
        }

        /* Remover rota quando atualizar e ela já estiver finalizada.
         Para isso vou adicionar uma classe aos elementos que foram atualizados, com isso os elementos que não foram atualizados serão eliminados por não terem esta classe
         a classe será removida de todos os íten ao buscar rotas, e adicionada no foreach que monta a linha de acompanhamento. */
        $('.acomp-rota-'+rota['rocodigo']).addClass('dont-remove-rota');

        $('.rota-'+rota['rocodigo']+'.progresso').addClass(mProgrs).css({'width': progs+'%'})
        $('.rota-'+rota['rocodigo']+'.progresso').parents('.cust-sm').find(".realizados").html(contII+'/'+contItens);

        $('.rota-'+rota['rocodigo']+'.progresso').parents('.cust-sm').find(".ultimo-ponto").html('Último: '+atual)
        if(typeof rota['item'][parseInt(posicao) + 1] != 'undefined')
            $('.rota-'+rota['rocodigo']+'.progresso').parents('.cust-sm').find(".prox-ponto").html('Próximo: '+prox)


        // Linha horizontal
        $('.acomp-itens-line').append('<span class="span-acomp-item"></span>');

        //mover scroll até veículo
        if(count_scroll < Object.size(rotas) && scrollsPositions[rota['rocodigo']] == undefined){
            count_scroll = count_scroll + 1;
            $(".acomp-line-"+rota['rocodigo']).parents('.scroll-y').animate({
                scrollLeft: veic_margin - 250
            }, 100);
        }

        //Esconder popover pois ainda não há uma forma de fixa-lo ao pai, paara um scroll.
        $('.scroll-y').on('scroll',function(event){
            $('.icons-maker').popover('hide');

            // criar um array com a posicao do scrol que foi alterado
            var rocodigo = $(this).data('rota');
            // console.log(event.target, $(this).scrollLeft(), rocodigo);
            scrollsPositions[rocodigo] =  $(this).scrollLeft();
        });

        //Move o scroll até sua posição.
        $('.scroll-y').each(function(){
            var rocodigo = $(this).data('rota');
            var position = scrollsPositions[rocodigo] != undefined? scrollsPositions[rocodigo] : 0;
            $(this).scrollLeft(position);
        })

        $('.truck-rota-'+rota['rocodigo']).css({'margin-left': veic_margin+'px'});

        if($('.acomp-margin-'+rota['rocodigo']).width() > whidthScroll){
            $('.acomp-line-'+rota['rocodigo']).css({'width': 100 +'%'});
        }

        if(rota['rostatus'] == 'F'){//Rota finalizada
            $('.realisado-invisible-'+rota['rocodigo']).removeClass('hidden');
            $('.veic-status-'+rota['rocodigo']).removeClass('invisible');
            $('.truck-rota-'+rota['rocodigo']).css({'margin-left': ($('.acomp-line-'+rota['rocodigo']).width() - 30)+'px'});
        }
    }

    $('.panel-acompanhamento').each(function(){
        if(!$(this).hasClass('dont-remove-rota')){
            $(this).remove();
        }else{
            $(this).removeClass('dont-remove-rota');
        }
    });

    //POPOVER --------------------------------------------------------------
    $('[data-toggle="popover"]').popover(
        {html:true}
    );

    $('[data-toggle="popover"]').on('shown.bs.popover', function () {
        $('.close-popover').on('click',function(){
            $(this).parents(".popover").popover('hide');
        });
    })

    // //Esconder popover pois ainda não há uma forma de fixa-lo ao pai, paara um scroll.
    // $('.scroll-y').on('scroll',function(event){
    //     $('.icons-maker').popover('hide');
    //
    //     // criar um array com a posicao do scrol que foi alterado
    //     var rocodigo = $(this).data('rota');
    //     // console.log(event.target, $(this).scrollLeft(), rocodigo);
    //     scrollsPositions[rocodigo] =  $(this).scrollLeft();
    // });
    //
    // //Move o scroll até sua posição.
    // $('.scroll-y').each(function(){
    //     var rocodigo = $(this).data('rota');
    //     var position = scrollsPositions[rocodigo] != undefined? scrollsPositions[rocodigo] : 0;
    //     $(this).scrollLeft(position);
    // })

    $(document).click(function(event) {
        if(!($(event.target).hasClass('ico-marker') || $(event.target).hasClass('icons-maker') || $(event.target).hasClass('icon-ico') ||
                $(event.target).hasClass('close-popover')  || $(event.target).parents('.popover').hasClass('in') )){
            try {
                $('.icons-maker').popover('hide');
                var thad = $('.icons-maker');
                $(thad).each(function(i){
                    $(thad[i]).data("bs.popover").inState.click = false;
                });
            } catch (e) {}
        }
    });

    var itensRota = new Array();
    //$(document).ready(function(){
        $('.btn-encerrar-rotas').click(function(){
            var mot = rota['rocodigo'];
            var rocodigo = $(this).data('id')
            var id = 0;
            var ircliente = 0;
            //ajax que retorna as justificativas do cliente
            $.ajax({
                    type: 'GET',
                    url: ROOT+'/painel/roteirizador/acompanhamento/ItensRotaNaoFinalizada/'+rocodigo,
                    dataType: 'json',
                    success : function(data){
                        var teste1 =  data['response'];

                        if(teste1.length > 0){
                            itensRota = teste1;
                            id = data['response'].length;
                            ircliente = teste1[0].ircliente;
                             $.ajax({
                                type: 'GET',
                                url: ROOT+'/painel/roteirizador/acompanhamento/getJustificativa/'+ircliente,
                                dataType: 'json',
                                success : function(data){
                                    var teste = data['response'];
                                        // htmlnovo= '<select name="selector" id="selector" data-id-item="" class="form-control select2">';
                                        htmlnovo= '';
                                        for(i in teste){
                                            htmlnovo+='<option value="'+teste[i].jucodigo+'">'+teste[i].judescricao+'</option>';
                                        }
                                        htmlnovo+='</select>'

                                         $('#modalClean .modal-title').html('<h3>Encerramento</h3>');
                                         $('#modalClean .modal-body').html('<h4>Alguns pontos não foram entregues, informe o motivo:</h4><p>');

                                        var i = 0;

                                        while(i < id){
                                            $('#modalClean .modal-body').append('<div class="bodyEncerramento"><h5> '+itensRota[i].irnome+' </h5></div>');
                                            $('#modalClean .modal-body').append('<div class="bodyEncerramento"><select name="selector" data-id-item="'+itensRota[i].ircodigo+'" class="selector form-control select2">'+
                                                htmlnovo+'</select></div> </p>');
                                            i++;
                                        }
                                          $('#modalClean .modal-body').append(`<div id="btn-encerramento" class="form-group block-salvar ">
                                                                            <button id="btnCancel" class="btn btn-lg btn-danger danger-eagle">
                                                                                Cancelar
                                                                                <span class="glyphicon glyphicon-remove"></span>
                                                                                </button>
                                                                            <button id="btnSave" type="submit" value="save" class="btn btn-lg btn-primary">
                                                                            Salvar
                                                                            <span class="glyphicon glyphicon-ok"></span>
                                                                            </button>
                                                                        </div>`);

                                        $('#btnCancel').on('click',function(){
                                            $('#modalClean').modal('hide');
                                        })

                                        $('#btnSave').on('click',function(){
                                             $('#modalClean').modal('hide');
                                            var cont = 0;
                                           var myColumnDefs = new Array();

                                            //aqui vai ser feito um ajax para salvar os dados
                                            $('.selector option:selected').each(function(){
                                                myColumnDefs.push({ircodigo : itensRota[cont].ircodigo, jucodigo : $(this).val()})
                                                cont++;
                                            })
                                            ajaxUpdateItensRota(myColumnDefs,/*rota['rocodigo']*/ rocodigo);
                                            //$('#modalClean').modal('cl')
                                           $('.flt-change-data').trigger('change');
                                        })

                                        $('#modalClean').modal('show');

                                        $('.selector').select2({width: '100%'});

                                        $('.selector').click(function(){
                                            var idItemRota  = $(this).data('id-item');
                                            var idJustificativa = $(this).val();
                                        })
                                },
                                error : function(data){

                                }
                            });
                        }else{
                            ajaxUpdateItensRota(null,rocodigo)
                            $('.flt-change-data').trigger('change')
                        }

                    },
                    error : function(data){

                    }
                });
        });
}

function ajaxUpdateItensRota(send,idcliente){
    $.ajax({
        type : 'POST',
        url : ROOT+'/painel/roteirizador/acompanhamento/updateItensRota',
        data : {'teste' : send, 'idcliente':idcliente},
        //dataType : 'json',
        success : function(data){
            if(data.status == 200){
                $('#modalClean').modal('hide');
                $('.')
            }
        },
        error : function(data1){
        }
    });
}

// define a cor para a linha, se estiver atrasado, adiantado ou dentro do periodo de tolerância
function defineCorToLine(data_hora_item_previsto, now_hora_entrega){
    var tolerancia = 10;
    var color = ' ';
    var diff = data_hora_item_previsto.diff(now_hora_entrega,'minutes');
    var tempo = 0;

    if(diff > 0){
        color = " acomp-adiantado ";
        tempo = diff
    }else{
        color = " acomp-atrasado ";
        if(!Number.isNaN(diff)) {
            tempo = diff;
        }
    }

    if(((diff + tolerancia) > 0 && (diff - tolerancia) < 0)){
        color = " acomp-success ";
    }

    return {'cor':color, 'tempo':tempo};
}


$(".fl_ro_st").on("click",function(){
    if($(this).attr('id') == "ini_ro"){
        $("#status_acomp_rota").val("I");
        $(".fl_ro_st").addClass("btn-default").removeClass("btn-primary");
        $(this).addClass("btn-primary").removeClass("btn-default");
    } else if($(this).attr('id') == "fin_ro"){
        $("#status_acomp_rota").val("F");
        $(".fl_ro_st").addClass("btn-default").removeClass("btn-primary");
        $(this).addClass("btn-primary").removeClass("btn-default");
    }else if($(this).attr('id') == "pend_ini_ro"){
        $("#status_acomp_rota").val("I,P");
        $(".fl_ro_st").addClass("btn-default").removeClass("btn-primary");
        $(this).addClass("btn-primary").removeClass("btn-default");
    }else{
        $("#status_acomp_rota").val("I,P,F,C");
        $(".fl_ro_st").addClass("btn-default").removeClass("btn-primary");
        $(this).addClass("btn-primary").removeClass("btn-default");
    }
    restartList();
    $('#clientesAcompanhamento').trigger('change');
});


function restartList(){
    count = 0;
    count_scroll = 0;
    flag = true;
    $(".list-acompanhamentos").html('');
}

$(document).on('click','.ic-inerface-acomp',function(){
    if($(this).find('span.fa').hasClass('fa-chevron-down')){
        $(this).find('span.fa').removeClass('fa-chevron-down');
        $(this).find('span.fa').addClass('fa-chevron-right');
    }else if($(this).find('span.fa').hasClass('fa-chevron-right')){
        $(this).find('span.fa').removeClass('fa-chevron-right');
        $(this).find('span.fa').addClass('fa-chevron-down');
    }
});

$(".bt-justificativa").click(function(e){
    e.preventDefault()
    var clientes = $('#clientesAcompanhamento').val();

    var modal = $("#modalClean");
    $(modal).find('.modal-title').html('Justificativas');
    var body = '<form id="formJust" class="form" action="'+ROOT+'/painel/cadastros/justificativas" type="post">'
        body += '<span><strong>Cadastre as justificativas que podem ser utilizadas para Entregas, Coletas e Encerramento:</strong></span>';
        body += '<div class="mt-just">';
        body += '<label>Descrição*</label>';
        body += '<input type="text" name="judescricao" id="judescricao" class="form-control">';
        body += '<input type="hidden" name="jucliente" value="'+clientes+'">'
        body += '</div>';
        body += '<div class="bts-acao">'
        body += '<a href="#" class="btn btn-info hidden bt-save btn-lg">Salvar</a>'
        body += '<a href="#" data-dismiss="modal" class="btn btn-danger btn-lg">Cancelar</a>'
        body += '</div>'
        body += '</form>'
    $(modal).find('.modal-body').html(body)
    $(modal).modal('show');


    $("#judescricao").keyup(function(){
        var val = $(this).val();
        if(val.length > 1){
            $('.bt-save').removeClass('hidden');
        }else{
            $('.bt-save').addClass('hidden');
        }
    })

    $(".bt-save").click(function(e){
        e.preventDefault()
        $('#formJust').ajaxForm({
            type:'post',
            success: function(dados) {
                if(dados.codigo == '200'){
                    $(".message").remove()
                    modal.find('.bts-acao').prepend('<span class="message text-success">'+dados.message+'</span>')
                    window.setTimeout(function() {
                    $(modal).modal('hide');
                    }, 2000);
                }else if(dados.codigo == '500'){
                    $(".message").remove()
                    modal.find('.bts-acao').prepend('<span class="message text-danger">'+dados.message+'</span>')
                }
            }
        }).submit();
    })
})

$("#clientesAcompanhamento").change(function(){
    var thad = $(this).val();
    if(thad.length == 0) {
        $(".bt-justificativa").addClass('hidden');
    }else{
        $(".bt-justificativa").removeClass('hidden');
    }
})

$("#modalClean").on('hidden.bs.modal', function(){
    limpaModal()
})

function limpaModal(){
    $('.modal .modal-title').html('')
    $('.modal .modal-body').html('')
    $('.modal .modal-footer').html('')
}
