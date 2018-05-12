var inicioRota = '';
var fimRota = '';
var pontos_adicionar = ''; //guarda um cache da consulta de pontos, para carregar mais rapido

$(document).ready(function(){
 	var carregar = $("#mapaRotaManual");
  	carregar.each(function(idx, element){
	    carregaMapa();
  	})

	var roteirizadorRotaManual = $("#roteirizadorRotaManual");
	var polyRM = [];
	var polyCarregados = [];
	var polygonn = [];
	var rotaManualRota = [];
	var rotaManualPontos = [];
    var inicioLatLng = '';
    var fimLatLng = '';
	var jaPontos = [];
    var layerInicio = [];
    var layerFim = [];
    var layerRotInicio = [];
    var layerRotFim = [];
	latcli = CLLATITUDE;
	lngcli = CLLONGITUDE;
	var rota = 0;


	roteirizadorRotaManual.each(function(index, element){
		var elem = $(element);
		var selectEmpresa = elem.find('.select-empresa-rota-manual');
		var selectRegiao = elem.find('.select-regiao-reta-manual');
		var data = elem.find(".data-data");
		var pedidos = elem.find(".pedidos-rota");
		var gerarRota = elem.find("#btGerarRotaManual");
        var disabledBt = elem.find('.disabled-bt');
		var veicuosRota = elem.find('.veiculos-rota-manual');
		var maisPedidos = elem.find('.mais-pedidos');
        var selectPontos = elem.find('.inicio-fim')
		var checkTodos = elem.find('.ip-check-todos .checkk');
		var btnFinalizacaoRota = elem.find('#btnIrFinalizacaoRota');
        $(selectRegiao).select2({
		    "language": "pt-BR",
		    "placeholder": "Selecione a região"
		})

		// function irFinalizarRota() {
		// 	var dataRoteirizacao = $(data).val();
		// 	var cliente = $(selectEmpresa).val();
		// 	$(btnFinalizacaoRota).click(function() {
		// 		window.location.assign(ROOT+'/painel/roteirizador/finalizacao/rota?data='+dataRoteirizacao+'&cliente='+cliente+'&buscar=true');
		// 	});
		// }

  //       irFinalizarRota();

		// $(data).change(function() {
		// 	irFinalizarRota();
		// });

		$(selectEmpresa).change(function(){
			var id = $(this).val();
			$.post(ROOT+'/painel/roteirizador/rota/manual/regioes', {id:id}, function(dados){
				var regiao = dados.regioes
                var pontos = dados.pontos
				var option = '<option value="00">Selecionar tudo</option>';
				var opt = '<option value="">Selecione um ponto de saída</option>';
                for(p in pontos){
					opt += '<option data-lat="'+pontos[p].polatitude+'" data-lng="'+pontos[p].polongitude+'" value="'+pontos[p].pocodigo+'">'+pontos[p].podescricao+'</option>';
				}

                var optr= '<option value="">Selecione um ponto de retorno</option>';
                for(p in pontos){
					optr += '<option data-lat="'+pontos[p].polatitude+'" data-lng="'+pontos[p].polongitude+'" value="'+pontos[p].pocodigo+'">'+pontos[p].podescricao+'</option>';
				}


				for(i in regiao){
					option += '<option value="'+regiao[i].recodigo+'">'+regiao[i].redescricao+'</option>';
				}
				$(selectRegiao).append(option)
                $("#pontoInicio").html(opt)
                $("#pontoFim").html(optr)

			})
		})
        $(selectEmpresa).trigger('change');

		$(selectRegiao).change(function(){
			var todos = $(this).val();
		    var t = todos.indexOf("00")
		    if(t == 0) {
		        $(selectRegiao).children().attr('selected', true)
		        $(selectRegiao).select2({
				    "language": "pt-BR"
				})
		    }
		    var id = $(this).val();
		    var url = $(this).attr('data-url');
		    var sel = $(this).attr('data-id');

		})
		$(document).on('dblclick', '.ips-pedidos', function(e){
			e.preventDefault();
			$(this).attr('readonly', false);

		})
		$(document).on('click', '.check', function(){
			if($(this).is(':checked')) {
				$(this).parents('.block-pedido-manual').addClass('sucesso').removeClass('alerta info');
			}else{
				$(this).parents('.block-pedido-manual').addClass('alerta').removeClass('info sucesso')
			}
			var checkeds = $(".sucesso .check:checked");
			var capacidade = $('.vei-capacidade');
			cargaTotal(checkeds, capacidade);
			desenhaRota(checkeds, inicioLatLng, fimLatLng);
		})
		$(document).on('blur', '.ips-pedidos', function(e){
			var thad = $(this);
			e.preventDefault();
			var valor = $(this).val();
			var campo = $(this).data('camp');
			var id = $(this).data('id');
			$.post(ROOT+"/painel/roteirizador/rota/manual/editar/itens",
				{
					valor:valor,
					campo:campo,
					id:id
				},
				function(data){
                    if($(thad).parent().hasClass('ip-quilos')){
                        var val = $(thad).val();
                        $(thad).parents(".block-pedido-manual").find(".check").data('peso', parseFloat(val));
                        var sucessoChecked = $(".sucesso .check");
                        var capacidade = $('.vei-capacidade');
                        if($(thad).parents('.block-pedido-manual').find('.check').is(':checked')) {
                            cargaTotal(sucessoChecked, capacidade);
                        }
                    }
					$(thad).attr('readonly', true);
				}
			)

		})

		
		$(maisPedidos).click(function(e){
			e.preventDefault();
			var id = $(selectEmpresa).val();
			if(pontos_adicionar.length == 0){
				$.post(ROOT+'/painel/roteirizador/rota/manual/mais/pedido',
					{
						id:id
					},
					function(dados){
						pontos_adicionar = dados.pontos;
						abrir_modal();
					}
				)
			}
			else
				abrir_modal();
			//======================================
			function abrir_modal(){
				var modal = $("#modalAlerta");
				$(".message-rota-men").remove();
				var msg = '';
				if(pontos_adicionar.length < 1) {
					msg += '<div class="alert alert-warning center-block message-rota-men" role="alert">'
					msg += '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>'
					msg += '<strong>Atenção! Nenhum ponto disponível</strong>'
					msg += '</div>'
					$('.messagens').prepend(msg)
					$(modal).modal('hide')
					return;
				}else {
					var opt = '';
					cli = pontos_adicionar[0].pocodigocliente
					for(i in pontos_adicionar) {
						opt += '<option data-id="'+pontos_adicionar[i].pocodigocliente+'" value="'+pontos_adicionar[i].pocodigo+'">'+pontos_adicionar[i].podescricao+'</option>'
					}
					modal.each(function(index, element){
						var el = $(element);
						var body = el.find('.modal-body');
						var title = el.find('.modal-title');
						var footer = el.find('.modal-footer');
						var data = $("#dataRoteirizacao").val();

						$(title).html('Adicionar pedido');
						var form = '<form class="form rota-manual" action="'+ROOT+'/painel/roteirizador/rota/manual/novo/pedido">'
							form += '<select class="form-control empresa">'+opt+'</select>'
							form += '<input type="text" name="irdocumento" placeholder="Número do documento" class="form-control numero">';
							form += '<input type="hidden" class="ircliente" name="ponto" value="'+pontos_adicionar[0].pocodigo+'">';
							form += '<input type="hidden" name="irdata" value="'+data+'">';
							form += '<input type="text" name="irqtde" placeholder="Qtde*" class="form-control add-campos-form qtde">';
							form += '<input type="number" step="0.10" name="ircubagem" placeholder="Cubagem*" class="form-control add-campos-form cubagem">';
							form += '<input type="number" step="0.10" name="irpeso" placeholder="Peso em quilos*" class="form-control add-campos-form peso">';
							form += '<input type="text" name="irvalor" placeholder="Valor*" class="form-control money add-campos-form valor">';
							form += '<span class="text-danger campos-obrigatorios">Os campos com * são obrigatórios complete-os antes de continuar!</span>'
							form += '<div class="bts-form">';
							form += '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>';
	          				form += '<button type="button" type="submit" data-dismiss="modal" disabled id="modalAddItemRota" class="btn btn-primary  bt-modal-desable">Salvar</button>';
	          				form += '</div>'
	          				form += '</form>';

	                        $(body).html(form);
	                        $(modal).modal('show');

	                        $(footer).html('')
	                        $('.empresa').select2({
	                            "language": "pt-BR",
	                            "placeholder": "Selecione a região",
	                            dropdownParent: $('#modalAlerta')
	                        })

							$(".money").maskMoney({symbol:'R$ ',
						    showSymbol:true, thousands:'.', decimal:',', symbolStay: true});
						    // $('.peso').mask('?99999');
						    $('.qtde').mask('?99999');

	          				$('.add-campos-form').keyup(function(){
	  							var preenchidos = true;
	          					$('.add-campos-form').each(function(i){
	          						if(!this.value) {
	          							preenchidos = false;
	          							return false;
	          						}
	          					})
	          					$('#modalAddItemRota').prop('disabled', !preenchidos);
	          				})

	      				$('#modalAddItemRota').click(function(){
	      					salvaItemRota($(this))
	      				})
					})
				}
			}
		})

		$(document).on('change', '.form.rota-manual .empresa', function(){
			$(this).siblings('.ircliente').val($(this).val());
		})

        $(disabledBt).change(function(){
            var preenchidos = true;
            $(disabledBt).each(function(i){
                if(!this.value) {
                    preenchidos = false;
                    return false;
                }
            })
            $(gerarRota).attr('disabled', !preenchidos);
        })


		function geraLayer(ponto) {
            var ht = "<div id='myIcon"+ponto.ircodigo+"' class='myIcon icon-roteirizados "+ponto.ircodigo+" "+ponto.pocodigo+"'><span class='sp-icon fa fa-map-marker b'></span></div>";
	        var icone = new  L.divIcon({
	            className: "divIcon",
	            html: ht,
	            iconSize:     [22, 35], // size of the icon
			    shadowSize:   [50, 64], // size of the shadow
			    iconAnchor:   [10, 30], // point of the icon which will correspond to marker's location
			    shadowAnchor: [0, 0],  // the same for the shadow
			    popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
	        });

	        jaPontos[ponto.ircodigo] = new L.marker([ponto.polatitude,ponto.polongitude],{icon: icone});

	        link = '<span> Descrição: '+ponto.podescricao+'</span><br />';
	        link += '<a href="#" data-rota="#'+ponto.pocodigo+'" class="btn btn-danger bt-desroteirizar">Remover desta rota</a>'
	        jaPontos[ponto.ircodigo].bindPopup(link).addTo(mapa);
		}

		$(gerarRota).click(function(e){
			e.preventDefault();
            mapa.removeLayer(layerInicio)
            mapa.removeLayer(layerFim)
            $('.busca-veiculos input').val('');
            $('.busca-pedido input').val('');
            if(jaPontos.length > 0)
                mapa.removeLayer(jaPontos)
            $(".myIcon.roteirizados").hide();
			var id = $(selectRegiao).val();
			var cli = $(selectEmpresa).val();
			var latlng = [];
			var dia = $(data).val();
            inicioRota = $("#pontoInicio option:selected");
            inicioLatLng = [ $(inicioRota).data('lat'), $(inicioRota).data('lng') ];
            fimRota = $("#pontoFim option:selected");
            fimLatLng = [ $(fimRota).data('lat'), $(fimRota).data('lng') ];

            var ht = "<div class='alerta myIcon roteirizados-iniFim'><span class='sp-icon fa fa-map-marker'></span></div>";
            var icone = new  L.divIcon({
                className: "divIcon",
                html: ht,
                iconSize:     [22, 35], // size of the icon
                shadowSize:   [50, 64], // size of the shadow
                iconAnchor:   [10, 30], // point of the icon which will correspond to marker's location
                shadowAnchor: [0, 0],  // the same for the shadow
                popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
            });

            layerInicio = new L.marker([inicioLatLng[0],inicioLatLng[1]],{icon: icone});
            layerFim = new L.marker([fimLatLng[0], fimLatLng[1]],{icon: icone});
            link = '<span> Descrição: Início</span><br />';
            lnk = '<span> Descrição: Fim</span><br />';
            layerFim.bindPopup(lnk).addTo(mapa);
            layerInicio.bindPopup(link).addTo(mapa);


			$.post(ROOT+'/painel/roteirizador/rota/manual/itens',
				{
			 		id:id,
			 		cli:cli,
			 		dia:dia,
			 		latlng:latlng,
		 		},
		 		function(dados){
		 			var item = dados.itens;
		 			var veiculo = dados.veiculos;
		 			var regioes = dados.regioes;
		 			var rot = dados.roteirizados;
		 			var itens = ''
		 			var cont = 0;
		 			var idRegiao = $(selectRegiao).val();
		 			var allPolygon = [];
		 			var vei = '';
		 			var collapse = '';
		 			for (z in rot) {
		 				ponto = rot[z];
		 				// cont = 1;
		 				var pdados = '';
                        var link = '';
                        var lnk = '';
		 				for(po in ponto) {
                            var ht = "<div class='alerta roteirizados ro-"+ponto[po].irplaca+" myIcon'><span class='fa sp-icon fa-map-marker'></span></div>";
                            var icone = new  L.divIcon({
                                className: "divIcon",
                                html: ht,
                                iconSize:     [22, 35], // size of the icon
                                shadowSize:   [50, 64], // size of the shadow
                                iconAnchor:   [10, 30], // point of the icon which will correspond to marker's location
                                shadowAnchor: [0, 0],  // the same for the shadow
                                popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
                            });

                            layerRotInicio[po] = new L.marker([ponto[po].lasaida,ponto[po].losaida],{icon: icone});
                            layerRotFim[po] = new L.marker([ponto[po].laretorno, ponto[po].loretorno],{icon: icone});
                            link = '<span> Descrição: Início rota</span><br />';
                            lnk = '<span> Descrição: Fim rota</span><br />';
                            layerRotFim[po].bindPopup(lnk).addTo(mapa);
                            layerRotInicio[po].bindPopup(link).addTo(mapa);
 					        pdados += '<div data-latlng="'+ponto[po].polongitude+','+ponto[po].polatitude+'" data-id=".'+ponto[po].ircodigo+'" class="li-roteirizados">'+
 					        	'<span class="roteirizados-count '+ponto[po].pocodigo+'">'+(parseInt(po) +1)+'</span>'+
 					        	'<span title="'+ponto[po].podescricao+'" class="roteirizados-cliente">'+ponto[po].podescricao.substr(0, 17)+'</span>'+
 					        	'<a id="'+ponto[po].pocodigo+'" class="roteirizados-remove" data-po="'+ponto[po].pocodigo+'" data-id="'+ponto[po].ircodigo+'" href="#"><span class="fa fa-minus"></span></a></div>'
 					        geraLayer(ponto[po]);
		 				}
		 				console.log('tester ',ponto[po])
		 				collapse += '<div class="panel panel-roteirizados panel-default" data-rocodigo="'+ponto[po].rocodigo+'" data-rocor="'+ponto[po].rocor+'">'+
	                                '<div class="panel-heading" role="tab" id="headingOne">'+
	                                    '<h4 class="panel-title">'+
	                                        '<a role="button" data-idrota="'+ponto[po].rocodigo+'" data-placa="'+ponto[po].rocodigo+'" data-rocor="'+ponto[po].rocor+'" data-toggle="collapse" data-parent="#accordion" href="#collapse'+ponto[po].rocodigo+'" aria-expanded="false" class="bt-carr-placa teste"  aria-controls="collapseOne">'+ponto[po].irplaca+

	                                   		    '<span class="fa fa-angle-double-right"></span>'+
	                                        '</a>'+
	                                    '</h4>'+
	                                '</div>'+
	                                '<div id="collapse'+ponto[po].rocodigo+'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">'+
	                                    '<div class="panel-body">'+
                                            '<div data-latlng="'+ponto[po].losaida+','+ponto[po].lasaida+'" class="hidden li-roteirizados"></div>'+
		                                    pdados+
                                            '<div data-latlng="'+ponto[po].loretorno+','+ponto[po].laretorno+'" class="hidden li-roteirizados"></div>'+
		                                    '<div><a href="#" data-placa="'+ponto[po].irplaca+'" data-rocodigo="'+ponto[po].rocodigo+'" data-id="'+ponto[po].rocodigo+'" class="remover-carregamento pontos-delete-'+ponto[po].rocodigo+'">Remover carregamento</a></div>'+
	                                    '</div>'+
	                                '</div>'+
	                            '</div>'
		 			}

		 			$('.ja-roteirizados #accordion').html(collapse);
		 			if($('.roteirizados-cliente').length > 0) {
		 				$(".bt-confirma-rota").attr('disabled', false);
		 				$(".bt-imprime-rota").attr('disabled', false);
		 			}
		 			$(".regioesRemover").remove();
		 			for (r in regioes) {
		 				var coordenadas = regioes[r].regioes_coordenadas;
	 					var iconMatriz = L.icon({
					        iconUrl: ROOT+'/img/matriz.png',
					        iconSize: [50,50]
					    });
					    if (typeof(matriz) != 'undefined') matriz.remove();
					    coordenadasArray = [];
					    for(c in coordenadas) {
					    	coordenadasArray.push([coordenadas[c].rclatitude, coordenadas[c].rclongitude]);
					    }
					    polygonn = L.polygon(
					    	coordenadasArray,
					    	{
					    		color: regioes[r].recor.toString(),
					    		opacity: 0.3,
					    		fill: true,
					    		fillOpacity: 0.2,
					    		allowIntersection: false,
					    		className: 'regioesRemover'
					    	}
					    ).addTo(mapa);
					    var posicaoRegiao = (allPolygon.push(polygonn))-1;
					    var velocidade = regioes[r].revelocidade ? '<div> Velocidade: '+(regioes[r].revelocidade.split('.'))[0]+' km/h</div>' : '';
					    var botaoEditar =
					    	'<div style="position: relative; float: left;">'
					    		+'<button type="button" class="btn btn-info btn-xs edita-regiao">Editar</button>'
					    	+'</div>';
					    var popupRegiao =
					    allPolygon.push(polygonn);
		 			}

		 			for(j in veiculo) {
		 				var color = 'perigo';
		 				var peso = 0;
		 				var carregar = '';
		 				var carregamento = '';
		 				var paradas = veiculo[j].qtde;
		 				if(veiculo[j].irstatus == 'R'){
		 					color = 'azul';
		 					// peso =  parseFloat(veiculo[j].peso);
	                        classParadas = 'veparadas-carregados';
	                        classCarregamento = 'linha3';
	                        // classCarregamento = 'linha3-carregados';
	                        carregar += '<a href="#" data-cli="'+veiculo[j].veproprietario+'" data-p="'+veiculo[j].veplaca+'" class="carregar-item">Carregar</a>';
	                        carregamento = '<span class="carregamento"></span>';
	                        classPeso = 've-peso';
	                        // classPeso = 've-peso-carregados';

		 				}else {
	                        carregar += '<a href="#" data-cli="'+veiculo[j].veproprietario+'" data-p="'+veiculo[j].veplaca+'" class="carregar-item">Carregar</a>';
	                        carregamento = '<span class="carregamento"></span>';
	                        classParadas = 'veparadas';
	                        classCarregamento = 'linha3';
	                        classPeso = 've-peso';
		 				}
	 					peso =  parseFloat(veiculo[j].vemaxpeso) - peso;

		 				vei += '<div class="block-veiculo-rota '+color+' col-md-2">'+
		                        '<div class="linha1">'+
		 							'<span class="item-placa">'+veiculo[j].veplaca+'</span>'+
		                        	carregar+'</div>'+
		                        '<div class="linha2">'+
		                            '<div class="dados-veiculos">'+
		                                '<span>Cap. Kg: </span>'+
		                                '<span class="sp-dados vei-capacidade">'+veiculo[j].vemaxpeso+'</span>'+
		                            '</div>'+
		                            '<div class="dados-veiculos">'+
		                                '<span>Disponível: </span>'+
		                                '<span class="sp-dados '+classPeso+'">'+peso.toFixed(2)+'</span>'+
		                            '</div>'+
		                            '<div class="dados-veiculos">'+
		                                '<span>Entregas: </span>'+
		                                '<span class="sp-dados '+classParadas+'">'+paradas+'</span>'+
		                            '</div>'+
		                        '</div>'+
		                        '<div class="'+classCarregamento+'">'+carregamento+'</div>'+
		                    '</div>'
		 			}
		 			if(veiculo.length < 1) {
		 				vei = '<div class="nada-encontrado">Nenhum veículo encontrado!</div>'
		 			}
		 			$(veicuosRota).html(vei);
		 			if(typeof pontos != 'undefined') {
		 				for(i in pontos) {
				 			mapa.removeLayer(pontos[i])
		 				}
		 			}
	 				pontos = []; //to a vingin

		 			for(i in item) {
		 				var nomes = (item[i].podescricao).substring(0, 20);
			 			var cor = 'alerta';
			 			var selecionado = '';
		 				cont++;
		 				if(idRegiao.length > 0 && item[i].poregiao != null) {
		 					var pontoRegiao = item[i].poregiao.toString()
		 					var regiao = idRegiao.indexOf(pontoRegiao);
		 					if(regiao > -1) {
		 						cor = item[i].poregiao == idRegiao[regiao] ? 'sucesso' : 'alerta';
		 						selecionado = item[i].poregiao == idRegiao[regiao] ? 'checked' : '';
		 					}
		 				}
	 					if(item[i].irstatus == 'R') {
	 						cor = 'azul';
							selecionado = 'checked';
			                    var ht = "<div id='myIcon"+i+"' class='"+cor+" myIcon "+item[i].pocodigo+"'><span class='sp-icon fa fa-map-marker'></span></div>";
	 					}else{
			                    var ht = "<div id='myIcon"+i+"' class='"+cor+" myIcon "+item[i].pocodigo+"'><span class='sp-icon fa fa-map-marker'></span></div>";
	 					}
			                var icone = new  L.divIcon({
			                    className: "divIcon",
			                    html: ht,
		                        iconSize:     [22, 35], // size of the icon
							    shadowSize:   [50, 64], // size of the shadow
							    iconAnchor:   [10, 30], // point of the icon which will correspond to marker's location
							    shadowAnchor: [0, 0],  // the same for the shadow
							    popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
			                });

			                pontos[i] = new L.marker([item[i].polatitude,item[i].polongitude],{icon: icone});
			                link = '<span> Descrição: '+item[i].podescricao+'</span><br />';
			                link += '<a href="#" data-rota="#'+item[i].pocodigo+'" class="btn btn-info bt-roteirizar">Incluir/Remover da rota</a>'
			                pontos[i].bindPopup(link).addTo(mapa);

			            console.log(nomes.length)
			            var reticencias = nomes.length > 15 ? "..." : "";
		 				itens += '<div class="col-sm-12 block-pedido-manual '+cor+'">'+
			                        '<div class="head-pedido-manual">'+
			                            '<span class="pedido-manual-tipo-ponto">'+item[i].potipo+'</span>'+
			                            '<span class="pedido-manual-nome-ponto">'+nomes.substr(0, 15)+''+reticencias+'</span>'+
			                            '<div class="block-pedido-carregar">'+
			            '<input title="Selecionar pedido" type="checkbox" data-rota=".'+item[i].pocodigo+'" data-id="'+item[i].ircodigo+'" data-lat="'+item[i].polatitude+'" data-long="'+item[i].polongitude+'" data-peso="'+item[i].irpeso+'" '+selecionado+' class="check" name="" id="'+item[i].pocodigo+'">'+
			                            '</div>'+
			                            '<div class="block-pedido-carregar">'+
			                            	'<a href="#" data-p=".'+item[i].pocodigo+'" data-id="'+item[i].ircodigo+'" class="remove-pedido"><span class="fa fa-minus" /></a>'+
			                            '</div>'+
			                        '</div>'+
			                        '<div class="block-editar-pedigo">'+
			                            '<div class="inputs-editar ip-pedigo">'+
			                                '<label for="">Pedido</label>'+
			                                '<input title="Duplo clique para editar" type="text" readonly value="'+item[i].irdocumento+'" name="" data-camp="irdocumento" data-id="'+item[i].ircodigo+'" class="form-control ips-pedidos">'+
		                                '</div>'+
			                            '<div class="inputs-editar ip-valor">'+
			                                '<label for="">Valor</label>'+
			                                '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item[i].irvalor+'" data-camp="irvalor" data-id="'+item[i].ircodigo+'" class="form-control ips-pedidos">'+
			                            '</div>'+
			                            '<div class="inputs-editar ip-volumes">'+
			                                '<label for="">Vol.</label>'+
			                                '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item[i].irqtde+'" data-camp="irqtde" data-id="'+item[i].ircodigo+'" class="form-control ips-pedidos">'+
			                            '</div>'+
			                            '<div class="inputs-editar ip-cubagem">'+
			                                '<label for="">Cub.</label>'+
			                                '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item[i].ircubagem+'" data-camp="ircubagem" data-id="'+item[i].ircodigo+'" class="form-control ips-pedidos">'+
			                            '</div>'+
			                            '<div class="inputs-editar ip-quilos">'+
			                                '<label for="">Quilos</label>'+
			                                '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item[i].irpeso+'" data-camp="irpeso" data-id="'+item[i].ircodigo+'" class="form-control ips-pedidos">'+
			                            '</div>'+
			                            '<div class="inputs-editar ip-data">'+
			                                '<label for="">Data</label>'+
			                                '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item[i].irdata+'" data-camp="irdata" data-id="'+item[i].ircodigo+'" class="form-control ips-pedidos">'+
			                            '</div>'+
			                            '<div class="inputs-editar ip-agendamento">'+
			                                '<label for="">Agen.</label>'+
			                                '<input title="Duplo clique para editar" type="text" name="" id="" disabled class="form-control">'+
			                            '</div>'+
			                        '</div>'+
			                    '</div>'
		 			}
		 			if(item.length < 1) {
		 				itens = '<div class="nada-encontrado">Nenhum pedido encontrado!</div>'
		 			}
		 			$(pedidos).html(itens)
		 			var capacidade = $('.vei-capacidade');
		 			var checkeds = $('.sucesso .check:checked');
					cargaTotal(checkeds, capacidade);
					desenhaRota(checkeds, inicioLatLng, fimLatLng)
					$(".icon-roteirizados").hide();
					mapa.removeLayer(polyCarregados);
		 		}
	 		)
		})

		$(document).on('click', '.bt-carr-placa', mostrarRoteirizados);

		function mostrarRoteirizados(e) {
			e.preventDefault();
			$(".icon-roteirizados").hide();
            $(".myIcon.roteirizados").hide();
			mapa.removeLayer(polyCarregados);
			var collapsed = $(this);
			var thad = $(this);
			var itens = $(this).parents('.panel-roteirizados').find('.li-roteirizados');
            var placa = $(this).data('placa');
			$('.bt-carr-placa').css('color', '#FFF').parents('.panel-heading').css('background-color', '#6f7cc7')
            $('.bt-carr-placa').find('span').removeClass('fa-angle-double-down')
					.addClass('fa-angle-double-right');
			var latlng = [];
			//latlng.push({'polatitude': latcli, 'polongitude':lngcli});
			var cor = $(this).data('rocor');
			if(!$(collapsed).hasClass('collapsed') && !$(collapsed).hasClass('.bt-carr-placa')) {
                $(".ro-"+placa).show();
                $(".ro-"+placa + " span").css('color', cor);
				$(this).css('color', '#000').parents('.panel-heading').css('background-color', '#fafafa');
				$(this).find('span').removeClass('fa-angle-double-right')
					.addClass('fa-angle-double-down');
				$(itens).each(function(id){
					var icon = $(itens[id]).data('id');
					$(icon).show().find('span').css("color", cor);
					var item = $(itens)[id]
                    var id = $(item).data('id');
                    var cont = $(item).find('.roteirizados-count').html()
					item = $(item).data('latlng').split(',')
					latlng.push({'polatitude': item[1], 'polongitude':item[0]})
                    $(".icon-roteirizados"+id).prepend("<span title='"+cont+"º ponto' class='idx-roteirizados'>"+cont+"º<span>")
				});
				$.post(ROOT+'/painel/roteirizador/rota/manual/rotas',
					{
						latLong:latlng,
						placa:placa
					},
					function(data){
						desenhaRoteirizados(data, cor,thad)
					}
				)
			}
		}


		function desenhaRoteirizados(data, cor = '',thad){
			var ponto = data.ponto
			var tempo = data.tempo;
			var kms = data.kms;			mapa.removeLayer(polyCarregados);
			jaRoteirizados = data.rota
			var decode = decodePoly.decode(jaRoteirizados);
			console.log('cor11 ',$(thad).parents('.panel-roteirizados').data('rocor'))
			if(cor.length < 1)
				cor = geraCor();
			polyCarregados = L.polyline.antPath(
				decode,
	            {
	                color: cor,
	                delay: 2500,
	                weight: 7,
	            }
    	    )
			var link = '<span> Distância: '+data.kms+'</span><br />';
			link += '<span> Tempo: '+data.tempo+'</span><br />';
	        polyCarregados.bindPopup(link);
			polyCarregados.addTo(mapa);
		}

		$(document).on('click', '.roteirizados-remove', function(e){
			mapa.removeLayer(polyCarregados);
			e.preventDefault();
			var thad = $(this);

			mapa.removeLayer(jaPontos[$(this).data('id')]);

			var itens = $(thad).parents('.li-roteirizados').siblings();
			var latlng = $(thad).parents('.li-roteirizados').data('latlng').split(',')
			var id = $(thad).data('id');
			var po = $(thad).data('po');
			var desc = $(thad).siblings('.roteirizados-cliente').html();
			$.post(ROOT+'/painel/roteirizador/rota/manual/desassociar/item',
				{
					id:id,
					idrota: $(thad).parents('.panel-roteirizados').data('rocodigo')
				},
				function(data){
					var itens = data.itens;
					var tamItens = itens.length
					console.log('sizeof ',tamItens)
					var idRota = $(thad).parents('.panel-roteirizados').data('rocodigo');
                    if(tamItens == 0 ) {
                    	$.post('/painel/roteirizador/rota/manual/remover/rota',{
				                id:idRota
						},
						function(data){
							mapa.removeLayer(polyCarregados);
							console.log('seria id ',$(thad).data('rocodigo'))
							$(thad).parents('.panel-roteirizados').remove()
							// $(thad).parents('.pontos-delete-'+$(thad).data('rocodigo')+'').remove();
							$('#btGerarRotaManual').trigger('click');
						})
                    	$('#btGerarRotaManual').trigger('click');
                        $(thad).parents(".panel-roteirizados").remove();
                        return;
                    }

					var placa = itens[0].rota.roplaca;
					var rotaManualRota = data.rota;
					var codigorota = itens[0].rota.rocodigo;
					var cor = $(thad).parents('.panel-roteirizados').data('rocor');
					if(rotaManualRota.length > 0){
						var pdados = '';
						var rota = itens[0].rota;
						var pontoRetorno = rota.ponto_retorno;
						var pontoSaida = rota.ponto_saida;
						itens.unshift(pontoSaida);
						itens.push(pontoRetorno);
						for(i in itens){
							var ordem = itens[i].irordem;
							var item = $('.'+itens[i].pocodigo).find('.idx-roteirizados');
							if(typeof ordem != 'undefined'){
								$(item).html(ordem+'º')
									.attr('title', ordem+'º ponto');
								$(item).siblings('.fa-map-marker').css("color", cor);
							}
							if(i > 0 && i < itens.length - 1){
								pdados += '<div data-latlng="'+itens[i].polongitude+','+itens[i].polatitude+'" data-id=".'+itens[i].ircodigo+'" class="li-roteirizados">'+
									'<span class="roteirizados-count '+itens[i].pocodigo+'">'+itens[i].irordem+'</span>'+
									'<span title="'+itens[i].podescricao+'" class="roteirizados-cliente">'+itens[i].podescricao.substr(0, 17)+'</span>'+
									'<a id="'+itens[i].pocodigo+'" class="roteirizados-remove" data-po="'+itens[i].pocodigo+'" data-id="'+itens[i].ircodigo+'" href="#"><span class="fa fa-minus"></span></a></div>'
							}
						}
						$('#collapse'+codigorota+' .panel-body .li-roteirizados').remove();
						$('#collapse'+codigorota+' .panel-body').prepend(pdados);


					}
					var decode = decodePoly.decode(rotaManualRota);
					var ponto = data.ponto;

					$(thad).parents('.li-roteirizados').remove();

					var link = '<span> Distância: '+data.kms+'</span><br />';
					link += '<span> Tempo: '+data.tempo+'</span><br />';
					polyCarregados = L.polyline.antPath(decode,{
						delay: 2500,
						weight: 7,
						color: cor,
					});

					polyCarregados.bindPopup(link);
					polyCarregados.addTo(mapa);

					polyCarregados.on("add", function (event) {
					  event.target.openPopup();
					});

					var item = data.item;
					var reticencias = item.podescricao.length > 15 ? "..." : "";
					console.log(item.podescricao.length)
					itens = '<div class="col-sm-12 block-pedido-manual alerta">'+
		                    '<div class="head-pedido-manual">'+
		                        '<span class="pedido-manual-tipo-ponto">'+item.potipo+'</span>'+
		                        '<span class="pedido-manual-nome-ponto">'+item.podescricao.substr(0, 15)+''+reticencias+'</span>'+
		                        '<div class="block-pedido-carregar">'+
		            '<input title="Selecionar pedido" type="checkbox" data-rota=".'+item.pocodigo+'" data-id="'+item.ircodigo+'" data-lat="'+item.polatitude+'" data-long="'+item.polongitude+'" data-peso="'+item.irpeso+'" class="check" name="" id="'+item.pocodigo+'">'+
		                        '</div>'+
		                        '<div class="block-pedido-carregar">'+
		                        	'<a href="#" data-p=".'+item.pocodigo+'" data-id="'+item.ircodigo+'" class="remove-pedido"><span class="fa fa-minus" /></a>'+
		                        '</div>'+
		                    '</div>'+
		                    '<div class="block-editar-pedigo">'+
		                        '<div class="inputs-editar ip-pedigo">'+
		                            '<label for="">Pedido</label>'+
		                            '<input title="Duplo clique para editar" type="text" readonly value="'+item.irdocumento+'" name="" data-camp="irdocumento" data-id="'+item.ircodigo+'" class="form-control ips-pedidos">'+
		                        '</div>'+
		                        '<div class="inputs-editar ip-valor">'+
		                            '<label for="">Valor</label>'+
		                            '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item.irvalor+'" data-camp="irvalor" data-id="'+item.ircodigo+'" class="form-control ips-pedidos">'+
		                        '</div>'+
		                        '<div class="inputs-editar ip-volumes">'+
		                            '<label for="">Vol.</label>'+
		                            '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item.irqtde+'" data-camp="irqtde" data-id="'+item.ircodigo+'" class="form-control ips-pedidos">'+
		                        '</div>'+
		                        '<div class="inputs-editar ip-cubagem">'+
		                            '<label for="">Cub.</label>'+
		                            '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item.ircubagem+'" data-camp="ircubagem" data-id="'+item.ircodigo+'" class="form-control ips-pedidos">'+
		                        '</div>'+
		                        '<div class="inputs-editar ip-quilos">'+
		                            '<label for="">Quilos</label>'+
		                            '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item.irpeso+'" data-camp="irpeso" data-id="'+item.ircodigo+'" class="form-control ips-pedidos">'+
		                        '</div>'+
		                        '<div class="inputs-editar ip-data">'+
		                            '<label for="">Data</label>'+
		                            '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item.irdata+'" data-camp="irdata" data-id="'+item.ircodigo+'" class="form-control ips-pedidos">'+
		                        '</div>'+
		                        '<div class="inputs-editar ip-agendamento">'+
		                            '<label for="">Agen.</label>'+
		                            '<input title="Duplo clique para editar" type="text" name="" id="" disabled class="form-control">'+
		                        '</div>'+
		                    '</div>'+
		                '</div>'
		            $('.nada-encontrado').remove()
		            $(".pedidos-rota").prepend(itens)

		            $(".icon-roteirizados."+item.cocodigo).remove();
			        var ht = "<div id='myIcon"+item.ircodigo+"' class='alerta myIcon "+item.pocodigo+"'><span class='sp-icon fa fa-map-marker'></span></div>";
	                var icone = new  L.divIcon({
	                    className: "divIcon",
	                    html: ht,
	                    color: cor,
                        iconSize:     [22, 35], // size of the icon
					    shadowSize:   [50, 64], // size of the shadow
					    iconAnchor:   [10, 30], // point of the icon which will correspond to marker's location
					    shadowAnchor: [0, 0],  // the same for the shadow
					    popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
	                });

	                pontos[item.ircodigo] = new L.marker([item.polatitude,item.polongitude],{icon: icone});
	                link = '<span> Descrição: '+item.podescricao+'</span><br />';
	                link += '<a href="#" data-rota="#'+item.pocodigo+'" class="btn btn-info bt-roteirizar">Incluir/Remover da rota</a>'
	                pontos[item.ircodigo].bindPopup(link).addTo(mapa);
				}
			)
		})

		$(document).on('click', '.bt-roteirizar', function(e){
			e.preventDefault()
			var clas = $(this).data('rota');
			$(clas).trigger('click');

		})

		$(checkTodos).click(function() {
			if($(this).is(':checked')) {
				var checks = $('.block-pedido-manual .check');
				$(checks).prop("checked", true);
				$(checks).parents('.alerta')
					.removeClass('alerta')
					.addClass('sucesso');

				var ultimo = $('.block-pedido-manual .check:last');
				$(ultimo).prop("checked", false);
				$(ultimo).parents('.sucesso')
						.removeClass('sucesso')
						.addClass('alerta');
			}else{
				var checks = $('.block-pedido-manual .check');
				$(checks).prop("checked", false);
				$(checks).parents('.sucesso')
					.removeClass('sucesso')
					.addClass('alerta');

				var ultimo = $('.block-pedido-manual .check:last');
				$(ultimo).prop("checked", true);
				$(ultimo).parents('.alerta')
						.removeClass('alerta')
						.addClass('sucesso');
			}
			$(ultimo).trigger('click')
		})

		$(document).on('click', '.azul .check', function(){
			var id = $(this).data('id');
			$.post(ROOT+'/painel/roteirizador/rota/manual/desassociar/item',
				{
					id:id,
				},
				function(dados) {
					$('#btGerarRotaManual').trigger('click');
				}
			)
		})
	})

	function cargaTotal(checkeds, capacidade) {
		var total = 0;
		$(checkeds).each(function(){
			total += $(this).data('peso');
		});
		$(capacidade).each(function(){

			var carga = $(this).html()
			var pai = $(this).parents('.block-veiculo-rota');
			var disponivel = $(pai).find('.ve-peso');
			var entregas = $(pai).find('.veparadas');
			if(((total * 100) / carga) > 100) {
				$('.veiculos-rota-manual').append(pai)
			}
			if(((total * 100) / carga) > 90 && ((total * 100) / carga) < 100) {
				$('.veiculos-rota-manual').prepend(pai)
			}
			if(carga < total) {
				$(pai).addClass('perigo').removeClass('alerta sucesso padao');
			}else if(total < parseFloat((carga * 70) / 100)) {
				$(pai).addClass('padao').removeClass('perigo alerta sucesso');
			}else if(parseFloat((carga * 90) / 100) <= total) {
				$(pai).addClass('sucesso').removeClass('perigo alerta padao');
			}else if(parseFloat((carga * 70) / 100) <= total){
				$(pai).addClass('alerta').removeClass('perigo sucesso padao');
			}
			var carregementoTotal = ((total * 100) / carga).toFixed(2);
			var porcento = carregementoTotal > 100 ? 100 : carregementoTotal;
			spans = '<span title="'+carregementoTotal+'% Carregado" class="porcentagem"><span style=width:'+porcento+'%;></span><span>';

			$(disponivel).html((parseFloat(carga) - parseFloat(total)).toFixed(2))
			$(entregas).html(checkeds.length);
			$(pai).find('.carregamento').html(spans);
		});
	}

	function desenhaRota(checkeds, inicio, fim){
		rotaManualPontos = [];
		var lat = '';
		var long = '';
		var latLong = [];
		mapa.removeLayer(polyRM);
		latLong.push({'polatitude': inicio[0], 'polongitude':inicio[1]});
		$(checkeds).each(function(){
			lat = $(this).data('lat');
			long = $(this).data('long');
			latLong.push({'polatitude': lat, 'polongitude': long});

		});
		latLong.push({'polatitude': fim[0], 'polongitude':fim[1]});
		$.post(ROOT+'/painel/roteirizador/rota/manual/rotas',
			{
				latLong:latLong
			},

			function(data){
				var rotaManualRota = data.rota;
				var decode = decodePoly.decode(rotaManualRota);
				var ponto = data.ponto
                if(checkeds.length > 0) {
                    var link = '<span> Distância: '+data.kms+'</span><br />';
                    link += '<span> Tempo: '+data.tempo+'</span><br />';

                    polyRM = L.polyline.antPath(decode,{
                        delay: 2500,
                        weight: 7,
                    });

                    polyRM.bindPopup(link);
                    polyRM.addTo(mapa);

                    polyRM.on("add", function (event) {
                      event.target.openPopup();
                    });
                }
				$(".myIcon").removeClass('sucesso').addClass('alerta')
					.children('.rota-index').remove();
				$(checkeds).each(function(id, elmt){
					var idx = parseInt(ponto[id + 1].waypoint_index);
					var index = $(elmt).data('rota');
					var idd = parseInt(id) + 1;
					var pt = $(index);
					rotaManualPontos.push(idx);
					$(pt).removeClass('alerta')
					.addClass('sucesso')
					.css("position","relative")
					.prepend('<span title="'+idx+'º ponto" class="rota-index">'+idx+'º</span>');
				})
			}
		);
	}


	$(document).on('click', '.carregar-item', function(e){
		e.preventDefault();
        carregarItem(this)
        console.log('ok')
	});

	function carregarItem(thad){

		$(".icon-roteirizados").hide();
		mapa.removeLayer(polyCarregados);
		var thad = $(thad);
		var cli = $(thad).data('cli');
		var dadosCarro = $(thad) ;
		var dadosRota = rotaManualRota;
		var placa = $(dadosCarro).data('p')
		var posicaoPontos = rotaManualPontos;
		var check = $(".sucesso .check:checked");
		var itens = [];
		var carga = parseFloat($(thad).parents('.block-veiculo-rota').find('.linha3 .carregamento span').attr('title'));
		$(check).each(function(id, elmt){
			itens.push($(elmt).data('id'));
		})
        if(check.length == 0){
			var modal = '<span class="modal-subcarga">Selecione ao menos um pedido para roteirizar!</span>'
			var bts = '<button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>';
			$("#modalAlerta .modal-footer").html(bts)
			$("#modalAlerta .modal-body").html(modal);
			$("#modalAlerta .modal-title").html('Alerta');
			$("#modalAlerta").modal('show');

        }else if(carga < 90 && carga < 100) {
			var modal = '<span class="modal-subcarga">Veículo com apenas '+carga+'% de sua capacidade deseja carregar mesmo assim?</span>'
			var bts = '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>';
			bts += '<button type="button" type="submit" data-dismiss="modal" id="btModalSubCarga" class="btn btn-primary  bt-modal-desable">Carregar</button>';
			$("#modalAlerta .modal-footer").html(bts)
			$("#modalAlerta .modal-body").html(modal);
			$("#modalAlerta .modal-title").html('Confirmar Veículo');
			$("#modalAlerta").modal('show');
		}else if(carga > 100){
			var modal = '<span class="modal-subcarga">Veículo com '+carga+'% de sua capacidade deseja carregar mesmo assim?</span>'
			var bts = '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>';
			bts += '<button type="button" type="submit" data-dismiss="modal" id="btModalSubCarga" class="btn btn-primary  bt-modal-desable">Carregar</button>';
			$("#modalAlerta .modal-footer").html(bts)
			$("#modalAlerta .modal-body").html(modal);
			$("#modalAlerta").modal('show');
		} else {
			itensRota(placa, posicaoPontos, itens, cli);
		}

		$("#btModalSubCarga").click(function(){
			itensRota(placa, posicaoPontos, itens, cli);
		})
	}

	$(document).on('click', '.remover-carregamento', function(e){
		e.preventDefault();
		var thad = $(this);
		var placa = $(this).data('placa');
        var id = $(this).data('id');
		$.post('/painel/roteirizador/rota/manual/remover/rota',
			{
                id:id
			},
			function(data){
				mapa.removeLayer(polyCarregados);
				console.log('seria id ',$(thad).data('rocodigo'))
				$(thad).parents('.panel-roteirizados').remove()
				// $(thad).parents('.pontos-delete-'+$(thad).data('rocodigo')+'').remove();
				$('#btGerarRotaManual').trigger('click');
			}
		)
	})
	$(document).on('click', '.bt-desroteirizar', function(e){
		var id = $(this).data('rota');
		$('.li-roteirizados '+id).trigger('click');
	})
	$(document).on('click', '.remove-pedido', function(e){
		e.preventDefault();
		var modal = '<span class="modal-subcarga">Ao remover esse item, ele será excluído do banco de dados e não estará mais disponível para roteirização. Deseja remover mesmo assim?</span>'
		var bts = '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>';
		bts += '<button type="button" type="submit" data-dismiss="modal" id="btRemoveItem" class="btn btn-danger bt-modal-desable">Remover</button>';
		$("#modalAlerta .modal-footer").html(bts)
		$("#modalAlerta .modal-body").html(modal);
		$("#modalAlerta .modal-title").html('Remover Pedido');
		$("#modalAlerta").modal('show');
		var id = $(this).data('id');
		var thad = $(this);
		$("#btRemoveItem").click(function(){
			$.post(ROOT+'/painel/roteirizador/rota/manual/remover/item',
				{
					id:id
				},
				function(dados){
					$(thad).parents('.block-pedido-manual').remove();
					var checkeds = $(".sucesso .check:checked");
					var capacidade = $('.vei-capacidade');
					cargaTotal(checkeds, capacidade);
					desenhaRota(checkeds, latcli, lngcli);
					var marker = $(thad).data('p');
					$(marker).remove();
				}
			)
		})
	})
})

$(document).on('keyup', '.busca-pedido input', function(e){
	e.preventDefault()
	var valor = $(this).val();
	var campos = $('.pedido-manual-nome-ponto');
	$(campos).each(function(i){
		var valCampo = $(campos[i]).html().toUpperCase()
		if(valCampo.indexOf(valor.toUpperCase()) != -1) {
			$(campos[i]).parents('.block-pedido-manual').show()
		}else{
			$(campos[i]).parents('.block-pedido-manual').hide()
		}
	})
})

$(document).on('keyup', '.busca-veiculos input', function(e){
	e.preventDefault();
	var valor = $(this).val();
	var campos = $('.item-placa');
	$(campos).each(function(i){
		var valCampo = $(campos[i]).html().toUpperCase()
		if(valCampo.indexOf(valor.toUpperCase()) != -1) {
			$(campos[i]).parents('.block-veiculo-rota').show()
		}else{
			$(campos[i]).parents('.block-veiculo-rota').hide()
		}
	})
})

$(".bt-confirma-rota").click(function(e){
	e.preventDefault()
	var ped = '';
	var pedido = $('.block-pedido-manual.alerta');
	var dataRoteirizacao = $('#dataRoteirizacao').val();
	var cliente = $('.select-empresa-rota-manual').val();

	if($(pedido).length > 0) {
		$(pedido).each(function(i){
			e.preventDefault();
			ped += '<span class="nao-roteirizados"><strong>'+$(pedido[i]).find('.pedido-manual-nome-ponto').html()+'</strong></span><br />'
		})
		var modal = '<span class="modal-subcarga">Os seguintes pedidos ainda não foram roteirizados: <br /> '+ped+'Deseja continuar mesmo assim?</span>'
		var bts = '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>';
		bts += '<button type="button" type="submit" data-dismiss="modal" id="btConfirmaCarga" class="btn btn-primary  bt-modal-desable">Continuar</button>';
		$("#modalAlerta .modal-footer").html(bts)
		$("#modalAlerta .modal-body").html(modal);
		$("#modalAlerta .modal-title").html('Confirmar Rotas');
		$("#modalAlerta").modal('show');

		$(document).on('click', '#modalAlerta #btConfirmaCarga', function(){
			ajaxRotas()
			window.location.assign(ROOT+'/painel/roteirizador/finalizacao/rota?data='+dataRoteirizacao+'&cliente='+cliente+'&buscar=true');
		})
	}else{
		ajaxRotas()
		window.location.assign(ROOT+'/painel/roteirizador/finalizacao/rota?data='+dataRoteirizacao+'&cliente='+cliente+'&buscar=true');
	}
})

$(".mostrar-painel").click(function(e){
	e.preventDefault();
	if($(this).parents('.block-roteirizados').hasClass('mostrar')) {
		$(this).parents('.block-roteirizados').removeClass('mostrar');
	}else{
		$(this).parents('.block-roteirizados').addClass('mostrar');
	}
})

$(".bt-imprime-rota").click(function(e){
	$(this).find('span').removeClass('fa-print').addClass('fa-spinner fa-spin fa-3x fa-fw');
	var thad = $(this);
	e.preventDefault();
	var dia = $('#dataRoteirizacao').val();
		$.post(ROOT+'/painel/relatorios/rotas/relatorio',
		{
			data:dia,
			tipo:"pdf"
		},function(data){
			window.open(ROOT+'/'+data.file.original.dados);
			$(thad).find('span').removeClass('fa-spinner fa-spin fa-3x fa-fw').addClass('fa-print');
		}
	)
})

function ajaxRotas(){
	var rotasId = new Array();
	$('.bt-carr-placa').each(function(index,element){
		console.log('okkkkkk ')
		var codigorota = $(element).data('idrota')
		console.log('cod',codigorota)
		rotasId.push(codigorota)
	})

	console.log('rotascodigo ',rotasId)
	$.ajax({
		url : ROOT+'/painel/roteirizador/rota/manual/update/statusRota',
		type : 'post',
		data : {idsRota : rotasId},
		dataType : 'json',
		success : function(data){
			console.log('sucesso ',data)

		},
		error : function(data){
			console.log('error ')
		}
	})
}

function itensRota(placa, posicaoPontos, itens, cli) {
    var inicio = $(inicioRota).val()
    var laloInicio = { 'polatitude': $(inicioRota).data('lat'), 'polongitude': $(inicioRota).data('lng') };
    var laloFim = { 'polatitude':$(fimRota).data('lat'), 'polongitude': $(fimRota).data('lng') };
    var fim = $(fimRota).val();
    $.post(ROOT+'/painel/roteirizador/rota/manual/itens/rota',
        {
            placa:placa,
            posicaoPontos:posicaoPontos,
            itens:itens,
            cli:cli,
            inicio:inicio,
            fim:fim,
            laloFim:laloFim,
            laloInicio:laloInicio,
            color: geraCor()
        },
        function(dados){
            $('#btGerarRotaManual').trigger('click');
        }
    )
}

function salvaItemRota(thad) {
	var form = $(thad).parents('form');
	$(form).ajaxForm({
		type:'post',
		success: function(dados) {
			console.log(dados)
			var mensagem = dados.mensagem
			var msg = '';
			$(".message-rota-men").remove();
			if(typeof mensagem != 'undefined') {
				msg += '<div class="alert alert-warning center-block message-rota-men" role="alert">'
				msg += '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>'
				msg += '<strong>Atenção! '+mensagem+'</strong>'
				msg += '</div>'
				$('.messagens').prepend(msg)
				return;
			}
			var item = dados.item;
			var ponto = dados.ponto;
			var reticencias = ponto.podescricao.length > 15 ? "..." : "";
			console.log(ponto.podescricao.length)
			itens = '<div class="col-sm-12 block-pedido-manual alerta">'+
                    '<div class="head-pedido-manual">'+
                        '<span class="pedido-manual-tipo-ponto">'+ponto.potipo+'</span>'+
                        '<span class="pedido-manual-nome-ponto">'+ponto.podescricao.substr(0, 15)+''+reticencias+'</span>'+
                        '<div class="block-pedido-carregar">'+
            			'<input title="Selecionar pedido" type="checkbox" data-rota=".'+ponto.pocodigo+'" data-id="'+item.ircodigo+'" data-lat="'+ponto.polatitude+'" data-long="'+ponto.polongitude+'" data-peso="'+item.irpeso+'" class="check" name="" id="'+ponto.pocodigo+'">'+
                        '</div>'+
                        '<div class="block-pedido-carregar">'+
                        	'<a href="#" data-p=".'+item.pocodigo+'" data-id="'+item.ircodigo+'" class="remove-pedido"><span class="fa fa-minus" /></a>'+
                        '</div>'+
                    '</div>'+
                    '<div class="block-editar-pedigo">'+
                        '<div class="inputs-editar ip-pedigo">'+
                            '<label for="">Pedido</label>'+
                            '<input title="Duplo clique para editar" type="text" readonly value="'+item.irdocumento+'" name="" data-camp="irdocumento" data-id="'+item.ircodigo+'" class="form-control ips-pedidos">'+
                        '</div>'+
                        '<div class="inputs-editar ip-valor">'+
                            '<label for="">Valor</label>'+
                            '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item.irvalor+'" data-camp="irvalor" data-id="'+item.ircodigo+'" class="form-control ips-pedidos">'+
                        '</div>'+
                        '<div class="inputs-editar ip-volumes">'+
                            '<label for="">Vol.</label>'+
                            '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item.irqtde+'" data-camp="irqtde" data-id="'+item.ircodigo+'" class="form-control ips-pedidos">'+
                        '</div>'+
                        '<div class="inputs-editar ip-cubagem">'+
                            '<label for="">Cubagem</label>'+
                            '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item.ircubagem+'" data-camp="ircubagem" data-id="'+item.ircodigo+'" class="form-control ips-pedidos">'+
                        '</div>'+
                        '<div class="inputs-editar ip-quilos">'+
                            '<label for="">Quilos</label>'+
                            '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item.irpeso+'" data-camp="irpeso" data-id="'+item.ircodigo+'" class="form-control ips-pedidos">'+
                        '</div>'+
                        '<div class="inputs-editar ip-data">'+
                            '<label for="">Data</label>'+
                            '<input title="Duplo clique para editar" type="text" name="" readonly value="'+item.irdata+'" data-camp="irdata" data-id="'+item.ircodigo+'" class="form-control ips-pedidos">'+
                        '</div>'+
                        '<div class="inputs-editar ip-agendamento">'+
                            '<label for="">Agen.</label>'+
                            '<input title="Duplo clique para editar" type="text" name="" id="" disabled class="form-control">'+
                        '</div>'+
                    '</div>'+
                '</div>'
            $('.nada-encontrado').remove()
            $(".pedidos-rota").prepend(itens)
    //         $(".pedido-manual-contagem").each(function(id){
    //         		var id = id + 1;
				// 	$(this).html(id);
				// });


            var icone = new  L.divIcon({
                className: "divIcon",
                html: "<div id='myIcon"+i+"' class='alerta myIcon "+ponto.pocodigo+"'><span class='sp-icon fa fa-map-marker'></span></div>",
                iconSize:     [22, 35], // size of the icon
			    shadowSize:   [50, 64], // size of the shadow
			    iconAnchor:   [10, 30], // point of the icon which will correspond to marker's location
			    shadowAnchor: [0, 0],  // the same for the shadow
			    popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
            });

            pt = new L.marker([ponto.polatitude,ponto.polongitude],{icon: icone});
            ptR = L.circle([ponto.polatitude,ponto.polongitude],{radius: ponto.poraio});
            ptR.addTo(mapa);
            link = '<span> Descrição: '+ponto.podescricao+'</span><br />';
            pt.bindPopup(link).addTo(mapa);
		}
	}).submit();
}
