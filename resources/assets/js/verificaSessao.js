/*
	Depois de 11 horas inicia a verificação de 5 em 5 minutos se vai
	acabar a sessao do usuário, caso retornar true, desloga o usuário
*/
setTimeout(function() {
	$.ajax({
	    url:ROOT+'/verificasessao',
	    type: 'get',
	    success: function(retorno){
	    	console.log(retorno)
	        if (retorno)
	        	$('#logout-form').submit();
	        else {
	        	setInterval(function() {
					$.ajax({
						    url:ROOT+'/verificasessao',
						    type: 'get',
						    success: function(retorno) {
						    	if (retorno)
	        						$('#logout-form').submit();
						    }
						});
	        	}, 5000);
	        }
	    }
	});
}, 35400000); //9:55 horas