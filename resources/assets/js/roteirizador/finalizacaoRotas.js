$(document).ready(function() {
	$('#sectionConfirmarRotasVeiculos').hide();
 	var finaliza = $("#finalizaRota");
  	finaliza.each(function(idx, element){
	    carregaMapa();
  	})
  	var rotas = [];
    var markers = [];
    var regs = [];

    function tootltipBtnMesclaRota (id) {
    	$(id).tooltip('destroy');
        $(id).tooltip({
        	placement: 'top',
        	title: 'É necessário ter duas ou mais rotas para agrupar',
        	container: 'body'
        });
        $(id).tooltip('show');
    }

    tootltipBtnMesclaRota('#btnMesclaRota');

  	$('#submitFinalizaRota').on('click', function() {
  		finalizaçãoRotaAjaxForm();
  	});

	function formataTempo(segundos) {
		var tempo = moment().startOf('day')
	        .seconds(parseFloat(segundos))
	        .format('HH:mm:ss');

		return tempo;
	}

  	function finalizaçãoRotaAjaxForm() {
  		var polygonn = [];
        var allPolygon = [];

        $.ajax({
        	url: ROOT+'/painel/roteirizador/finalizacao/rota/regioes',
        	data: {
        		'clientes': $('#finalizacaoClientes').val(),
        		'data_saida': $('#dataRoteirizacao').val()
        	},
        	method: 'post',
			success: function(dados) {
				if (dados.rotas.length < 2) {
					$('#btnMesclaRota').prop('disabled', true);
					$('.bt-imprimir-rotas').attr('disabled', true);
					tootltipBtnMesclaRota('#btnMesclaRota');
				} else {
					$('#btnMesclaRota').prop('disabled', false).tooltip('destroy');
					$('.bt-imprimir-rotas').attr('disabled', false);
				}

                $('.mapa-finalizacao-rota').css('height','400px');

                // clearMap
                for (var i in regs) { regs[i].remove();}
                for (var i in markers) { markers[i].remove();}
                markers = [];
                regs = [];
                var acoes2 = '';

				var regioes = dados.regioes;
				rotas = dados.rotas;

                $("#tableFinalizacao tbody").html("");

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

                    var texto = `<span> `+regioes[r].redescricao+` </span><br />`;
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
				    ).bindPopup(texto).addTo(mapa);
                    regs.push(polygonn);
				    var posicaoRegiao = (allPolygon.push(polygonn))-1;
				    var velocidade = regioes[r].revelocidade ? '<div> Velocidade: '+(regioes[r].revelocidade.split('.'))[0]+' km/h</div>' : '';
				    var botaoEditar =
				    	'<div style="position: relative; float: left;">'
				    		+'<button type="button" class="btn btn-info btn-xs edita-regiao">Editar</button>'
				    	+'</div>';
				    var popupRegiao =
				    allPolygon.push(polygonn);
	 			}

                var tr = '';
	 			for(i in rotas){
                    var acoes = `<a class='col-md-3'><span value='`+rotas[i].rocodigo+`' class='bt-imprime-rota-fn fa fa-print'></span></a>
                                <a class='col-md-3'><span data-placa="`+rotas[i].veiculo.veplaca+`" data-id='`+rotas[i].rocodigo+`' class='fa fa-trash-o remover-rota'></span></a>
                                <a class='col-md-3'><input data-id='`+rotas[i].rocodigo+`' class='check-imprime-rota check-mesclagem-rota check-hidden-pontos' type='checkbox' checked ></input></a>`;

                    if (rotas[i].rocor == undefined) {
                        var cor = geraCor();
                        salvaCorRota(rotas[i].rocodigo,cor);
                    } else {
                        var cor = rotas[i].rocor;
                    }

                    // carregar itens de rota no mapa
                    for (y in rotas[i].itens_rota) {
                        var item = rotas[i].itens_rota[y];
                        markerItemRota(item, cor);
                    }

                    var status_rota = defineTableLine(rotas[i].ropeso,rotas[i].veiculo.vemaxpeso, rotas[i].rocubagem, rotas[i].veiculo.vecubagem);
                    var kms = parseFloat((rotas[i].rokm/1000).toFixed(2));
                    var custo = parseFloat(rotas[i].veiculo.vecusto) * kms;
                    var cubagem = parseFloat(rotas[i].rocubagem).toFixed(2);

                    acoes2 = '<div class="ocultarAcoesMRDois col-md-11">'+
                    			'<input class="check check-mesclagem-rota check-mesclagem-confirm" data-rota="'+rotas[i].rocodigo+'" value="'+i+'" type="checkbox"></input>'
                    		'</div>';

	 				tr =
                    // <td style='background-color: `+cor+`;' >`+cor+`</td>
                     `<tr class="stl-table to-hidden `+status_rota+` ">
                        <td title="Clique para alterar a cor da rota" ><input type="color" class='form-control imput_color imput-color-`+rotas[i].rocodigo+`' name="favcolor" value="`+cor+`" data-id='`+rotas[i].rocodigo+`'></td>
                        <td class='serch' >`+rotas[i].rocodigo+`</td>
                        <td class='serch' >`+rotas[i].roplaca+`</td>
                        <td class='serch' >`+rotas[i].veiculo.veprefixo+`</td>
                        <td class='serch' >`+rotas[i].ropeso+`Kg</td>
                        <td class='serch' >`+cubagem+`</td>
                        <td class='serch' >`+rotas[i].roqtde+`</td>
                        <td class='serch' >`+kms+` Km</td>
                        <td class='serch' >`+(rotas[i].rotemposegundos ? formataTempo(rotas[i].rotemposegundos) : rotas[i].rotempo)+`</td>
                        <td class='serch' >R$`+String(parseFloat(rotas[i].rovalor).toFixed(2)).replace('.',',')+`</td>
                        <td class='serch' >R$`+String(parseFloat(custo).toFixed(2)).replace('.',',')+`</td>
                        <td class="nopadding serch nomargin checked" ><div class="nomargin nopadding col-sm-12 ocultarAcoesMR">`+acoes+`</div>`+acoes2+`</td>
                    </tr>`;

                    $("#tableFinalizacao tbody").append(tr);

                    // carregar itens de rota no mapa
                    // for (y in rotas[i].itens_rota) {
                    //     var item = rotas[i].itens_rota[y];
                    //     markerItemRota(item);
                    // }
	 			}

	 			$('.check-mesclagem-confirm').click(function() {
			    	var count = 0;
			    	$('.check-mesclagem-confirm').each(function(i, e) {
			    		if ($(e).is(':checked')) count++;
			    	});

			    	if (count < 2) {
			    		$('.bt-mesclar-rotas-confirmacao').attr('disabled', true);
			    		tootltipBtnMesclaRota('.bt-mesclar-rotas-confirmacao');
			    	} else {
			    		$('.bt-mesclar-rotas-confirmacao').attr('disabled', false);
			    		$('.bt-mesclar-rotas-confirmacao').tooltip('destroy');
			    	}

			    });

			    $('.check-hidden-pontos').click(function() {
			    	var id = $(this).data('id');
			    	if (!$(this).is(':checked')) {
			    		$('.icon-iten-'+id).parent().hide();
			    		mapa.closePopup();
			    	}
			    	else
			    		$('.icon-iten-'+id).parent().show();
			    });

		        $('.imput_color').on('change',function(){
		            salvaCorRota($(this).data('id'),$(this).val());
		            // alterar as cores dos icones
		            $('.item-rota-fn-'+$(this).data('id')).css('color', $(this).val());

		        });

	 			// modalMesclarRotas();
			},
			error: function(request, status, error) {
				// console.log(error)
			}
  		});
  	}

  	if ($('#submitFinalizaRota').data('busca')) $('#submitFinalizaRota').click();

	modalMesclarRotas();

    function salvaCorRota(id_rota, cor){
        $.post(ROOT+'/painel/roteirizador/finalizacao/rota/alterar/cor',
        {
            id: id_rota,
            cor: cor
        },
        function(data, status){
        });
    }

    function markerItemRota(item, corParam) {
        var cor = $('.imput-color-'+item.irrota).val();
        if (corParam)
        	cor = corParam;
        var ht = `<div id='markerItemRota`+item.ircodigo+`' class='item-rota-fn icon-iten-`+item.irrota+` '>
                    <span style='color: `+cor+`'  class='item-rota-fn-`+item.irrota+` icon-iten fa fa-map-marker'>
                        <span style="font-family: 'Open Sans', sans-serif;" class='item-ico-num item-numero-fn-`+item.irordem+`' >`+item.irordem+`</span>
                    </span></div>`;
        var icone = new  L.divIcon({
            className: "divIcon",
            html: ht,
            iconSize:     [22, 35], // size of the icon
            shadowSize:   [50, 64], // size of the shadow
            iconAnchor:   [20, 50], // point of the icon which will correspond to marker's location
            shadowAnchor: [0, 0],  // the same for the shadow
            popupAnchor:  [-5, -35] // point from which the popup should open relative to the iconAnchor
        });

        var marker = new L.marker([item.ponto.polatitude,item.ponto.polongitude],{icon: icone});
        var texto = `<span> Descrição: `+item.irnome+`</span><br />
                     <span> Volumes: `+item.irqtde+`</span><br />
                     <span> Valor: R$`+String(parseFloat(item.irvalor).toFixed(2)).replace('.',',')+`</span><br />
                    `;
        marker.bindPopup(texto).addTo(mapa);

        markers.push(marker);
    }

    function defineTableLine(pesoRota,maxPesoVeic,cubagemRota, cubagemV) {
        var status_rota = '';
        var percentagem = (pesoRota/maxPesoVeic) * 100;
        var cubagem = (cubagemRota/cubagemV) * 100;

        if ((percentagem >= 70 || cubagem >= 70) && (percentagem < 90 || cubagem < 90)) {
            status_rota = ' alerta ';
        } else {
            status_rota = ' padao ';
        }
        if (percentagem >= 90 || cubagem >= 90) {
            status_rota = ' sucesso ';
        }
        if (percentagem > 100 || cubagem > 100) {
            status_rota = ' perigo ';
        }

        return status_rota;
    }

  	function modalMesclarRotas() {
	  	$('.bt-mesclar-rotas-confirmacao').click(function() {
	  		setTimeout(function() {
	  			$('.modal-title').html('Confirmação de Agrupamento de Rotas');//titulo
	  			var table = '';
	  			var tr = '';
	  			var index = [];
	  			var peso = 0;
	  			var cubagem = 0;
	  			var volume = 0;
	  			var valor = 0;
	  			var veiculos = '';
	  			var codRota = [];
	  			var rotaVeiculo = null;
	  			$('.check').each(function() {
	  				if ($(this).is(':checked')) {
	  					index.push($(this).val());
	  					codRota.push($(this).data('rota'));
	  				}
	  			});

	  			for (var i in index) {
	  				// var custo = rotas[i].veiculo.vecusto * rotas[i].rokm;
					tr +=
                    '<tr>'+
		 				'<td>'+rotas[index[i]].rocodigo+'</td>'+
		 				'<td>'+rotas[index[i]].roplaca+'</td>'+
		 				'<td>'+parseFloat(rotas[index[i]].ropeso).toFixed(2)+'</td>'+
	  					'<td>'+parseFloat(rotas[index[i]].rocubagem).toFixed(2)+'</td>'+
		 				'<td>'+rotas[index[i]].roqtde+'</td>'+
	 				'</tr> ';

	 				peso += parseFloat(rotas[index[i]].ropeso);
	 				cubagem += parseFloat(rotas[index[i]].rocubagem);
	 				volume += parseFloat(rotas[index[i]].roqtde);
	 				valor += parseFloat(rotas[i].rovalor);
	  			}

	  			$.ajax({
	  				url: ROOT+'/painel/roteirizador/finalizacao/rota/mesclagemVeiculosCapacitados',
	  				method: 'post',
	  				data: {
	  					'cliente': $('#finalizacaoClientes').val(),
	  					'data': $('#dataRoteirizacao').val()
	  				},
	  				success: function(retorno) {
	  					var pontos = retorno.pontos;
	  					var selectPontos = '<select>';
	  					for (var x in pontos) {
	  						var value = pontos[x].pocodigo+'|'+pontos[x].polatitude+'|'+pontos[x].polongitude;
							selectPontos += '<option value="'+value+'">'+pontos[x].podescricao+'</option>';
	  					}
	  					selectPontos += '</select>';

	  					var result = retorno.veiculos;
	  					for (var y in result) {
	  						var pesov = parseFloat(result[y].vemaxpeso).toFixed(2);
	  						var cubagemv = parseFloat(result[y].vecubagem).toFixed(2);
	  						var pesovpedido = peso;
	  						var cubagemvpedido = cubagem;

	  						if (result[y].rocodigo && result[y].veplaca != result[y].roplaca) {
	  							pesovpedido = parseFloat(result[y].ropeso)+parseFloat(peso);
	  							cubagemvpedido = parseFloat(result[y].rocubagem)+parseFloat(cubagem);
	  						}

	  						var classtr = defineTableLine(pesovpedido, pesov, cubagemvpedido, cubagemv);
	  						var carregado = '';

	  						if (result[y].rocodigo) {
								var title = 'Veículo já possui uma rota!';
	  							carregado = '<span title="'+title+'" style="margin-left: 18px;" class="fa fa-check"></span>';
	  						}

	  						veiculos += '<tr class="'+classtr+'">'+
	  										'<td>'+result[y].veplaca+'</td>'+
	  										'<td>'+(isNaN(pesov) ? '' : pesov)+'</td>'+
	  										'<td>'+(isNaN(cubagemv) ? '' : cubagemv)+'</td>'+
	  										'<td>'+carregado+'</td>'+
	  										'<td><input data-peso="'+pesov+'" data-cubagem="'+cubagemv+'" data-rota="'+result[y].rocodigo+'" name="radioMesclagemVeiculo" value="'+result[y].veplaca+'" type="radio"></td>'+
	  									'</tr>';
	  					}

	  					var nenhumVeiculo = '<td style="text-align: center;padding: 20px;" colspan=4>Nenhum veículo encontrado!</td>';

  						$('.selectPontosMesclagemRotas').html(selectPontos);
  						$('.selectPontosMesclagemRotas2').html(selectPontos);
  						$('.selectPontosMesclagemRotas').select2({
  							language: "pt-BR",
    						allowClear: false,
  							dropdownParent: $('#modalAlerta .modal-body')
  						});
  						$('.selectPontosMesclagemRotas2').select2({
  							language: "pt-BR",
    						allowClear: false,
  							dropdownParent: $('#modalAlerta .modal-body')
  						});
  						$('.veiculos-disponiveis-mr').html(veiculos != '' ? veiculos : nenhumVeiculo);

						$('input:radio[name=radioMesclagemVeiculo]').change(function() { //aquichange
							$('.btn-footer-cmr button').attr('disabled', false);
							rotaVeiculo = $(this).data('rota');
							$('input:radio[name=radioMesclagemVeiculo]').tooltip('destroy');
							if ($(this).data('peso') < peso || $(this).data('cubagem') < cubagem) {
								$(this).tooltip({
									placement: 'right',
									title: 'Este veículo não suporta as rotas selecionadas!',
									container: 'body'
								});
								$(this).tooltip('show');
							}
						});

						$('.btn-footer-cmr #btnConfirmarMesclagem').popover({
							placement: 'left',
							content: 'Após a confirmação não será mais possível desfazer o agrupamento.',
							container: 'body'
						});
						$('.btn-footer-cmr #btnConfirmarMesclagem').popover('toggle');
						$(document).click(function() {
							$('.btn-footer-cmr #btnConfirmarMesclagem').popover('hide');
						});
	  				}
	  			});

	  			table += '<div id="divRotasSelecionadas">'+
			  				'<div class="divTitleRotasVeiculos">Rotas selecionadas:</div>'+
			  				'<div class="divTableMesclagemRotaSelecionado">'+
				  				'<table id="tableMesclagemRotaSelecionado" class="table table-container divTableOverflow">'+
				                    '<thead>'+
				                        '<tr>'+
				                            '<th>Código</th>'+
				                            '<th>Veículo</th>'+
				                            '<th>Peso</th>'+
				                            '<th>Cubagem</th>'+
				                            '<th>Volume</th>'+
				                        '</tr>'+
				                    '</thead>'+
				                    '<tbody class="scroll-container">'+
				                    	tr+
				                    '</tbody>'+
				                '</table>'+
				            '</div>'+
			                '<div>'+
			                	'<div class="col-md-12">Peso: '+parseFloat(peso).toFixed(2)+' Kg</div>'+
			                	'<div class="col-md-12">Cubagem: '+parseFloat(cubagem).toFixed(2)+'</div>'+
			                	'<div class="col-md-12">Volume: '+volume+'</div>'+
			                '</div>'+
			                '<div class="divTitleRotasVeiculos">Veículos disponíveis para agrupamento:</div>'+
		  					'<div class="divTableMesclagemRotaSelecionado">'+
				                '<table id="tableMesclagemVeiculoDisponivel" class="table table-container divTableOverflow">'+
				                    '<thead>'+
				                        '<tr>'+
				                            '<th>Veículo</th>'+
				                            '<th>Peso</th>'+
				                            '<th>Cubagem</th>'+
				                            '<th>Carregado</th>'+
				                            '<th></th>'+
				                        '</tr>'+
				                    '</thead>'+
				                    '<tbody class="scroll-container veiculos-disponiveis-mr">'+
				                    	'<tr><td>'+
				                    		'<span class="fa fa-spinner fa-spin fa-3x fa-fw"></span> Carregando...'+
				                    	'</td></tr>'+
				                    '</tbody>'+
				                '</table>'+
			  				'</div>'+
			  				'<div class="col-md-12">'+
			  					'<div class="col-md-6">'+
			  						'<label>Ponto de saída</label>'+
			  						'<select class="col-md-12 selectPontosMesclagemRotas">'+
			  						'</select>'+
			  					'</div>'+
			  					'<div class="col-md-6">'+
			  						'<label>Ponto de retorno</label>'+
			  						'<select class="col-md-12 selectPontosMesclagemRotas2">'+
			  						'</select>'+
			  					'</div>'+
			  				'</div>'+
			  			'</div>';

	            $('#modalAlerta .modal-body').html(table);

	  			var footer =
	  				'<div class="warning-confirmacao-mesclagem" role="alert">'+
	  					'<div>'+
	  						'<span class="fa fa-spinner fa-spin fa-3x fa-fw"></span> Agrupando...'+
		  				// 	'<button type="button" class="close" aria-label="Close">'+
		  				// 		'<span aria-hidden="true">&times;</span>'+
		  				// 	'</button>'+
		  				// 	'Após a confirmação não será mais possível desfazer a mesclagem.'+
		  				'</div>'+
	  				'</div>'+
					'<div class="btn-footer-cmr">'+
						'<button disabled id="btnConfirmarMesclagem" class="btn btn-success col-md-offset-2" type="button">'+
							'<span class="fa fa-check"></span> Confirmar'+
						'</button>'+
						'<button id="btnModalCancelar" class="btn btn-danger col-md-offset-2" type="button">'+
							'<span class="fa fa-times"></span> Cancelar'+
						'</button>'+
					'</div>';

				$('#modalAlerta .modal-footer').html(footer);

				$('.btn-danger').click(function() {
					$('#modalAlerta').modal('hide')
				});

				$('.close').click(function() {
					$(this).parent().remove();
				});

				$('#btnConfirmarMesclagem').click(function() {
					$(this).attr('disabled', true);
					if (rotaVeiculo)
						codRota.push(rotaVeiculo);
			  		ajaxConfirmaMesclaRota(codRota, peso, cubagem, volume, valor)
				});

	  		}, 500);
	  	});
  	}

  	function ajaxConfirmaMesclaRota(codRota, peso, cubagem, volume, valor) {
  		$('#btnModalCancelar').attr('disabled', true);
		$('.warning-confirmacao-mesclagem div').css('display', 'block');
		$.ajax({
			url: ROOT+'/painel/roteirizador/finalizacao/rota/mesclarRota',
			data: {
				'rota': codRota,
				'veiculo': $('input:radio[name=radioMesclagemVeiculo]:checked').val(),
				'partida': $('.selectPontosMesclagemRotas').val(),
				'retorno': $('.selectPontosMesclagemRotas2').val(),
				'data': $('#dataRoteirizacao').val(),
				'cliente': $('#finalizacaoClientes').val(),
				'peso': peso,
				'cubagem': cubagem,
				'volume': volume,
				'valor': parseFloat(valor)
			},
			method: 'post',
			success: function(data) {
				if (data['mensagem']) {
					$('.warning-confirmacao-mesclagem > div').addClass('text-danger').html('<div>'+data['mensagem']+'</div>');
					$('#btnModalCancelar').attr('disabled', true);
					return;
				}
				//removeAttr( 'style' )
				$('.warning-confirmacao-mesclagem > div').addClass('text-success').html('<div>'+data['success']+'</div>');
				setTimeout(function() {
					$('#modalAlerta').modal('hide');
					finalizaçãoRotaAjaxForm();
					$('.bt-mesclar-rotas').click();
				}, 1200);
			},
			error: function() {
				$('.warning-confirmacao-mesclagem > div').addClass('text-danger').html('<div>Não foi possível concluir a solicitação</div>');
				$('#btnModalCancelar').attr('disabled', true);
			},
			timeout: 10000
		});
  	}

    $(document).on('click', '.remover-rota', function() {
        var thad = $(this);
        var placa = $(this).data('placa');
        var id = $(this).data('id');
        $.post('/painel/roteirizador/rota/manual/remover/rota',
            {
                placa:placa,
                id:id
            },
            function(data) {
                if(data.codigo == 200){
                    $(thad).parents('.to-hidden').remove();
                    $('.icon-iten-'+id).remove();
                }
                //esconder ítem da tabela e remover markes

                // mapa.removeLayer(polyCarregados);
                // $(thad).parents('.panel-roteirizados').remove()
                // $('#btGerarRotaManual').trigger('click');
            }
        )
    })

    $(document).on('click','.collapse-table',function() {
        if ($(this).find('span').hasClass('fa-chevron-down')) {
            $(this).find('span').removeClass('fa-chevron-down');
            $(this).find('span').addClass('fa-chevron-right');
            $(this).attr('title','Clique aqui para mostrar a tabela de rotas');

            //alterar  o tamanho do mapa
            $('.mapa-finalizacao-rota').css('height','650px');

        } else if($(this).find('span').hasClass('fa-chevron-right')) {
            $(this).find('span').removeClass('fa-chevron-right');
            $(this).find('span').addClass('fa-chevron-down');
            $(this).attr('title','Clique aqui para esconder a tabela de rotas');

            $('.mapa-finalizacao-rota').css('height','400px');
        }
    });

    $('.flt-change-busca').on('keyup',function() {
        var bval = $(this).val();
        var thad = $('.serch');
        if (bval.length > 2) {
            $(thad).parents('.to-hidden').hide();
            $(thad).each(function(i){
        		var valCampo = $(thad[i]).html().toUpperCase()
        		if(valCampo.indexOf(bval.toUpperCase()) != -1) {
                    $(thad[i]).parents('.to-hidden').show()
        		}
            });
        } else {
            $(thad).parents('.to-hidden').show()
        }
    });


    $(document).on('click','.bt-imprimir-rotas',function(){
        var id = '';
        var thad = $('.check-imprime-rota:checked');
        $(thad).each(function(i) {
            id = id + $(thad[i]).data('id')+',';
        });
        if(id != ''){
            imprimeRota(id, $(this));
        }
    });

    $(document).on('click', ".bt-imprime-rota-fn", function() {
        var id = $(this).attr('value');
        imprimeRota(id, $(this));
    });

    function imprimeRota(id, thad){
        if ($(thad).is('span')) {
            $(thad).removeClass('fa-print').addClass('fa-spinner fa-spin fa fa-fw');
        } else {
            $(thad).find('span').removeClass('fa-print').addClass('fa-spinner fa-spin fa fa-fw');
        }
    	$.post(ROOT + '/painel/relatorios/rotas/relatorio', {
    		codigo_rota: id.split(","),
    		tipo: "pdf"
    	}, function(data) {
    		window.open(ROOT + '/' + data.file.original.dados);
            if ($(thad).is('span')) {
               $(thad).removeClass('fa-spinner fa-spin fa fa-fw').addClass('fa fa-print');
            } else {
               $(thad).find('span').removeClass('fa-spinner fa-spin fa fa-fw').addClass('fa fa-print');
            }
    	});
    }

    $('.bt-mesclar-rotas').click(function() {
    	if ($(this).hasClass('btn-danger')) {
    		$('#sectionListaRotas').show();
    		$('#sectionConfirmarRotasVeiculos').hide();
			$('.ocultarAcoesMRDois').css('display', 'none');
			$('.ocultarAcoesMR').css('display', 'block');
			$('.check').prop('checked', false);
			tootltipBtnMesclaRota('.bt-mesclar-rotas-confirmacao');
    		return;
    	}

    	$('#sectionListaRotas').hide();
    	$('#sectionConfirmarRotasVeiculos').show();
    	$('.ocultarAcoesMRDois').css('display', 'block');
		$('.ocultarAcoesMR').css('display', 'none');
		$('.bt-mesclar-rotas-confirmacao').attr('disabled', true);
    });
});
