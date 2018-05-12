@if (session('success'))
	<div class="alert alert-success center-block message-min" role="alert">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	  	<strong>Sucesso! </strong>{{ session('success') }}
	</div>
@endif

@if (Session::has('error'))
    <div class="alert alert-danger center-block message-min" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
        <strong>Ops! </strong> {{Session::get('error')}}
    </div>
@endif
@if (Session::has('warning'))
    <div class="alert alert-warning center-block message-min" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
        <strong>Atenção! </strong> {{Session::get('warning')}}
    </div>
@endif
@if (Session::has('flash_error'))
    <div class="alert alert-danger ">
        <strong>Opaa!</strong> Temos problemas para acessar.<br><br>
     E-mail ou senha inválidos.</div>
@endif
