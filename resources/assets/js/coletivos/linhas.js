// Variáveis
// var cont_pontos_selecionados = 0;
var pontos_selecionados = [];
var pontosIc = [];
var regioes = new Array();
var polyLinha = [];
var lidistancia = 0;
var liqtdpontos = 0;
var litempoestimado = 0;
var dadosSelect2 = null;

// listagem
$(document).ready(function(){

    $('#selectCliListLinhas').change(function(){
        $.ajax({
            url: ROOT+'/painel/coletivos/cadastros/linhas/listagem',
            data: {'clientes' : $(this).val()},
            type: "post",
            success: function(data){
                var linhas = data.linhas;
                if(linhas.length > 0){
                    var ppeditar = $("#ppeditar").data('permissao');
                    var ppexcluir = $("#ppexcluir").data('permissao');

                    var dataSet = [];
                    for (var i in linhas) { var linha = linhas[i];
                        tr = [];
                        tr.push(linha.lidescricao);
                        tr.push(linha.cliente.clfantasia);
                        var bt = '';
                        if(ppeditar){
                            bt  +='<a class="btn btn-tb btn-info" href="'+ROOT+'/painel/coletivos/cadastros/linhas/editar/'+linha.licodigo+'"><i class="fa fa-pencil"></i></a>'
                        }
                        if(ppexcluir){
                            bt +='<a class="btn btn-tb btn-danger excluir-linha" data-id="'+linha.licodigo+'"><i class="glyphicon glyphicon-minus"></i></a>'
                        }
                        tr.push(bt);

                        dataSet.push(tr);
                    }

                    if($.fn.DataTable.isDataTable('#feriadosTable')) {
                        $('#feriadosTable').DataTable().destroy();
                    }
                    table = $('#tb_linhas').DataTable({
                        paging: false,
                        retrieve: true,
                        language: traducao,
                        dom: 'Bfrtip',
                        data: dataSet,
                        buttons:
                            [{
                                extend: 'pdf',
                                className: 'btn btn-lg btn-default exportar',
                                exportOptions: { columns: [0,1] },
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
                               exportOptions: { columns: [0,1] }
                           },{
                               extend: 'csv',
                               footer: false,
                               className: 'btn btn-lg btn-default exportar',
                               exportOptions: { columns: [0,1] }
                           },{
                               extend: 'print',
                               text: 'Imprimir',
                               footer: false,
                               className: 'btn btn-lg btn-default exportar',
                               exportOptions: { columns: [0,1] }
                           }],
                        initComplete: function () {
                            $('.dt-buttons').prepend('<span class="label-botoes-table">Exportar para: </span>');
                            $('.exportar').removeClass("dt-button buttons-pdf buttons-csv buttons-excel buttons-html5");
                            $('.exportar').prepend("<span class='fa fa-save'></span>");
                        }
                    })
                    dataSet = [];
                }
            }
        });
    })
    $('#selectCliListLinhas').trigger('change');
})

$(document).on('click', '.excluir-linha', function(e){
    e.preventDefault()
    var modal = $('#modalDeleta');
    var body = $('#modalDeleta').find('.modal-body');
    var bt = $('#modalDeleta').find('#btnDelModal');
    var footer = $("#modalDeleta").find('.modal-footer');
    var id = $(this).data('id');

    $(body).prepend('Ao remover essa linha não será mais possível recuperá-lo <br />');
    var bts = '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>'
    bts += '<a id="btnDelModal" href="'+ROOT+'/painel/coletivos/cadastros/linhas/excluir/'+id+'" class="btn btn-primary">Salvar alterações</a>'
    $(footer).html(bts);
    $(modal).modal('show');
})

// edicao
$(document).ready(function(){
    if($('#licodigo').val() != undefined && $('#licodigo').val() != ''){
        $.ajax({
            url: ROOT+'/painel/coletivos/cadastros/linhas/dados',
            type:'post',
            data:{'licodigo' : $('#licodigo').val()},
            success: function(dados){
                // pontos_selecionados = dados.pontos;

                // montar table horários
                showHorarios(dados.horarios);

                //mostrar pontos selecionados no mapa;
                for (var i in dados.podados) { var podado = dados.podados[i];
                    // cont_pontos_selecionados++;
                    pontos_selecionados.push(parseInt(podado.pocodigo));
                    pontosIc[podado.pocodigo] = createMarker(podado.pocodigo, podado.podescricao, podado.potipo , podado.polatitude, podado.polongitude);
                    addTableOnEdit(podado);
                }
                roteirizar();
            }
        });
    }
});

function showHorarios(horarios){
    var diasSemana = ["Domingo", "Segunda feira", "Terça feira", "Quarta feira", "Quinta feira", "Sexta feira", "Sábado"];
    // montar table horários
    for (var i in horarios) { var horario = horarios[i];
        var tr = '';
        tr = `<tr>
                <td class="hr-horario" >${ horario.hrhorario.substring(0,(horario.hrhorario.length - 3))  }</td>
                <td class="hr-dia-semana" data-id='${horario.hrdiasemana}'>${diasSemana[horario.hrdiasemana]}</td>
                <td class="td-span-remove"> <span class='fa fa-times bt-remover-horario' title="Remover horário da linha"></span> </td>
            </tr>`;
        $('.table-horarios tbody').prepend(tr);
    }
}

function changeCliente(){
    for (var i in pontosIc) {
        mapa.removeLayer(pontosIc[i]);
    }
    if (polyLinha != []) {
        for (var i in polyLinha) {
            mapa.removeLayer(polyLinha[i]);
        }
    }
    pontosIc = [];

    // cont_pontos_selecionados = 0;
    pontos_selecionados = [];

    lidistancia = 0;
    liqtdpontos = 0;
    litempoestimado = 0;

    $('.tb-pontos tbody').html('');
    $('.table-horarios tbody').html('');

     resetTotaizadores();
    // limpar tabela pontos, limpar tabela pontos, limpar tabela de horários e limpar rota
}

function resetTotaizadores(){
    $('.dst-total').html('0km');
    $('.qtd-pontos').html('0');
    $('.tempo-estimado').html('00:00h');
}

// Cadastro
$(document).ready(function(){
    $('#seguirOrdemInsercaoPontos').click(function(){
        roteirizar();
    })

    carregaMapaLinhas();
    $('#selectCliCadLinhas').on('change',function(){

        // limpar tudo, array de selecionados,
        // array de icones, remover markers selecionados e
        // reinicializar o contador de pontos selecionados

        changeCliente();

        $.ajax({
            url: ROOT+'/painel/coletivos/cadastros/linhas/pontos/todos',
            type:'post',
            data:{'cliente' : $('#selectCliCadLinhas').val()},
            success: function(dados){
                dadosSelect2 = dados;
                mountSelect2Linhas(dadosSelect2);
            },
            beforeSend: function() {
                disableBuscaPonto();
                $('#loader').css('display', 'block');
            },
            complete: function() {
                enableBuscaPonto();
                $('#loader').css('display', 'none');
            }
        });
    });

    $('#selectCliCadLinhas, #liPoReferencia, #liPoColeta, #liPoEntrega').change(function(){
        var potipos = [];
        if($('#liPoReferencia').is(':checked')){
            potipos.push('P');
        }
        if($('#liPoColeta').is(':checked')){
            potipos.push('C');
        }
        if($('#liPoEntrega').is(':checked')){
            potipos.push('E');
        }

        removePontosMarkers(pontosIc);
        if(potipos.length > 0){
            $.ajax({
                url: ROOT+'/painel/coletivos/cadastros/linhas/pontos',
                type:'post',
                data:{'cliente' : $('#selectCliCadLinhas').val(), 'tipos': potipos, 'pontos_selecionados': pontos_selecionados},
                success: function(dados){
                    // cont_pontos_selecionados = 0;

                    var pontos = dados.pontos;
                    for (var i in pontos) {
                        var ponto = pontos[i];

                        var ic_name = "";
                        if(ponto.potipo == "C"){
                            ic_name = "coleta"
                        }else if (ponto.potipo == "E") {
                            ic_name = "entrega"
                        }else if(ponto.potipo == "P"){
                            ponto.potipo = "R"
                            ic_name = "referencia"
                        }

                        var icon = L.divIcon({
                            className: 'fa fa-map-marker ponto-'+ic_name+' ponto-'+ponto.pocodigo,
                            html: '<span data-potipo="'+ponto.potipo+'" data-pocodigo="'+ponto.pocodigo+'" class="circle  circle-'+ic_name+'">'+ponto.potipo+'</span>',
                            iconAnchor: [16, 30],
                            popupAnchor: [-3, -25]
                        });
                        var marker = L.marker([ponto.polatitude, ponto.polongitude], {icon: icon})
                            .bindPopup("<b>"+ponto.podescricao+"</b><br><a data-polat=\""+ponto.polatitude+"\" data-polong=\""+ponto.polongitude+"\" data-potipo=\""+ponto.potipo+"\" data-pocodigo=\""+ponto.pocodigo+"\" data-podescricao=\""+ponto.podescricao+"\" class='bt-adicionar-ponto btn btn-sm  btn-primary'> Adicionar </a>");

                        marker.addTo(mapa);
                        pontosIc[ponto.pocodigo] = marker;
                    }
                },
                beforeSend: function() {
                    disableBuscaPonto();
                    $('#loader').css('display', 'block');
                },
                complete: function() {
                    enableBuscaPonto();
                    $('#loader').css('display', 'none');
                }
            });
        }
    });

    $('#selectCliCadLinhas').trigger('change');

    $('#liRegiao').change(function(){
        removeRegioesMapa(regioes);
        if($(this).is(':checked')){
            $.ajax({
                url: ROOT+'/painel/coletivos/cadastros/linhas/regioes',
                type:'post',
                data:{'cliente' : $('#selectCliCadLinhas').val()},
                success: function(dados){
                    var regioess = dados.regioes;
                    for (var i in regioess) {
                        var regiao = regioess[i];
                        var arrayPosicoes = new Array();

                        for (var j in regiao.regioes_coordenadas){
                            var coordenada = regiao.regioes_coordenadas[j]
                            arrayPosicoes.push([coordenada.rclatitude, coordenada.rclongitude])
                        }
                        regioes.push(L.polygon(arrayPosicoes,{color: regiao.recor, name: regiao.redescricao}).bindPopup(regiao.redescricao).addTo(mapa));
                    }
                },
                beforeSend: function() {
                    $('#loader').css('display', 'block');
                    disableBuscaPonto();
                },
                complete: function() {
                    enableBuscaPonto()
                    $('#loader').css('display', 'none');
                }
            });
        }
    });

});

function disableBuscaPonto(){
    $('.search-tb-pontos').prop('disabled', true);
}

function enableBuscaPonto(){
    $('.search-tb-pontos').prop("disabled", false);
}

$(document).on('change', '.search-tb-pontos', function(){
    var pocodigo = $('.search-tb-pontos').val();
    if(!pontos_selecionados.includes(parseInt(pocodigo)) && parseInt(pocodigo) != 0){
        var ponto = null;
        // alterar popup
        var podescricao = $('.search-tb-pontos option:selected').text();
        var potipo = $('.search-tb-pontos option:selected').data('potipo');
        var polat = $('.search-tb-pontos option:selected').data('polat');
        var polong = $('.search-tb-pontos option:selected').data('polong');

        pontos_selecionados.push(parseInt(pocodigo));
        addPontoTable(pocodigo,podescricao,potipo,polat,polong);

        if(pontosIc[pocodigo] != undefined){
            var marker  = pontosIc[pocodigo];
            marker.bindPopup("<b>"+podescricao+"</b><br><a data-polatitude='"+polat+"' data-polong='"+polong+"' data-potipo=\""+potipo+"\" data-pocodigo=\""+pocodigo+"\" data-podescricao=\""+podescricao+"\"  class='bt-remover-ponto bt-remover-ponto-"+pocodigo+" btn btn-sm  btn-danger'> Remover </a>");
        }else{
            pontosIc[pocodigo] = createMarker(pocodigo, podescricao, potipo, polat, polong);
        }

        roteirizar();
        setTotalizadores(null);

        mountSelect2Linhas(dadosSelect2);
    }

    // remontar select2

});

function mountSelect2Linhas(dados){
    if(dados != null){
        var html = `<option value="0" >Buscar Pontos</option>`;
        for (var i in dados.pontos) {
            var ponto = dados.pontos[i];
            if(!pontos_selecionados.includes(parseInt(ponto.pocodigo))){
                html = html+`<option data-polat="${ponto.polatitude}" data-polong="${ponto.polongitude}" data-potipo="${ponto.potipo}" value="${ponto.pocodigo}" >${ponto.podescricao}</option>`
            }
        }
        $('.search-tb-pontos').html(html);
        $('.search-tb-pontos').select2();
    }
}


$(document).on('click','.bt-adicionar-ponto',function(){
    mapa.closePopup();

    // alterar popup
    var pocodigo = $(this).data('pocodigo');
    var podescricao = $(this).data('podescricao');
    var potipo = $(this).data('potipo');
    var polat = $(this).data('polat');
    var polong = $(this).data('polong');

    pontos_selecionados.push(pocodigo);
    addPontoTable(pocodigo,podescricao,potipo,polat,polong);

    if(pontosIc[pocodigo] != undefined){
        var marker  = pontosIc[pocodigo];
        marker.bindPopup("<b>"+podescricao+"</b><br><a data-polatitude='"+polat+"' data-polong='"+polong+"' data-potipo=\""+potipo+"\" data-pocodigo=\""+pocodigo+"\" data-podescricao=\""+podescricao+"\"  class='bt-remover-ponto bt-remover-ponto-"+pocodigo+" btn btn-sm  btn-danger'> Remover </a>");
    }else{
        pontosIc[pocodigo] = createMarker(pocodigo, podescricao, potipo, polat, polong);
    }

    roteirizar();
    setTotalizadores(null);
    mountSelect2Linhas(dadosSelect2);
});

$(document).on('click','.bt-remover-ponto',function(){
    mapa.closePopup();
    $('#modalClean').modal('hide');
    removeFromTable($(this));
    var pocodigo = $(this).data('pocodigo');
    var podescricao = $(this).data('podescricao');
    var potipo = $(this).data('potipo');
    var marker  = pontosIc[pocodigo];
    marker.bindPopup("<b>"+podescricao+"</b><br><a data-polat='"+marker.getLatLng().lat+"' data-polong='"+marker.getLatLng().lng+"' data-potipo=\""+potipo+"\" data-pocodigo=\""+pocodigo+"\" data-podescricao=\""+podescricao+"\" class='autofocus bt-adicionar-ponto btn btn-sm  btn-primary'> Adicionar </a>");

    // remover do array de selecionados..
    roteirizar();
    mountSelect2Linhas(dadosSelect2);
});

function roteirizar(){
    var seguirOrdem = $('#seguirOrdemInsercaoPontos').is(':checked')
    //ajax para roteirizar pontos;
    if(polyLinha != []){
        for (var i in polyLinha) {
            mapa.removeLayer(polyLinha[i]);
        }
    }
    if(pontos_selecionados.length > 1){
        $.ajax({
            url: ROOT+'/painel/coletivos/cadastros/linhas/rota',
            type:'post',
            data:{'cliente' : $('#selectCliCadLinhas').val(), 'seguirOrdem' : true /*seguirOrdem */, 'pontosSelecionados' : pontos_selecionados},
            success: function(dados){
                if(dados.rota.trips != undefined){
                    // TODO foi desabilitada a funcao de criar linha por trip;

                    // var legs = dados.rota.trips[0].legs;
                    // var decode = decodePoly.decode(dados.rota.trips[0].geometry);
                    // polyLinha = L.polyline.antPath(decode,{delay: 2500,weight: 7})
                    // polyLinha.addTo(mapa);
                    // //ReordenarArray
                    // reorderPontosSelecionados(dados.rota.waypoints);
                    // setDuracaoRota(legs);
                    // setTotalizadores(dados.rota.trips[0]);
                    //
                    // $('.scoll-ponto').animate({
                    //     scrollTop: $('.tb-pontos').height()+200
                    // }, 1);

                }else if(dados.rota[0].routes != undefined){
                    for (var i in dados.rota) {
                        var legs = dados.rota[i].routes[0].legs;
                        var decode = decodePoly.decode(dados.rota[i].routes[0].geometry)
                        polyLinha[i] = L.polyline.antPath(decode,{delay: 2500,weight: 7})
                        polyLinha[i].addTo(mapa);
                        setDuracaoRota(legs);
                        setTotalizadores(dados.rota[i].routes[0]);

                    }

                    $('.scoll-ponto').animate({
                        scrollTop: $('.tb-pontos').height()+200
                    }, 1);
                }
            },
            beforeSend: function() {
                disableBuscaPonto();
                $('#loader').css('display', 'block');
            },
            complete: function() {
                enableBuscaPonto();
                $('#loader').css('display', 'none');
            }
        })
    }else{
        $('.tempo-previsto').html('');
        resetTotaizadores();
        setTotalizadores(null);
    }
}

function setDuracaoRota(legs){
    var soma = 0.0;
    for(i in legs){
        soma = parseFloat(soma) + parseFloat(legs[i].duration);
        $('.tempo-previsto-'+pontos_selecionados[parseInt(i)+1]).html(moment.utc(soma*1000).format('HH:mm'));
        if(i == 0){
            $('.tempo-previsto-'+pontos_selecionados[parseInt(i)]).html('');
        }
    }
}

function reorderPontosSelecionados(waipts){
    var new_pontos_selecionados = [];
    for(var i in waipts){
        var waipt = waipts[i].waypoint_index;
        new_pontos_selecionados[waipt] = parseInt(pontos_selecionados[i]);
    }
    pontos_selecionados = new_pontos_selecionados;

    for(var i in new_pontos_selecionados){
        $('.ponto-'+new_pontos_selecionados[i]).find('span').html(parseInt(i)+1);
        $('.ponto-tabela-'+new_pontos_selecionados[i]).find('.ponto-ordem').html(parseInt(i)+1);
    }
    $('.click-order-ordem').trigger('click');
}

function setTotalizadores(route){
    if(route != null){
        lidistancia = route.distance;
        liqtdpontos = route.legs.length + 1;
        litempoestimado = route.duration;


        $('.dst-total').html((lidistancia/1000).toFixed(2) +'km');
        $('.qtd-pontos').html(liqtdpontos);
        $('.tempo-estimado').html(moment.utc(litempoestimado*1000).format('HH:mm') +'h');
    }else{
        $('.qtd-pontos').html(pontos_selecionados.length);
    }
}

function createMarker(pocodigo, podescricao, potipo, polatitude, polongitude){

    var ic_name = "";
    var icon = L.divIcon({
        className: 'poSelecionado fa fa-map-marker ponto-'+ic_name+' ponto-'+pocodigo,
        html: '<span data-potipo="'+potipo+'" data-pocodigo="'+pocodigo+'" class="circle  circle-'+ic_name+'">'+parseInt(pontos_selecionados.length)+'</span>',
        iconAnchor: [16, 30],
        popupAnchor: [-3, -25]
    });
    var marker = L.marker([polatitude, polongitude], {icon: icon})
        .bindPopup(`<b> ${podescricao}</b><br><a data-potipo="${potipo}" data-pocodigo="${pocodigo}" data-podescricao="${podescricao}" class='bt-remover-ponto bt-remover-ponto-${pocodigo} btn btn-sm  btn-danger'> Remover </a>`);
    marker.addTo(mapa);

    $('.ponto-'+pocodigo).css('color','purple');
    $('.ponto-'+pocodigo).find('span').css('border','2px solid  purple');

    return marker;
}

function removePontosMarkers(pontosIc){
     for (var pocodigo in pontosIc){
         if(!pontos_selecionados.includes(parseInt(pocodigo))){
             mapa.removeLayer(pontosIc[pocodigo]);
         }
     }
}

function removeRegioesMapa(regioes){
    regioes.forEach(function(regiao){
         mapa.removeLayer(regiao)
    });
}

function carregaMapaLinhas(){
    position = [-27.099203, -52.626327];
    var attribution = '&copy;<a href="http://maps.google.com">Google Maps</a>';

    var googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {attribution: attribution, maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3']}),
        satelliteGoogle = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {attribution: attribution, maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3']}),
        detalhado = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {attribution: attribution});

    var baseLayers = {
          "Padrão": googleStreets,
          "Satélite": satelliteGoogle,
          "Detalhado": detalhado
    };

    mapa = L.map('mapaLinhas', {
          center: position,
          zoom: 13,
          layers: [googleStreets]
      });
    mapa.zoomControl.setPosition("bottomright");
    L.control.layers(baseLayers,null,{
        position: 'bottomright'
    }).addTo(mapa);
}

function addTableOnEdit(podado){
    var linha = '';
    var ic_name = "";
    var potipo = podado.potipo;
    if(potipo == "C"){
        ic_name = "circle_coleta"
    }else if (potipo == "E") {
        ic_name = "circle_entrega"
    }else if(potipo == "P" || potipo == "R"){
        ic_name = "circle_referencia"
    }

    linha += `<tr class="ponto-tabela-linha ponto-tabela-`+podado.pocodigo+`">
        <td class="ponto-ordem pequena-width" >${parseInt(pontos_selecionados.length )}</td>
        <td class="pequena-width" ><img class="img-table" src="`+ROOT+`/img/${ic_name}.png" /></td>
        <td class="pequena-width" ><span style="cursor:crosshair;" class="glyphicon glyphicon-screenshot localizar-veiculo" onclick="mapa.flyTo([${podado.polatitude},${podado.polongitude}], 20);" /></td>
        <td><spam class="texto-linha-span-`+podado.pocodigo+`" title="${podado.podescricao}" ></spam></td>
        <td class="pequena-width tempo-previsto tempo-previsto-${podado.pocodigo}" ></td>
        <td class="pequena-width" ><span class="fa fa-times bt-remover-ponto " data-polat="${podado.polatitude}" data-polong="${podado.polongitude}" data-potipo="${potipo}"  data-pocodigo="${podado.pocodigo}" data-podescricao="${podado.podescricao}"  ></span></td>
    </tr>`

    $('.tb-pontos tbody').append(linha);

    var width = $(`.texto-linha-span-`+podado.pocodigo).parent().width();
    var podescricao = podado.podescricao.truncar(parseInt(width) / 10);//10 é um valor médio de tamanho de letra
    $(`.texto-linha-span-`+podado.pocodigo).html(podescricao);

}

function addPontoTable(pocodigo,podescricao,potipo,polat,polong){
        var linha = '';
        var ic_name = "";
        if(potipo == "C"){
            ic_name = "circle_coleta"
        }else if (potipo == "E") {
            ic_name = "circle_entrega"
        }else if(potipo == "P" || potipo == "R"){
            ic_name = "circle_referencia"
        }

        // cont_pontos_selecionados++;
        $('.ponto-'+pocodigo).find('span').html(parseInt(pontos_selecionados.length ));
        $('.ponto-'+pocodigo).addClass('poSelecionado');
        $('.ponto-'+pocodigo).css('color','purple');
        $('.ponto-'+pocodigo).find('span').css('border','2px solid  purple');


        linha += `<tr class="ponto-tabela-linha ponto-tabela-`+pocodigo+`">
            <td class="ponto-ordem pequena-width" >${parseInt(pontos_selecionados.length )}</td>
            <td class="pequena-width" ><img class="img-table" src="`+ROOT+`/img/${ic_name}.png" /></td>
            <td class="pequena-width" ><span style="cursor:crosshair;" class="glyphicon glyphicon-screenshot localizar-veiculo" onclick="mapa.flyTo([${polat},${polong}], 20);" /></td>
            <td><spam class="texto-linha-span-`+pocodigo+`" title="${podescricao}" ></spam></td>
            <td class="pequena-width tempo-previsto tempo-previsto-${pocodigo}" ></td>
            <td class="pequena-width" ><span class="fa fa-times bt-remover-ponto " data-polat="${polat}" data-polong="${polong}" data-potipo="${potipo}"  data-pocodigo="${pocodigo}" data-podescricao="${podescricao}"  ></span></td>
        </tr>`

        $('.tb-pontos tbody').append(linha);

        var width = $(`.texto-linha-span-`+pocodigo).parent().width();
        var podescricao = podescricao.truncar(parseInt(width) / 10);//10 é um valor médio de tamanho de letra
        $(`.texto-linha-span-`+pocodigo).html(podescricao);

        //move Scroll
}

function removeFromTable(thad){
    foreachs(thad);
    descelecionaPonto(thad);
    $('.ponto-'+$(thad).data('pocodigo')).removeClass('poSelecionado');
}

function foreachs(thad){

    var this_ordem = $('.ponto-'+$(thad).data('pocodigo')).find('span').html();

    $('.poSelecionado').each(function(){
        var ordem = $(this).find('span').html();
        if(parseInt(ordem) > parseInt(this_ordem)){
            $(this).find('span').html(ordem-1);
        }
    })

    $('.ponto-tabela-linha').each(function(){
        var ordem = $(this).find('.ponto-ordem').html();
        if(parseInt(ordem) > parseInt(this_ordem)){
            $(this).find('.ponto-ordem').html(ordem-1);
        }
    })
}

function descelecionaPonto(thad){
    // cont_pontos_selecionados--;
    var potipo = $(thad).data('potipo');
    var pocodigo = $(thad).data('pocodigo');
    var cor = "";
    if(potipo == "C"){
        cor = '#ff0005'
    }else if (potipo == "E") {
        cor = '#408700'
    }else if(potipo == "P" || potipo == "R"){
        potipo = 'R';
        cor = '#0684b5'
    }

    $('.ponto-'+pocodigo).find('span').html(potipo);
    $('.ponto-'+pocodigo).css('color',cor);
    $('.ponto-'+pocodigo).find('span').css('border','2px solid '+cor);
    $('.ponto-'+pocodigo).find('span').css('background-color','white');
    $('.ponto-tabela-'+pocodigo).remove();
    pontos_selecionados.splice(pontos_selecionados.indexOf(pocodigo),1);

};

function removerPorPocodigo(array, id) {
  var result = array.filter(function(el) {
    return el.id == id;
  });

  for(var elemento of result){
    var index = array.indexOf(elemento);
    array.splice(index, 1);
  }
}

// *****************************************************************************
// ******************* Método para ordenar a tabela ****************************
// *****************************************************************************

$('.click-order-ordem').click(function(){
    var table = $(this).parents('table').eq(0)
    var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
    this.asc = !this.asc
    // if (!this.asc){rows = rows.reverse()} //essa coluna que faz a ordenaçao pelo reverso. foi comentada pois neste caso a ordenação é apenas em ASC
    for (var i = 0; i < rows.length; i++){table.append(rows[i])}
})

function comparer(index) {
    return function(a, b) {
        var valA = getCellValue(a, index), valB = getCellValue(b, index)
        return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
    }
}
function getCellValue(row, index){ return $(row).children('td').eq(index).text() }


// *****************************************************************************
// ******************* Testar se a descrição ja existe *************************
// *****************************************************************************
$(document).ready(function(){
    $('#lidescricao').on('keyup', function(){
        if($(this).val().length > 2)
            $.ajax({
                url: ROOT+'/painel/coletivos/cadastros/linhas/check/descricao',
                type:'post',
                data:{'descricao' : $(this).val(), 'clientes' : $('#selectCliCadLinhas').val()},
                success: function(dados){
                    if(dados.cont_linhas > 0){
                        $('.lidescricao-error').removeClass('hidden');
                        $('.lidescricao-error').html("Já existe uma linha com essa descrição");
                    }else{
                        if(!$('.lidescricao-error').hasClass('hidden')){
                            $('.lidescricao-error').addClass('hidden');
                        }
                    }
                }
            });
    })
})


////////////////////////////////////////////////////////////////////////////////
///////////////////// Code from definir horario ////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
$('.input-hora').timepicker({
	minuteStep: 5,
	showMeridian: false,
    timeFormat: 'H:i',
	defaultTime: '08:00'
});

$('.bt-add-dia-semana').click(function(){
    if($('#horario').val().length > 0){
        if(!checkJaExisteHorario($('#horario').val(), $('#selectDiaSemana :selected').html())){
            $('.lihorarios-error').addClass('hidden');
            var tr = '';
            tr = `<tr>
                    <td class="hr-horario" >${$('#horario').val()}</td>
                    <td class="hr-dia-semana" data-id='${$('#selectDiaSemana').val()}'>${$('#selectDiaSemana :selected').html()}</td>
                    <td class="td-span-remove"> <span class='fa fa-times bt-remover-horario' title="Remover horário da linha"></span> </td>
                </tr>`;
            $('.table-horarios tbody').prepend(tr);
        }else{
            $('.lihorarios-error').removeClass('hidden');
            $('.lihorarios-error').html("Horário já cadastrado");
        }
    }
    $('.input-time').focus();

    $('#mCSB_2_container').css('top','0px');
});

jQuery(document).on('keydown', '#horario', function(ev) {
    if(ev.which === 13) {
        $('.bt-add-dia-semana').trigger('click');
        return false;
    }
});

function checkJaExisteHorario(horario, dia){
    var tem = false;
    $('.hr-horario').each(function(){

        var a = ($(this).html()).split(':'); // split it at the colons
        var secondsa = (+a[0]) * 60 * 60 + (+a[1]);

        var b = horario.split(':'); // split it at the colons
        var secondsb = (+b[0]) * 60 * 60 + (+b[1]);

        if(secondsa == secondsb){
            if($(this).siblings('.hr-dia-semana').html() === dia){
                tem = true;
            }
        }
    })
    return tem;
}

$(document).on('click','.bt-remover-horario',function(){
    $(this).parents('tr').remove();
});

$('.bt-salvar-linha').click(function(){
    var descricao = $('#lidescricao').val();
    var seguirOrdemInsercaoPontos = $('#seguirOrdemInsercaoPontos').is(':checked');
    var pontos = pontos_selecionados;
    var horarios = [];
    //
    $('.table-horarios tbody tr').each(function(index){
        var array = [];
        array.push($(this).find('.hr-horario').html());
        array.push($(this).find('.hr-dia-semana').data('id'));
        horarios.push(array);
    })

    if(validations(descricao,seguirOrdemInsercaoPontos,pontos,horarios)){
        $.ajax({
            url: ROOT+'/painel/coletivos/cadastros/linhas/cadastrar',
            type:'post',
            data:{'cliente' : $('#selectCliCadLinhas').val(),
                'descricao' : descricao,
                'seguirOrdemInsercaoPontos' : true /*seguirOrdemInsercaoPontos*/,
                'pontos' : pontos,
                'horarios' : horarios,
                'licodigo' : $('#licodigo').val()
            },
            success: function(dados){
                console.log("Sucesso ao salvar Linha",dados);
                window.location.assign(ROOT+'/painel/coletivos/cadastros/linhas/listagem');
            },
            error: function(dados){
                console.log("Erro ao salvar Linha",dados);
            },
            beforeSend: function() {
                disableBuscaPonto();
                $('#loader').css('display', 'block');
            },
            complete: function() {
                enableBuscaPonto();
                $('#loader').css('display', 'none');
                //ir para listagem
            }
        });
    }else{
        console.log("não passou na validação");
    }
})

function validations(descricao,seguirOrdemInsercaoPontos,pontos,horarios){
    var validate = true;
    if(descricao == null || descricao == ""){
        $('.lidescricao-error').removeClass('hidden');
        $('.lidescricao-error').html("O campo descrição é obrigatório");
        validate = false;
    }
    if(pontos == [] || pontos.length < 2){
        $('.lipontos-error').removeClass('hidden');
        $('.lipontos-error').html("Selecione dois ou mais pontos para formar uma linha");
        validate = false;
    }

    if(horarios.length <= 0){
        $('.lihorarios-error').removeClass('hidden');
        $('.lihorarios-error').html("Deve haver no mínimo um horário relacionado a linha!");
        validate = false;
    }

    return validate;
}
