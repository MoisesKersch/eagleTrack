$(document).ready(function(){
	var clickMontaRota = [];
    var cadastroRegiao = $("#divCadastroRegiao");
    var polygon;
    var allPolygon = [];
    var cliente;
    var clickOnRegiao = false;
    var corRegiao = $('input[name=recor]');
    var regiaoSalva;
    var filtroCliente;
    var carregandoRegioes = false;
    var matriz;

    cadastroRegiao.each(function(index, element){
        var elem = $(element);
        var novaRegiao = elem.find('#buttonNovaRegiao');
        var btnSalvarRegiao = elem.find('#salvarRegiao');
        var cancelarRegiao = elem.find('#cancelarRegiao');
        var descricao = elem.find('input[name=redescricao]');
        var velocidade = elem.find('input[name=revelocidade]');
        filtroCliente = elem.find('select[name=clientesbusca]');
        cliente = elem.find('select[name=recliente]');
        var longitude = elem.find('#longitudeClienteRegiao');
        var latitute = elem.find('#latitudeClienteRegiao');
        // carregaMapaRegioes($(latitute).val(), $(longitude).val());

        var carregar = elem.find("#mapaRegiao");
		$(carregar).each(function(idx, element){
			carregaMapaRegioes($(latitute).val(), $(longitude).val());
		});

        $(filtroCliente).change(function() {
        	if (!carregandoRegioes) {
        		$('#buttonNovaRegiao').addClass('disabled');
        		carregandoRegioes = true;
	        	var clienteSelecionado = $(this).find('option:selected');
	        	$(cliente).html('<option value="'+clienteSelecionado.val()+'">'+clienteSelecionado.text()+'</option>');

	        	//retorna e carrega as regioes no mapa
	        	buscaRegioes(clienteSelecionado.val());
        	}
        });

        $(filtroCliente).trigger('change');

        $(novaRegiao).tooltip({
        	'placement': 'down',
        	'title': 'Aguarde! Carregando as regiões.'
        });
        $(novaRegiao).tooltip('show');

        //nova região
        $(novaRegiao).click(function() {
        	$(this).hide();
        	$('#sectionFiltrosRegiao').hide();
        	$('#sectionNovaRegiao').show();
        	$('#sectionNovaRegiaoSalvar').show();
        	$('.divMapaRegioes > label').html('Clique no mapa para formar a região');
        	criarRegiao($(corRegiao).val());
        });

        //salvar regiao
        $(btnSalvarRegiao).click(function() {
        	$('.has-error').removeClass('has-error');
        	$('.help-block').remove();
        	$('.mapa-regioes').css('border', '0');

        	var descricaoVal = $(descricao).val();
        	var velocidadeVal = $(velocidade).val();

        	var params = {
        		'redescricao': descricaoVal,
        		'revelocidade': velocidadeVal,
        		'recor': $(corRegiao).val(),
        		'recliente': $(cliente).val(),
        		'recoordenadas': JSON.stringify(clickMontaRota)
        	};

        	salvarRegiao(params);
        	var posicaoRegiao = (allPolygon.push(polygon))-1;
        	setTimeout(function() {
	        	var velocidadeDiv = velocidadeVal ? '<div> Velocidade: '+velocidadeVal+' km/h</div>' : '';
	        	var popupRegiao =
					'<div style="display: table; min-width: 90px;">'
						+'<div><strong>'+descricaoVal+'</strong></div>'
						+(velocidadeDiv.split('.'))[0]
						+'<hr style="margin-top: 0px;margin-bottom: 5px;">'
						+'<div>';
							if($('#ppexcluir').data('permissao')){
								popupRegiao+='<div style="position: relative; float: right;">'
									+'<button data-toggle="modal" data-target="#modalDeleta" type="button" data-posicao-regiao="'+posicaoRegiao+'" data-regiao="'+regiaoSalva+'" class="btDelModal btn btn-danger btn-xs exclui-regiao" title="Exclui permanentemente a região">Excluir</button>'
								+'</div>';
							}
						popupRegiao+='</div>'
					+'</div>';

				var thisPosicaoRegiao;

				//popup regiao
				polygon.on('click', function(event) {
					setTimeout(function() {
						$('.exclui-regiao').click(function() {
				    		$('#btnDelModal').addClass('btn-deleta-regiao');
							$('.btn-deleta-regiao').click(function() {
								var idRegiao = $('.exclui-regiao').data('regiao');
					    		thisPosicaoRegiao = $('.exclui-regiao').data('posicao-regiao');
						    	$.ajax({
						    		url: ROOT+'/painel/cadastros/regioes/excluir',
						    		type: 'post',
						    		data: {'id': idRegiao},
						    		success: function(retorno) {
						    			if (retorno == 'true') {
					    					allPolygon[thisPosicaoRegiao].closePopup().remove();
						    			}
						    		}
						    	});
							})
				    	});
					}, 200);
				}).bindPopup(popupRegiao);
			}, 1000);
        });

        //cancela nova/editar regiao
        $(cancelarRegiao).click(function() {
        	cancelaRegiao();
        	polygon.remove();
        });

        // mapa.on('click', function() {

        // });

        //mapa._layers retorna os objetos no mapa - todos
    });

    function cancelaRegiao() {
    	$('.has-error').removeClass('has-error');
    	$('.help-block').remove();
    	$('#mapaRegiao').css('border', '0');
    	$('#sectionNovaRegiao').hide();
    	$('#sectionNovaRegiaoSalvar').hide();
    	$('#sectionFiltrosRegiao').show();
    	$('#buttonNovaRegiao').show();
    	$('#sectionNovaRegiao input').val('').attr("readonly", false);
    	$('.divMapaRegioes > label').html('Listagem das regiões');
    	$('#salvarRegiao').removeClass('btn-warning disabled').addClass('btn-primary')
    		.html('<span class="glyphicon glyphicon-ok"></span> Salvar');
    	$('#salvarRegiao').css({'display': 'block', 'float': 'left', 'margin-right': '5px'});
    	$('.fa-spinner-tmp').remove();

    	mapa.off('click');
    	mapa.off('contextmenu');
    	clickMontaRota = [];
	}

	function criarRegiao(cor) {
		if (allPolygon.length > 0) {
			for (var poly in allPolygon) {
				allPolygon[poly].on('click', function() {
					clickOnRegiao = true;
					// $(this).find('.removerAoEditar').addClass('disabled');
				});
			}
		}

		polygon = L.polygon([], {
					color: cor.toString(),
					opacity: 0.3,
					fill: true,
					fillOpacity: 0.2,
					allowIntersection: false,
					className: 'regioesRemover'
				}).addTo(mapa);

		mapa.on('click', function(ev) {
		    if (clickOnRegiao == false) {
		    	clickMontaRota.push(ev.latlng);
		    	polygon.addLatLng(ev.latlng);
		    	return;
		    }

		    clickOnRegiao = false;

		    //padrao do traço

			//move até o click
			// mapa.flyTo(ev.latlng);
		});

		mapa.on('contextmenu', function() {
			clickMontaRota.pop();
			polygon.setLatLngs(clickMontaRota);
		});

		$('input[name=recor]').change(function() {
			polygon.setStyle({color: $(this).val().toString()})
		})
	}

	function salvarRegiao(params) {
		$.ajax({
	        url:ROOT+'/painel/cadastros/regioes/cadastrar',
	        type: 'post',
	        // dataType: 'json',
	        data: {'params': params},
	        success: function(retorno) {
	        	if (retorno.hasOwnProperty('erro')) return trataErrosValidacao(retorno.erro);

	        	cancelaRegiao();
	        	regiaoSalva = retorno;
	        }
      	});
	}

	function trataErrosValidacao(erros) {
		Object.keys(erros).forEach(function(item){
			if (item == 'recoordenadas') {
				$('#mapaRegiao').css('border', '1px solid red');
				$('.mapa-regioes').parent()
					.append('<p class="help-block text-danger">É necessário uma região com pelo menos 3 pontas.</p>');
			}
			if (item == 'redescricao') erros[item] = 'O campo descrição é obrigatório.';
			if (item == 'recliente') erros[item] = 'O campo empresa é obrigatório.';
			$('input[name='+item+']').parent().addClass('has-error')
				.append('<p class="help-block">'+erros[item]+'</p>');
		});

		$('input').attr('readonly', false);
		$('#salvarRegiao').removeClass('btn-warning disabled').addClass('btn-primary')
			.html('<span class="glyphicon glyphicon-ok"></span> Salvar').blur()
			.css({'display': 'block', 'float': 'left', 'margin-right': '5px'});
    	$('.fa-spinner-tmp').remove();
    }
	function buscaRegioes(paramCliente) {
		mapa.closePopup();
		$('.regioesRemover').remove();
		allPolygon = [];

		$.ajax({
			url: ROOT+'/painel/cadastros/regioes/buscaRegioes',
			type: 'post',
			dataType: 'json',
			data: {'cliente': $(cliente).val()},
			success: function(retorno) {
				if (carregandoRegioes) {
					$(filtroCliente).val(paramCliente);
					$(filtroCliente).select2({
						"language": "pt-BR",
    					allowClear: false
					});
				}
				carregandoRegioes = false;
				montaRegioes(retorno);
			}
		});
	}

	function montaRegioes(regioes) {
		if (typeof(regioes[0]) != 'undefined' && typeof(regioes[0].cllatitude) != 'undefined') {
			var iconMatriz = L.icon({
				        iconUrl: ROOT+'/img/matriz.png',
				        iconSize: [50,50]
				    });
			if (typeof(matriz) != 'undefined') matriz.remove();
			var position = [regioes[0].cllatitude, regioes[0].cllongitude];
			    matriz = L.marker(position,{
				        title: 'Matriz',
				        icon: iconMatriz
				    });

			    matriz.addTo(mapa);
			    mapa.flyTo(position);
		} else {
			matriz.remove();
		}

		$('#buttonNovaRegiao').removeClass('disabled');
		$('#buttonNovaRegiao').tooltip('destroy');
		if (typeof regioes[0].recor == 'undefined') return;

		for (var i in regioes) {
			var coordenadas = regioes[i].regioes_coordenadas;
			var coordenadasArray = [];
			for (var y in coordenadas) {
				coordenadasArray.push([coordenadas[y].rclatitude, coordenadas[y].rclongitude]);
			}

			var polygon = L.polygon(
				coordenadasArray,
				{
					color: regioes[i].recor.toString(),
					opacity: 0.3,
					fill: true,
					fillOpacity: 0.2,
					allowIntersection: false,
					className: 'regioesRemover'
				}
			).addTo(mapa);
			var posicaoRegiao = (allPolygon.push(polygon))-1;
			var velocidade = regioes[i].revelocidade ? '<div> Velocidade: '+(regioes[i].revelocidade.split('.'))[0]+' km/h</div>' : '';
			var botaoEditar =
				'<div style="position: relative; float: left;">'
					+'<button type="button" class="btn btn-info btn-xs edita-regiao">Editar</button>'
				+'</div>';
			var popupRegiao =
			allPolygon.push(polygon);

			var popupRegiao =
				'<div style="display: table; min-width: 90px;">'
					+'<div><strong>'+regioes[i].redescricao+'</strong></div>'
					+velocidade
					+'<hr style="margin-top: 0px;margin-bottom: 5px;">'
					+'<div style="align-text: center" class="removerAoEditar">';
						// +botaoEditar
						if($('#ppexcluir').data('permissao')){
							popupRegiao += '<div style="position: relative; float: right;">'
								+'<button data-toggle="modal" data-target="#modalDeleta" type="button" data-posicao-regiao="'+posicaoRegiao+'" data-regiao="'+regioes[i].recodigo+'" class="btDelModal btn btn-danger btn-xs exclui-regiao" title="Exclui permanentemente a região">Excluir</button>'
							+'</div>';
						}
					popupRegiao += '</div>';

			var thisPosicaoRegiao;
			+'</div>';

			polygon.on('click', function(event) {
				setTimeout(function() {
					$('.exclui-regiao').click(function() {
						$('#btnDelModal').addClass('btn-deleta-regiao');
						$('.btn-deleta-regiao').click(function() {
							var idRegiao = $('.exclui-regiao').data('regiao');
				    		thisPosicaoRegiao = $('.exclui-regiao').data('posicao-regiao');
					    	$.ajax({
					    		url: ROOT+'/painel/cadastros/regioes/excluir',
					    		type: 'post',
					    		// dataType: 'json',
					    		data: {'id': idRegiao},
					    		success: function(retorno) {
					    			if (retorno == 'true') {
				    					allPolygon[thisPosicaoRegiao].closePopup().remove();
					    			}
					    		}
					    	});
						})
		    		});
				}, 200);
			}).bindPopup(popupRegiao);
		}

		// $('#buttonNovaRegiao').tooltip('destroy');
		// $('#buttonNovaRegiao').removeClass('disabled');
	}

	function carregaMapaRegioes(CLLATITUDE, CLLONGITUDE){
	    if (CLLATITUDE && typeof(CLLATITUDE) != 'undefined' && CLLATITUDE.length != 0) {
	        position = [CLLATITUDE,CLLONGITUDE];
	        //marker matriz
		    var iconMatriz = L.icon({
		        iconUrl: ROOT+'/img/matriz.png',
		        iconSize: [50,50]
		    });
		    matriz = L.marker(position,{
		        title: 'Matriz',
		        icon: iconMatriz
		    });

		    matriz.addTo(mapa);
	    } else {
	    	position = [-27.099203, -52.626327];
	    }

	    // else
	    //     position = [-27.099203, -52.626327];

	    var attribution = '&copy;<a href="http://maps.google.com">Google Maps</a>';

	    var googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {attribution: attribution, maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3']}),
	        satelliteGoogle = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {attribution: attribution, maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3']}),
	        detalhado = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {attribution: attribution});

	    var baseLayers = {
	          "Padrão": googleStreets,
	          "Satélite": satelliteGoogle,
	          "Detalhado": detalhado
	      };

	    mapa = L.map('mapaRegiao', {
	          center: position,
	          zoom: 13,
	          layers: [googleStreets]
	      });
	    mapa.zoomControl.setPosition("bottomright");
	    L.control.layers(baseLayers,null,{
	        position: 'bottomright'
	    }).addTo(mapa);

	}

	// function editaRegiao(regiao) {
 //    	$('#sectionFiltrosRegiao').hide();
 //    	$('#sectionNovaRegiao').show();
 //    	$('#sectionNovaRegiaoSalvar').show();
 //    	$('.divMapaRegioes > label').html('Editar região');

 //    	mapa.on('click', function(ev) {
	// 	    if (clickOnRegiao == false) {
	// 	    	clickMontaRota.push(ev.latlng);
	// 	    	polygon.addLatLng(ev.latlng);
	// 	    	return;
	// 	    }

	// 	    clickOnRegiao = false;

	// 	    //padrao do traço

	// 		//move até o click
	// 		// mapa.flyTo(ev.latlng);
	// 	});

	// 	mapa.on('contextmenu', function() {
	// 		clickMontaRota.pop();
	// 		polygon.setLatLngs(clickMontaRota);
	// 	});
	// }

// var latlngs = [
//     [['45.51', '-122.68'],
//      ['37.77', '-122.43'],
//      ['34.04', '-118.2']],
//     [[40.78, -73.91],
//      [41.83, -87.62],
//      [32.76, -96.72]]
// ];

// var polygon = L.polygon(
// 	latlngs,
// 	{color: 'red'}
// ).addTo(mapa);

// // zoom the map to the polygon
// mapa.fitBounds(polygon.getBounds());


})
