@extends('layouts.layout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default panel-eagle">
                <div class="panel-heading">Login</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Usuário</label>

                            <div class="col-md-6">
                                <input id="email" type="text"
                                class="form-control" name="name" value="{{
                                old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Senha</label>

                            <div class="col-md-6 ip-senha">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-6 block-bt-entrar">
                                <button type="submit" class="btn btn-primary">
                                    Entrar
                                    <span class="glyphicon glyphicon-ok"></span>
                                </button>
                            </div>
                        </div>

                        {{--<div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Entrar
                                </button>

                                <!--<a class="btn btn-link" href="{{ route('password.request') }}">-->
                                    <!--Forgot Your Password?-->
                                <!--</a>-->
                            </div>
                        </div>--}}
                        <div class="img-logo-resp">
                            <img src="{{asset('/backgorundLogin/logo.png')}}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
