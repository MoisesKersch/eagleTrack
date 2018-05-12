$(document).on('change', '#inputPontoLatitude', function(){})
$(document).ready(function() {
    // var urlSearchNominatim = 'http://nominatim.eagletrack.com.br/nominatim/search.php';
    var urlSearchNominatim = ROOT+'/painel/cadastros/pontos/pesquisaPonto';
    $('.fa-spinner-localiza-ponto').hide()

    var cadDispon = $("#divCadastroPonto");
    cadDispon.each(function(index, element){
        var elem = $(element);
        var btAddDisp = elem.find('.bt-add-disponibilidade');
        var btLatitude = elem.find('#inputPontoLatitude');
        var inputPesquisarLoc = elem.find('#inputPesquisarLocalizacao');
        var tipoPonto = elem.find('#tipo');

        $(tipoPonto).change(function() {
            var llponto = novoPonto.getLatLng();
            var raio = novoPontoRaio.getRadius();
            novoPontoRaio.remove();
            novoPonto.remove();

            var imgPontos = {
                'C': ROOT+'/img/coleta.png', //coleta
                'E': ROOT+'/img/entrega.png', //entrega
                'P': ROOT+'/img/referencia.png', //referencia
            };

            var imgPonto = 'https://unpkg.com/leaflet@1.0.3/dist/images/marker-icon.png';
            if (typeof imgPontos[$(this).val()] !== 'undefined')
                imgPonto = imgPontos[$(this).val()];

            var icone = new L.icon({
                iconUrl: imgPonto,
                iconSize: [34, 34], //34, 34
                iconAnchor: [17, 32],
                popupAnchor: [-1, -30],
            });

            novoPonto = L.marker([llponto.lat,llponto.lng],{
                        draggable:true,
                        icon: icone
                    }).addTo(mapa);
            novoPontoRaio = L.circle([llponto.lat,llponto.lng],{
                            radius: raio
                        });
            mapa.flyTo([llponto.lat,llponto.lng]);
            var html = "<span class='bloco'>Me arraste para o local desejado.</span>"
                     + "<span class='bloco'><span class='linha'><strong>Raio:</strong><input id='inputRangeRaio' value='"+raio+"' type='range' min='10' max='200'><span id='metrosRaio'>"+raio+" Mts</span></span></span>";
            novoPonto.bindPopup(html)
                     .openPopup();
            novoPontoRaio.addTo(mapa);
            novoPonto.on('dragstart',function(){
                novoPontoRaio.remove();
            });
            novoPonto.on('dragend',function(){
                novoPonto.openPopup();
                novoPontoRaio.setLatLng([novoPonto.getLatLng().lat,novoPonto.getLatLng().lng])
                             .addTo(mapa);
                $(".inputLatitude").val(novoPonto.getLatLng().lat);
                $(".inputLongitude").val(novoPonto.getLatLng().lng);
                if($("#cadastroPontos").html() != 'undefined'){
                    var pontos = {'0':{'lat':novoPonto.getLatLng().lat, 'log':novoPonto.getLatLng().lng}}
                    var regiao = {'0':''};
                    var cliente = $("#pontoVeproprietario").val();
                    buscaRegiaoPonto(pontos, regiao, cliente)
                }

                dragPontoEndereco();
            });
        });

        function buscaRegiaoPonto(pontos, regiao, cliente){
            $.post(ROOT+'/painel/cadastros/pontos/regiao',
                {
                    pontos:pontos,
                    regiao:regiao,
                    cliente:cliente
                },
                function(dados){
                    var regiao = dados.regiao
                    if(typeof regiao != 'undefined') {
                        $("#poregiao").val(regiao.recodigo);
                        $('.regiao-nome').val(regiao.redescricao);
                    }else{
                        $("#poregiao").val('');
                        $('.regiao-nome').val('');
                    }
                }
            )
        }

        $(btAddDisp).on('click', function(e){
            e.preventDefault();
            var semana = ["Domingo", "Segunda-Feira", "Terça-Feira", "Quarta-Feira", "Quinta-Feira", "Sexta-Feira", "Sábado", "Segunda a sexta"];
            var horaInicio = elem.find('.hora-inicio').val();
            var horaFim = elem.find('.hora-fim').val();
            var dia = elem.find('#diaSemanaPontos').val();
            var dados = []
            var excel = ''
            var tabela =  $("#tableDispoPontos");
            var linhas = $(tabela).find('tr');
            if(linhas.length > 1) {
                for(j = 1; j < (linhas.length); j++) {
                    cols = $(linhas[j]).find('td');
                    var menor = horaMenor(horaInicio, $(cols[0]).html()) && horaMenor(horaFim, $(cols[0]).html());
                    var maior = horaMaior(horaInicio, $(cols[1]).html()) && horaMaior(horaFim, $(cols[1]).html());
                    var tr = '';
                    for(i in dia){
                    var nun1 = 0
                    var nun2 = 0
                    var nun3 = 0
                        if((semana[dia[i]] == $(cols[2]).html()) && maior) {
                            nun1++
                        }else if((semana[dia[i]] == $(cols[2]).html()) && menor) {
                            nun2++
                        }else if(semana[dia[i]] != $(cols[2]).html()) {
                            nun3++
                        }
                        tr += '<tr class="tr-dispo">';
                        tr += '<td>'+horaInicio+'</td>'
                        tr += '<input type="hidden" name="hora_inicio[]" value="'+horaInicio+'"/>'
                        tr += '<td>'+horaFim+'</td>'
                        tr += '<input type="hidden" name="hora_fim[]" value="'+horaFim+'"/>'
                        tr += '<td>'+semana[dia[i]]+'</td>'
                        tr += '<input type="hidden" name="semana[]" value="'+dia[i]+'"/>'
                        tr += '<td><a "href="#" class="removeDispo btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a></td>'
                        tr += '</tr>'
                        if(nun3 == 0 && nun1 == nun2) {
                            tr = '';
                            return;
                        }
                    }
                }
            }else{
                var tr = '';
                for(i in dia){
                    tr += '<tr class="tr-dispo">';
                    tr += '<td>'+horaInicio+'</td>'
                    tr += '<input type="hidden" name="hora_inicio[]" value="'+horaInicio+'"/>'
                    tr += '<td>'+horaFim+'</td>'
                    tr += '<input type="hidden" name="hora_fim[]" value="'+horaFim+'"/>'
                    tr += '<td>'+semana[dia[i]]+'</td>'
                    tr += '<input type="hidden" name="semana[]" value="'+dia[i]+'"/>'
                    tr += '<td><a "href="#" class="removeDispo btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a></td>'
                    tr += '</tr>'
                }
            }
            $(".table-dispo-pontos tbody").prepend(tr)
            $("#diaSemanaPontos").children('option').prop('selected', false);
            $("#diaSemanaPontos").select2()
            $(".removeDispo").click(function(){
                removeDispo($(this))
           })
        })

        $(document).click(function() {
            $('#divListPesquisarPontos').remove();
        });

        $('#spanBuscaPonto').click(function() {
            $('.fa-spinner-localiza-ponto').show();
            if ($(inputPesquisarLoc).val()) {
                $.ajax({
                    url: urlSearchNominatim,
                    data: {
                        'q': $(inputPesquisarLoc).val()
                    },
                    method: 'post',
                    success: function(retorno) {
                        $('.fa-spinner-localiza-ponto').hide();
                        $('#divListPesquisarPontos').remove();
                        var div = '<div id="divListPesquisarPontos" class="list-group">';

                        for (var i in retorno) {
                            var endereco = montaOptionsEndereco(retorno[i]);
                            div += '<button type="button" data-lat="'+retorno[i].lat+'" data-lon="'+retorno[i].lon+'" class="list-group-item divListPesquisarPontos" value="'+endereco+'">'+endereco+'</button>';
                        }

                        if (retorno.length < 1)
                            div += '<div class="list-group-item">Nenhum dado encontrado!</div>';

                        div += '</div>';

                        $(inputPesquisarLoc).parent().parent().append(div);

                        $('.divListPesquisarPontos').click(function() {
                            $(inputPesquisarLoc).val($(this).val());
                            setaNovaPosicaoPonto($(this).data('lat'), $(this).data('lon'));
                            var pontos = {'0':{'lat':novoPonto.getLatLng().lat, 'log':novoPonto.getLatLng().lng}}
                            var regiao = {'0':''};
                            var cliente = $("#pontoVeproprietario").val();
                            buscaRegiaoPonto(pontos, regiao, cliente);
                        });

                        function setaNovaPosicaoPonto(lat, lon) {
                            novoPonto.setLatLng([lat, lon]);
                            novoPontoRaio.setLatLng([lat, lon]);
                            mapa.flyTo([lat, lon]);
                            $('#inputPontoLatitude').val(lat);
                            $('#inputPontoLongitude').val(lon);
                        }
                    }
                });
            } else {
                $('#divListPesquisarPontos').remove();
                $('.fa-spinner-localiza-ponto').hide();
            }
        });

        novoPonto.on('dragend',function(){
            dragPontoEndereco();
        });

        $('#spanLimpaTempoLocaliza').click(function() {
            $(inputPesquisarLoc).val('');
        });

        function dragPontoEndereco() {
            $('.fa-spinner-localiza-ponto').show();
            $.ajax({
                url: urlSearchNominatim,
                data: {
                    'q': novoPonto.getLatLng().lat+','+novoPonto.getLatLng().lng
                },
                method: 'post',
                success: function(retorno) {
                    $('.fa-spinner-localiza-ponto').hide();
                    var endereco = montaOptionsEndereco(retorno[0]);
                    $(inputPesquisarLoc).val(endereco);
                }
            });
        }

        function montaOptionsEndereco(retorno) {
            var cidade = retorno.address.city ? retorno.address.city : retorno.address.city_district;
            cidade = cidade ? cidade : retorno.address.village

            var endereco = retorno.address.address29+', '+retorno.address.road+', '+retorno.address.suburb+', '+cidade+', '+retorno.address.town+', '+retorno.address.state+', '+retorno.address.postcode+', '+retorno.address.country;
            var enderecoEspacos = endereco.replace(/undefined, /g, '');
            // var enderecoEspacos2 = endereco.replace(/undefined,/g, '');
            // var enderecoPesquisa = enderecoEspacos2.replace(/\s{2,}/g, ' ');

            return enderecoEspacos;
        }





        // 17/10/2017 - 17:23
        // btLatitude.on('change', function(){
        //     console.log('asdf')
        // })
    })
    $(".removeDispo").click(function(){
        removeDispo($(this))
    })
    function removeDispo(thad) {
        var id = $(thad).data('id');
        if(typeof id != 'undefined') {
            $.post(ROOT+'/painel/cadastros/pontos/disponibilidade',{id:id}, function(data){
                // console.log(data);
            })
        }
        $(thad).parents('tr').remove()
    }

    var dataSet = [];
    var table;

    function ajaxAtualizaTabelaPontosList(){
        dataSet = null;
        dataSet = [];
        $.ajax({
            type: "POST",
            url: ROOT+'/painel/cadastros/pontos/listar/reload',
            data: {clientesbusca:$('#clientesbusca_pontos').val(),
    			   tipo_ponto:$('.tipo_ponto').val()},
                   dataType: "json",
            'success': function (data) {

                var ppeditar = $("#ppeditar").data('permissao');
                var ppexcluir = $("#ppexcluir").data('permissao');

                    for (var ponto in data.pontos) {

                    var local = [];
                    if(data.pontos[ponto].podescricao == undefined){
                        data.pontos[ponto].podescricao = "";
                    }
                    local.push(data.pontos[ponto].podescricao)

                    if(data.pontos[ponto].potipo == undefined){
                        data.pontos[ponto].potipo = "";
                    }else{
                        if(data.pontos[ponto].potipo == 'C' ) {
                            data.pontos[ponto].potipo = 'Coleta'
                        }else if(data.pontos[ponto].potipo == 'E') {
                            data.pontos[ponto].potipo = 'Entrega'
                        }else if(data.pontos[ponto].potipo == 'R') {
                            data.pontos[ponto].potipo = 'Risco'
                        }else if(data.pontos[ponto].potipo == 'P') {
                            data.pontos[ponto].potipo = 'Referência'
                        }
                    }
                    local.push(data.pontos[ponto].potipo)

                    if(data.pontos[ponto].poendereco == undefined){
                        data.pontos[ponto].poendereco = "";
                    }
                    local.push(data.pontos[ponto].poendereco)

                    if(data.pontos[ponto].poraio == undefined){
                        data.pontos[ponto].poraio = "";
                    }
                    local.push(data.pontos[ponto].poraio)

                    if(data.pontos[ponto].pocodigocliente == undefined){
                        data.pontos[ponto].pocodigocliente = "";
                    }
                    local.push(data.pontos[ponto].pocodigocliente)

                    var td = "";
                    var pocodigo = data.pontos[ponto].pocodigo;

                    if(ppeditar){
                        td += '<a title="Editar Ponto" class="btn btn-tb btn-info" href="'+ROOT+'/painel/cadastros/pontos/editar/'+pocodigo+'"><span class="fa fa-pencil"></span></a>'
                    }

                    if(ppexcluir){
                        td += '<a data-id="'+pocodigo+'" title="Excluir Ponto" class=" btn btn-tb btn-danger btn-del-ponto">'
                            td += '<span class="glyphicon glyphicon-minus"></span>'
                        td += '</a>';
                    }

                  local.push(td);

                  dataSet.push(local);
                }

                $('#tableCadastroPontos').DataTable().destroy();

                table =  $('#tableCadastroPontos').DataTable({
                    // paging: false,
                    retrieve: true,
                    language: traducao,
                    dom: 'Bfrtip',
            		buttons:
            			[{
            	           extend: 'pdf',
                           className: 'btn btn-lg btn-default exportar',
            	           exportOptions: { columns: [0,1,2,3,4] },
                            customize: function (doc) {
                                doc.defaultStyle.alignment = 'center';
                                doc.styles.tableHeader.alignment = 'center';
                                doc.content[1].table.widths =
                                Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                           },
                           orientation: 'landscape'
            	   		},{
            	           extend: 'excel',
            	           footer: false,
                           className: 'btn btn-lg btn-default exportar',
                           filename: 'Excel',
            			   exportOptions: { columns: [0,1,2,3,4] }
            		   },{
                          extend: 'csv',
                          footer: false,
                          className: 'btn btn-lg btn-default exportar',
                          exportOptions: { columns: [0,1,2,3,4] }
                       },{
                           extend: 'print',
                           text: 'Imprimir',
                           footer: false,
                           className: 'btn btn-lg btn-default exportar',
                           exportOptions: { columns: [0,1,2,3,4] }
                       }],
                    data: dataSet,
                    initComplete: function () {
                        $('.dt-buttons').prepend('<span class="label-botoes-table">Exportar para: </span>');
                        $('.exportar').removeClass("dt-button buttons-pdf buttons-csv buttons-excel buttons-html5");
                        $('.exportar').prepend("<span class='fa fa-save'></span>");
                    }
                });



            }
        });
    }

    // ajaxAtualizaTabelaPontosList();

    $("#clientesbusca_pontos").on("change", function () {
            ajaxAtualizaTabelaPontosList();
    });

    $(".tipo_ponto").on("change", function () {
            ajaxAtualizaTabelaPontosList();
    });

    $(document).ready(function(){
        $('#clientesbusca_pontos').trigger('change');
    })


    $(document).on('click','.btn-del-ponto',function(){
        var id = $(this).data('id');
        var thad = $(this);

        $.ajax({
          url: ROOT+"/painel/cadastros/pontos/excluir/"+id,
          type: "GET",
          dataType: "json",
          success : function(data){
            if(data.codigo == 200){
                table.row($(thad).parents('tr')).remove().draw();
            }else if(data.codigo == 500){
                console.log("erro ao excluir");
            }
          }
        });
    });

    function horaMaior(hora1, hora2)
    {
        hora1 = hora1.split(":");
        hora2 = hora2.split(":");

        var d = new Date();
        var data1 = new Date(d.getFullYear(), d.getMonth(), d.getDate(), hora1[0], hora1[1]);
        var data2 = new Date(d.getFullYear(), d.getMonth(), d.getDate(), hora2[0], hora2[1]);

        return data1 > data2;
    };

    function horaMenor(hora1, hora2)
    {
        hora1 = hora1.split(":");
        hora2 = hora2.split(":");

        var d = new Date();
        var data1 = new Date(d.getFullYear(), d.getMonth(), d.getDate(), hora1[0], hora1[1]);
        var data2 = new Date(d.getFullYear(), d.getMonth(), d.getDate(), hora2[0], hora2[1]);

        return data1 < data2;
    };

});
