<nav class="navbar navbar-default navbar-static-top eagle-navbar">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand title-eagle" href="{{ url('/') }}">
                <img src="../backgorundLogin/logo.png" alt="logo">
                <div><span>EAGLE</span>TRACK</div>
                <!-- {{ config('app.name', 'Eagle Track') }} -->
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                &nbsp;
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav nav-eagle navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <!--<li><a href="{{ route('register') }}">Register</a></li>-->
                @else
                    @foreach($perfis as $perfil)
                    @if($perfil->piid == 'mapferrementes' && $perfil->ppvisualizar)
                        {{-- <li>
                            <div class="dropdown">
                                <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="glyphicon glyphicon-wrench"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dLabel">
                                    <div class="filtroFerramentasPC">
                                        <li>
                                            <input id="ipFerramentaArea" class="filtroFerramentasPC" type="checkbox">
                                            <label for="ipFerramentaArea">Medir área</label>
                                        </li>
                                        <li>
                                            <input id="ipFerramentaDistancia" class="filtroFerramentasPC" type="checkbox">
                                            <label for="ipFerramentaDistancia">Medir distância</label>
                                        </li>
                                        <li>
                                            <input id="ipFerramentaRota" class="filtroFerramentasPC" type="checkbox">
                                            <label for="ipFerramentaRota">Traçar rota</label>
                                        </li>
                                    </div>
                                </ul>
                            </div>
                        </li> --}}
                    @endif
                    @endforeach
                    <li>
                        <div class="dropdown">
                            <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="fa fa-map-marker"></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dLabel">
                                @foreach($perfis as $perfil)
                                    @if($perfil->piid == 'cadpontos' && $perfil->ppvisualizar)
                                        <li>
                                            <input type="checkbox" id="checkboxVisualizaColeta" value="C" /> Pontos de Coleta
                                        </li>
                                        <li>
                                            <input type="checkbox" id="checkboxVisualizaEntrega" value="E"  /> Pontos de Entrega
                                        </li>
                                        <li>
                                            <input type="checkbox" id="checkboxVisualizaReferencia" value="P" /> Pontos de Referência
                                        </li>
                                    @endif
                                @endforeach
                                <li>
                                    <input type="checkbox" id="checkboxVisualizaRegioes" value="R" /> Regiões
                                </li>
                                <li>
                                    <input type="checkbox" class="btn-cluster" name="cluster" value="1" /> Agrupar objetos
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li><a title="Ir para o painel" href="{{ url('painel') }}"><span class="fa fa-bar-chart-o"></span></a></li>
                    <li>
                        <a title="Sair" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="fa fa-sign-out"></span>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
