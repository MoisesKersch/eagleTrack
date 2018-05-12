<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- META SECTION -->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title') - Eagle Track</title>
        <link rel="icon" href="favicon.ico" type="image/x-icon" />
        <!-- END META SECTION -->

        <!-- CSS INCLUDE -->
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css">
        <link href="{{ mix('css/eagle.css') }}" rel="stylesheet">
        <link href="{{ mix('css/all.css') }}" rel="stylesheet">
        <link href="{{ mix('css/leaflet.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" id="theme" href="{{asset('css/template/theme-default.css')}}"/>
        <link href="{{ asset('icon/font/flaticon.css') }}" rel="stylesheet">

        <!-- Font -->
        <link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/manifest.json">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="theme-color" content="#ffffff">

        <!-- Scripts -->
        <script>
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
            ]) !!};
        </script>
    </head>
    <body>
        <div id="app">
            <!-- START PAGE CONTAINER -->
        <div class="page-container">

            <!-- START PAGE SIDEBAR -->
            <div class="page-sidebar hidden-print">
                <!-- START X-NAVIGATION -->
                <ul class="x-navigation">
                    <li class="xn-logo">
                        <a href="{{ url('/') }}"><span class="flaticon-icon041"></span> Ver o mapa</a>
                        <a href="#" class="x-navigation-control"></a>
                    </li>
                    <li class="xn-profile">
                        <a href="#" class="profile-mini">
                            @if(empty(Auth::user()->cliente->cllogo))
                                <img src="{{asset('img/avatar.jpg')}}" alt="John Doe"/>
                            @else
                                <img src="{{asset(Auth::user()->cliente->cllogo)}}" alt="John Doe"/>
                            @endif
                        </a>
                        <div class="profile">
                            <div class="profile-image">
                                @if(empty(Auth::user()->cliente->cllogo))
                                    <img src="{{asset('img/avatar.jpg')}}" alt="John Doe"/>
                                @else
                                    <img src="{{asset(Auth::user()->cliente->cllogo)}}" alt="John Doe"/>
                                @endif
                                <!--<img src="{{asset('img/avatar.jpg')}}" alt="John Doe"/>-->
                            </div>
                            <div class="profile-data">
                                <div class="profile-data-name">{{ Auth::user()->cliente->clnome }}</div>
                                <div class="profile-data-title">Usuário: {{ Auth::user()->name }}</div>
                            </div>
                        </div>
                    </li>
                    <li class="active">
                        <a href="{{ url('painel') }}"><span class="flaticon-icon024"></span> <span class="xn-text"> Painel</span></a>
                    </li>
                    <li class="xn-openable {{ strpos($_SERVER['REQUEST_URI'], 'painel/cadastros') ? 'active' : ''}}">
                        <a href="#"><span class="flaticon-icon018"></span> <span class="xn-text"> Cadastros</span></a>
                        <ul>
                            @if(Auth::user()->usumaster == 'S')
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], '/cadastros/chips') ? 'active' : ''}}">
                                    <a href="{{ url('painel/cadastros/chips')}}"><span class="flaticon-icon016"></span> Chips</a>
                                </li>
                            @endif
                            @if(Auth::user()->usumaster == 'S')
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], 'clientes') ? 'active' : ''}}">
                                    <a href="{{ url('painel/cadastros/clientes')}}"><span class="flaticon-icon019"></span> Clientes</a>
                                </li>
                            @endif
                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadferiados','ppvisualizar'))
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], '/cadastros/feriados') ? 'active' : ''}}"><a href="{{ url('painel/cadastros/feriados')}}"><span class="flaticon-icon002"></span> Feriados</a></li>
                            @endif
                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadgrupomotoristas','ppvisualizar'))
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], '/cadastros/gruposMotoristas') ? 'active' : ''}}"><a href="{{ url('painel/cadastros/gruposMotoristas')}}"><span class="flaticon-icon009"></span> Grupos de Motoristas</a></li>
                            @endif
                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadgrupoveiculos','ppvisualizar'))
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], '/cadastros/gruposVeiculos') ? 'active' : ''}}"><a href="{{ url('painel/cadastros/gruposVeiculos')}}"><span class="flaticon-icon014"></span> Grupos de Veículos</a></li>
                            @endif
                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadjornadatrabalho','ppvisualizar'))
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], '/cadastros/jornadaTrabalho') ? 'active' : ''}}"><a href="{{ url('painel/cadastros/jornadaTrabalho')}}"><span class="flaticon-icon007"></span> Jornada de Trabalho</a></li>
                            @endif
                            @if(Auth::user()->usumaster == 'S')
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], '/cadastros/modulos') ? 'active' : ''}}"><a href="{{ url('painel/cadastros/modulos')}}"><span class="flaticon-icon006"></span> Módulos</a></li>
                            @endif
                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadmotoristas','ppvisualizar'))
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], '/cadastros/motoristas') ? 'active' : ''}}"><a href="{{ url('painel/cadastros/motoristas')}}"><span class="flaticon-icon010"></span> Motoristas</a></li>
                            @endif
                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadperfilacesso','ppvisualizar'))
                                <li class="{{ (strpos($_SERVER['REQUEST_URI'], '/cadastros/perfil/acesso') && !strpos($_SERVER['REQUEST_URI'], '/cadastros/cadastros/perfil/acesso')) ? 'active' : ''}}">
                                    <a href="{{ url('painel/cadastros/perfil/acesso') }}"><span class="flaticon-icon031"></span> Perfil de Acesso</a>
                                </li>
                            @endif
                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadpontos','ppvisualizar'))
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], '/cadastros/pontos') ? 'active' : ''}}"><a href="{{ url('painel/cadastros/pontos')}}"><span class="flaticon-icon012"></span> Pontos</a></li>
                            @endif
                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadregioes','ppvisualizar'))
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], '/cadastros/regioes') ? 'active' : ''}}"><a href="{{ url('painel/cadastros/regioes')}}"><span class="flaticon-icon040"></span> Regiões</a></li>
                            @endif
                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadusuarios','ppvisualizar'))
                                <li class="{{ (strpos($_SERVER['REQUEST_URI'], '/cadastros/usuarios') && !strpos($_SERVER['REQUEST_URI'], '/cadastros/usuarios/app')) ? 'active' : ''}}">
                                    <a href="{{ url('painel/cadastros/usuarios') }}"><span class="flaticon-icon008"></span> Usuários</a>
                                </li>
                            @endif
                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadusuarioapp','ppvisualizar'))
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], '/cadastros/usuarios/app') ? 'active' : ''}}"><a href="{{ url('painel/cadastros/usuarios/app') }}"><span class="flaticon-icon001"></span> Usuários App</a></li>
                            @endif
                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadveiculos','ppvisualizar'))
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], '/cadastros/veiculos') ? 'active' : ''}}"><a href="{{ url('painel/cadastros/veiculos') }}"><span class="flaticon-icon023"></span> Veículos</a></li>
                            @endif



                        </ul>
                    </li>
                    <li class="xn-openable {{ strpos($_SERVER['REQUEST_URI'], 'manutencao') ? 'active' : ''}}">
                        <a href="#"><span class="flaticon-icon020" disabled></span>
                            <span class="xn-text"> Manutenções</span>
                        </a>
                        <ul>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], '/manutencao/manutencao') ? 'active' : ''}}">
                                <a href="{{ url('painel/manutencao/manutencao/') }}">
                                <span class="flaticon-icon011"></span> Agendar manutenção</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], '/manutencao/tipo_manutencao') ? 'active' : ''}}">
                                <a href="{{ url('painel/manutencao/tipo_manutencao/') }}">
                                    <span class="flaticon-icon032"></span> Tipos de manutenções
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li class="xn-openable {{ strpos($_SERVER['REQUEST_URI'], 'roteirizador') ? 'active' : ''}}">
                        <a href="#"><span class="flaticon-icon013" disabled></span> <span class="xn-text"> Roteirizador</span></a>

                        <ul>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'roteirizador/finalizacao/rota') ? 'active' : ''}}"><a href="{{ url('painel/roteirizador/finalizacao/rota') }}"><span class="flaticon-icon025"></span> Finalização de rotas</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'roteirizador/importar/cargas') ? 'active' : ''}}"><a href="{{ url('painel/roteirizador/importar/cargas') }}"><span class="flaticon-icon039"></span> Importar cargas</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'roteirizador/acompanhamento') ? 'active' : ''}}"><a href="{{ url('painel/roteirizador/acompanhamento') }}"><span class="flaticon-icon022"></span> Monitoramento</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'roteirizador/criar') ? 'active' : ''}}"><a href="{{ url('painel/roteirizador/criar') }}"><span class="flaticon-icon027"></span> Rota automática</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'roteirizador/rota/manual') ? 'active' : ''}}"><a href="{{ url('painel/roteirizador/rota/manual') }}"><span class="flaticon-icon015"></span> Rota manual</a>
                            </li>
                        </ul>
                    </li>
                    <!--<li class="xn-title">Components</li>-->

                    <li class="xn-openable {{ strpos($_SERVER['REQUEST_URI'], 'relatorios') ? 'active' : ''}}">
                        <a href="#"><span class="flaticon-icon026"></span> <span class="xn-text"> Relatórios</span></a>
                        <ul>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'relatorios/acionamentoPortas') ? 'active' : ''}}">
                                <a href="{{ url('painel/relatorios/acionamentoPortas') }}"><span class="flaticon-icon029"></span> Abertura de portas</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'relatorios/controle/horario') ? 'active' : ''}}">
                                <a href="{{ url('painel/relatorios/controle/horario') }}"><span class="flaticon-icon017"></span> Controle de horário</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'relatorios/comunicacao') ? 'active' : ''}}">
                                <a href="{{ url('painel/relatorios/comunicacao') }}"><span class="flaticon-icon047"></span> Comunicação</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'relatorios/jornada/trabalho') ? 'active' : ''}}">
                                <a href="{{ url('painel/relatorios/jornada/trabalho') }}"><span class="flaticon-icon007"></span> Jornada de trabalho</a>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'relatorios/excesso/velocidade') ? 'active' : ''}}">
                                <a href="{{ url('painel/relatorios/excesso/velocidade') }}"><span class="flaticon-icon033"></span> Excesso de velocidade</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'relatorios/historico/posicoes') ? 'active' : ''}}">
                                <a href="{{ url('painel/relatorios/historico/posicoes') }}"><span class="flaticon-icon030"></span> Histórico de posições</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'relatorios/jornada/trabalho') ? 'active' : ''}}">
                                <a href="{{ url('painel/relatorios/jornada/trabalho') }}"><span class="flaticon-icon007"></span> Jornada de trabalho</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'relatorios/kmspercorridos') ? 'active' : ''}}">
                                <a href="{{ url('/painel/relatorios/kmspercorridos') }}"><span class="flaticon-icon028"></span> Quilômetros percorridos</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'relatorios/tempo/ignicao/ligada') ? 'active' : ''}}">
                                <a href="{{ url('/painel/relatorios/tempo/ignicao/ligada') }}"><span class="flaticon-icon005"></span> Tempo de ignição ligada</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'relatorios/tempo/funcionamento') ? 'active' : ''}}">
                                <a href="{{ url('painel/relatorios/tempo/funcionamento') }}"><span class="flaticon-icon048"></span> Tempo funcionamento</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'relatorios/tempo/parado') ? 'active' : ''}}">
                                <a href="{{ url('painel/relatorios/tempo/parado') }}"><span class="flaticon-icon021"></span> Tempo parado</a>
                            </li>
                            <li class="{{ strpos($_SERVER['REQUEST_URI'], 'relatorios/regiao') ? 'active' : ''}}">
                                <a href="{{ url('painel/relatorios/regiao') }}"><span class="flaticon-icon040"></span> Veículos nas regiões</a>
                            </li>
                        </ul>
                    </li>
                    @if(\App\Helpers\AcessoHelper::acessosPermissao('cadlinhas','ppvisualizar'))
                        <li class="xn-openable {{ strpos($_SERVER['REQUEST_URI'], 'painel/coletivos') ? 'active' : ''}}">
                            <a href="#"><span class="flaticon-icon046" disabled></span> <span class="xn-text"> Coletivos</span></a>
                            <ul>
                                <li class="{{ strpos($_SERVER['REQUEST_URI'], '/coletivos/cadastros/linhas/listagem') ? 'active' : ''}}"><a href="{{ url('painel/coletivos/cadastros/linhas/listagem') }}"><span class="flaticon-icon045"></span class="xn-text"> Linhas</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
                <!-- END X-NAVIGATION -->
            </div>
            <!-- END PAGE SIDEBAR -->
            <!-- PAGE CONTENT -->
            <div class="page-content">
                <!-- START X-NAVIGATION VERTICAL -->
                <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
                    <!-- TOGGLE NAVIGATION -->
                    <li class="xn-icon-button">
                        <a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a>
                    </li>
                    <!-- END TOGGLE NAVIGATION -->
                    <!-- SEARCH -->
                    <!--<li class="xn-search">-->
                        <!--<form role="form">-->
                            <!--<input type="text" name="search" placeholder="Search..."/>-->
                        <!--</form>-->
                    <!--</li>-->
                    <!-- END SEARCH -->
                    <!-- SIGN OUT -->
                    <li class="xn-icon-button pull-right">
                        <!--<a href="#" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span></a>-->

                        </a>
                        <li class="dropdown navbar-right">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" data-html="true" id="parceiros" title="Fontes" data-toggle="popover" data-placement="left" data-content='Font generated by <a href="http://www.flaticon.com">flaticon.com</a>. <p>Under <a href="http://creativecommons.org/licenses/by/3.0/">CC</a>: <a data-file="027-icon027" href="https://www.flaticon.com/authors/good-ware">Good Ware</a>, <a data-file="029-icon029" href="http://www.freepik.com">Freepik</a>, <a data-file="002-icon002" href="https://www.flaticon.com/authors/those-icons">Those Icons</a>, <a data-file="012-icon012" href="https://www.flaticon.com/authors/daniel-bruce">Daniel Bruce</a>, <a data-file="010-icon010" href="https://www.flaticon.com/authors/simpleicon">SimpleIcon</a>, <a data-file="005-icon005" href="https://www.flaticon.com/authors/scott-de-jonge">Scott de Jonge</a>, <a data-file="003-icon003" href="https://www.flaticon.com/authors/becris">Becris</a></p>'>Parceiros</a>
                                </li>
                                <li>
                                    {{-- <a title="Sair" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> --}}
                                        {{-- Sair<span class="fa fa-sign-out"></span>  --}}
                                    <form action="{{ route('logout') }}" method="POST">
                                        <button class="tb-sair" type="submit">Sair <span class="fa fa-sign-out"></span></button>
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>


                    </li>
                    <!-- END SIGN OUT -->
                    <!-- TASKS -->
                    <li class="xn-icon-button pull-right">
                        <div class="panel panel-primary animated zoomIn xn-drop-left xn-panel-dragging">
                            <div class="panel-heading">
                                <h3 class="panel-title"><span class="fa fa-tasks"></span> Tasks</h3>
                                <div class="pull-right">
                                    <span class="label label-warning">3 active</span><--Dropzone-->
                                </div>
                            </div>
                            <div class="panel-body list-group scroll" style="height: 200px;">
                                <a class="list-group-item" href="#">
                                    <strong>Phasellus augue arcu, elementum</strong>
                                    <div class="progress progress-small progress-striped active">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">50%</div>
                                    </div>
                                    <small class="text-muted">John Doe, 25 Sep 2014 / 50%</small>
                                </a>
                                <a class="list-group-item" href="#">
                                    <strong>Aenean ac cursus</strong>
                                    <div class="progress progress-small progress-striped active">
                                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%;">80%</div>
                                    </div>
                                    <small class="text-muted">Dmitry Ivaniuk, 24 Sep 2014 / 80%</small>
                                </a>
                                <a class="list-group-item" href="#">
                                    <strong>Lorem ipsum dolor</strong>
                                    <div class="progress progress-small progress-striped active">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100" style="width: 95%;">95%</div>
                                    </div>
                                    <small class="text-muted">John Doe, 23 Sep 2014 / 95%</small>
                                </a>
                                <a class="list-group-item" href="#">
                                    <strong>Cras suscipit ac quam at tincidunt.</strong>
                                    <div class="progress progress-small">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">100%</div>
                                    </div>
                                    <small class="text-muted">John Doe, 21 Sep 2014 /</small><small class="text-success"> Done</small>
                                </a>
                            </div>
                            <div class="panel-footer text-center">
                                <a href="pages-tasks.html">Show all tasks</a>
                            </div>
                        </div>
                    </li>
                </ul>

                @include('addons.modal_deleta_desativa')
                @include('addons.modal_deleta')
                @include('addons.modal_desativa')
                @include('addons.modal_alerta')
                @include('addons.modal_confirma_manutencao')
                @include('addons.modal_nova_manutencao')
                @include('addons.modal_edit_manutencao')
                @include('addons.roterizador.modal_edit_itens_rota')
                @include('addons.modal_clean')
                @include('addons.modal_large')

                @yield('content')
            </div>
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->

        <!-- MESSAGE BOX-->
        <div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
            <div class="mb-container">
                <div class="mb-middle">
                    <div class="mb-title"><span class="fa fa-sign-out"></span> Log <strong>Out</strong> ?</div>
                    <div class="mb-content">
                        <p>Are you sure you want to log out?</p>
                        <p>Press No if youwant to continue work. Press Yes to logout current user.</p>
                    </div>
                    {{--<div class="mb-footer">
                        <div class="pull-right">
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-success btn-lg">Yes</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            <button class="btn btn-default btn-lg mb-control-close">No</button>
                        </div>
                    </div>--}}
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-desativar">
            <div class="modal-dialog">
                <div class="modal-content"></div>
            </div>
        </div>

        </div>

        <script src="{{ asset('js/template/plugins/jquery/jquery.min.js') }}"></script>
        {{-- <script src="{{ url('js/cores.js') }}"></script> --}}
        <script type="text/javascript">var ROOT = "{{ url('') }}"</script>
        <script type="text/javascript">
            var CLLATITUDE = "{{ Auth::User()->cliente->cllatitude }}"; var CLLONGITUDE = "{{ Auth::User()->cliente->cllongitude }}";
        </script>

        <script src="{{ asset('js/raphael-min.js') }}"></script>
        <script src="{{ asset('js/morris.min.js') }}"></script>
        <script src="{{ asset('js/moment.min.js') }}"></script>
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"></script>-->
        <script>
            moment.locale('pt-br');
        </script>
        <script src="{{ asset('js/template/plugins/dropzone/dropzone.min.js') }}"></script>
        <script src="{{ mix('js/layout/eagle.js') }}"></script>
        <script src="{{ asset('js/pt-br.js') }}"></script>
        <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('js/jszip.min.js') }}"></script>
        <script src="{{ asset('js/pdfmake.min.js') }}"></script>
        <script src="{{ asset('js/vfs_fonts.js') }}"></script>
        <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('js/buttons.print.min.js') }}"></script>
        <script src="{{ asset('js/jspdf.min.js') }}"></script>
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        </script>
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->
    </body>
</html>
