$(document).on('keyup', '#inputUsuario', buscaClientes);

function buscaClientes() {
	var cliente = $(this).val();

	if(cliente.length > 2) {
		$('.ul-busca li').remove();
		$.post(ROOT+'/painel/cadastros/clientes/buscar', {busca:cliente}, function(data) {
			var clientes = data.dados;
			var ul = '<ul class="ul-busca">';

			$.each(clientes, function(i, cli) {
				ul += '<li><a href="#" class="buscados" data-id="'+cli.clcodigo+'">'+cli.clnome+'</a>'; 
			});

			ul += '</ul>';
			$('.busca').append(ul);
		});
	} else if(cliente.length == 0) {
		$('.ul-busca').remove();
	}
}

$(document).on('click', '.buscados', setaBusca);

function setaBusca(e) {
	e.preventDefault();
	
	$('#usucliente').val($(this).attr('data-id'));
	$('#inputUsuario').val($(this).html());
	$('.ul-busca').remove();
}
