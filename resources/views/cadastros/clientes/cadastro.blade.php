@extends('layouts.eagle')
@section('title')
Cadastro de clientes @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li><a href="{{url('painel/cadastros/clientes')}}">Clientes</a></li>
        <li class="active">Novo</li>
    </ul>
    <form id="formCadastroCliente" method="POST" action="{{url('painel/cadastros/clientes/cadastrar')}}" class="form-horizontal" enctype="multipart/form-data">
        <div id="cadastroCliente" class="container">
            <div class="tab-content col-sm-12">
                <div class="page-title">
                    <h2>
                        <span class="flaticon-icon019"></span> Cadastro de clientes
                    </h2>
                </div>
                <ul class="nav nav-tabs nav-eagle">
                    <li class="active"><a data-toggle="tab" href="#home">Cadastro</a></li>
                    <li><a data-toggle="tab" href="#menu3">Configurações</a></li>
                    <li><a data-toggle="tab" href="#parametrosJornada">Parâmetros de jornada</a></li>
                    <li><a data-toggle="tab" href="#modulosSistema">Módulos</a></li>
                </ul>
                <div id="home" class="tab-pane fade in active pane-eagle">
                    <div class="panel panel-default">
                        {{ csrf_field() }}
                        <div class="col-sm-9">
                            <div class="col-sm-12">
                                <div class="col-xs-3 block-chec-pes">
                                    <label class="tipo-cliente">Tipo de cliente</label>
                                    <div class="chec-tipo-cliente">
                                        <input type="hidden" name="cltipo" value="F">
                                        <span class="col-xs-4 psa-fisica">Física</span>
                                        <label class="col-xs-4 switch">
                                          <input id="inputTipoPessoa" class="desabilitar" {{ old('cltipo') == 'F' ? '' : 'checked' }} type="checkbox" name="cltipo" value="J">
                                          <div class="slider round"></div>
                                        </label>
                                        <span class="col-xs-4 psa-juridica">Jurídica</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9">
                            <div class="col-sm-12 dados-cliente">
                            @if(old('cltipo') == 'F')
                                <div class="col-sm-12 {{ ($errors->has('clnome')) ? 'has-error' : '' }}">
                                    <label class="label-nome-cl">Nome*</label>
                                    <input type="text" name="clnome" placeholder="Digite o nome" id="clnome" class="form-control vazio" value="{{old('clnome')}}">
                                    <p class="help-block">{{ ($errors->has('clnome') ? "Inválido" : '') }}</p>
                                </div>
                            @else
                                <div class="col-sm-12 {{ ($errors->has('clnome')) ? 'has-error' : '' }}">
                                    <label class="label-nome-cl">Razão Social*</label>
                                    <input type="text" name="clnome" placeholder="Digite o nome" id="clnome" class="form-control vazio" value="{{old('clnome')}}">
                                    <p class="help-block">{{ ($errors->has('clnome') ? "Inválido" : '') }}</p>
                                </div>
                                <div class="col-sm-10 nome-fantasia">
                                    <div class="col-sm-12 {{ ($errors->has('clfantasia')) ? 'has-error' : '' }} form-group">
                                        <label>Nome fantasia</label>
                                        <input type="text" name="clfantasia" value="{{old('clfantasia')}}" class="form-control">
                                        <p class="help-block">{{ ($errors->has('clfantasia') ? $errors->first('clfantasia') : '') }}</p>
                                    </div>
                                </div>
                            @endif
                            </div>
                            <div class="col-sm-12">
                                <div class="col-xs-3 block-cpf {{ ($errors->has('cldocumento')) ? 'has-error' : '' }}">
                                    @if(old('cltipo') == 'F')
                                        <label>CPF*</label>
                                        <input type="text" placeholder="Digite nº do documento" name="cldocumento" id="cldocumento" class="form-control cpf" value="{{old('cldocumento')}}">
                                        <p class="help-block">{{ ($errors->has('cldocumento') ? "Inválido" : '') }}</p>
                                    @else
                                        <label>CNPJ*</label>
                                        <input type="text" placeholder="Digite nº do documento" name="cldocumento" id="cldocumento" class="form-control cnpj" value="{{old('cldocumento')}}">
                                        <p class="help-block">{{ ($errors->has('cldocumento') ? "Inválido" : '') }}</p>
                                    @endif
                                </div>
                                <div class="col-xs-3 block-rg">
                                    @if(old('cltipo') == 'F')
                                        <label>RG</label>
                                    @else
                                        <label>Insc. estadual</label>
                                    @endif
                                    <input type="text" name="cldocumento2" value="{{old('cldocumento2')}}" placeholder="Digite nº do documento" id="cldocumento2" class="form-control vazio">
                                </div>
                                <div class="col-xs-3">
                                    <label>Segmento Cliente</label>
                                    <select id="" name="clsegmento" value="{{old('clsegmento')}}" class="form-control">
                                        <option value="T">Transporte</option>
                                        <option value="C">Transporte coletivo</option>
                                    </select>
                                    <!--<input type="text" name="clsegmento" value="{{old('clsegmento')}}" placeholder="Digite o segmento" id="clsegmento" class="form-control vazio">-->
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-xs-6">
                                    <label>Logradouro</label>
                                    <input type="text" placeholder="Digite o logradouro" name="cllogradouro" value="{{old('cllogradouro')}}" id="cllogradouro" class="form-control vazio">
                                </div>
                                <div class="col-xs-2">
                                    <label>Número</label>
                                    <input type="text" name="clnumero" value="{{old('clnumero')}}" placeholder="Digite o número" id="clnumero" class="form-control vazio">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-xs-4">
                                    <label>Complemento</label>
                                    <input type="text" name="clcomplemento" value="{{old('clcomplemento')}}" id="clcomplemento" placeholder="Digite o complemento" class="form-control vazio">
                                </div>
                                <div class="col-xs-3">
                                    <label>Bairro</label>
                                    <input type="text" placeholder="Digite o bairro" id="clbairro" name="clbairro" value="{{old('clbairro')}}" class="form-control vazio">
                                </div>
                                <div class="col-xs-3 busca {{ ($errors->has('clcidade')) ? 'has-error' : '' }}">
                                    <label>Cidade*</label>
                                    <select class="form-control" name="clcidade">
                                        @foreach($cidades as $cidade)
                                            @if(old('clcidade') == $cidade->cicodigo)
                                                <option selected value="{{$cidade->cicodigo}}">{{$cidade->cinome}}</option>
                                            @else
                                                <option value="{{$cidade->cicodigo}}">{{$cidade->cinome}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <hr class="col-sm-12">

                            </div>
                        </div>
                        <div class="col-xs-3 tel-mail">
                            <div class="tel-cliente">
                                    @if(old('clfone'))
                                        <div class="col-sm-offset-1 col-sm-11">
                                            <label>Telefone</label>
                                        </div>
                                        @foreach(old('clfone') as $j => $fone)
                                            <div class="row">
                                                <div class="campos-add">
                                                    <div class="col-xs-1 icon-campo-add">
                                                        @if($j == 0)
                                                            <a href="#" data-type="text" title="Adicionar telefone" data-mask="telefone" data-campo="clfone" data-parent="tel-cliente" class="add-campo"><span class="glyphicon glyphicon-plus"></span></a>
                                                        @else
                                                            <a href="#" class="remove-campo" title="Remover"><span class="glyphicon glyphicon-minus"></span></a>
                                                        @endif
                                                    </div>
                                                    <div class="col-xs-11">
                                                        <input type="text" name="clfone[]" value="{{old('clfone.'.$j)}}" id="clfone" placeholder="Digite o telefone" class="form-control telefone vazio">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="row">
                                            <div class="col-xs-1 group-mais-campo">
                                                <a href="#" data-type="text" title="Adicionar telefone" data-mask="telefone" data-campo="clfone" data-parent="tel-cliente" class="add-campo"><span class="glyphicon glyphicon-plus"></span></a>
                                            </div>
                                            <div class="col-xs-11">
                                                <label>Telefone</label>
                                                <input type="text" name="clfone[]" value="{{old('clfone.0')}}" id="clfone" placeholder="Digite o telefone" class="form-control telefone vazio">
                                            </div>
                                        </div>
                                    @endif

                            </div>
                            <div class="mail-cliente">
                                @if(old('clemail'))
                                    <div class="col-sm-offset-1 col-sm-11">
                                        <label>E-mail</label>
                                    </div>
                                    @foreach(old('clemail') as $i => $email)
                                        <div class="row">
                                            <div class="campos-add">
                                                <div class="col-xs-1 icon-campo-add">
                                                    @if($i == 0)
                                                        <a href="#" data-type="email" title="Aticionar e-mail" data-campo="clemail" data-mask="" data-parent="mail-cliente" class="add-campo"><span class="glyphicon glyphicon-plus"></span></a>
                                                    @else
                                                        <a href="#" class="remove-campo" title="Remover"><span class="glyphicon glyphicon-minus"></span></a>
                                                    @endif
                                                </div>
                                                <div class="col-xs-11">
                                                    <input type="email" name="clemail[$i]" value="{{old('clemail.'.$i)}}" id="clfone" placeholder="Digite o email" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row">
                                        <div class="col-xs-1 group-mais-campo">
                                            <a href="#" data-type="email" title="Aticionar e-mail" data-campo="clemail" data-mask="" data-parent="mail-cliente" class="add-campo"><span class="glyphicon glyphicon-plus"></span></a>
                                        </div>
                                        <div class="col-xs-11">
                                            <label>E-mail</label>
                                            <input type="email" name="clemail[]" value="{{old('clemail.0')}}" id="clemail" placeholder="Digite o email"  class="form-control vazio">
                                        </div>
                                    </div>
                                @endif

                            </div>
                            <div class="col-sm-12">
                                <label>Logo</label>
                                <input type="file" name="cllogo" value="{{'cllogo'}}" class="form-control vazio">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="col-sm-12">
                                <div class="col-xs-6">
                                    <label>Selecione o local da empresa</label>
                                    <input type="hidden" class="inputLatitude" name="cllatitude" value="{{old('cllatitude')}}">
                                    <input type="hidden" class="inputLongitude" name="cllongitude" value="{{old('cllongitude')}}">
                                    <input type="hidden" class="inputRaio" name="clraio" value="{{old('clraio')}}">
                                    <div class="mapa-cliente">
                                        <div id="mapaPrincipal"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div id="menu3" class="tab-pane fade">
                    <div class="panel panel-default pane-eagle">
                        <div class="col-sm-7">
                            <div class="col-sm-12 block-api">
                                <h4 class="title-configuracoes-cliente">Configurações de API</h4>
                                <a href="#" class="btn btn-primary habilita-api">Habilitar API</a>
                            </div>
                            <div class="col-sm-12 block-key-cliente hidden">
                                <label for="">Chave da API</label>
                                <div class="input-group">
                                    <input type="text" name="clapikey"  readonly="true" value="{{$key}}" disabled class="form-control api-key-cliente">
                                    <span class="input-group-btn">
                                        <button class="btn btn-warning bt-copiar-key" type="button">Copiar</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="col-sm-12">
                                <h4 class="title-configuracoes-cliente">Regras Aplicativo</h4>
                                <label for="">Considerar confirmações de entrega/coleta:</label>
                                <div class="radio">
                                  <label><input type="radio" value="A" checked="checked" name="clmodotratamentorota">Através do aplicativo</label>
                                </div>
                                <div class="radio">
                                  <label><input type="radio" value="I" name="clmodotratamentorota">Baseado na ignição e posicionamento do veículo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="parametrosJornada" class="tab-pane fade">
                    <div class="panel panel-default pane-eagle">
                        <div class="col-sm-4 paran-cad-cli">
                            <h4>Regras</h4>
                            <h3>Motoristas <span class="com-ajuda">com</span> ajudante</h3>
                            <h4>Considerar tempo dentro do ponto:</h4>
                            <div class="radio col-sm-12">
                                <input type="radio" checked="checked" name="cljornadamotoristacomajudante" value="E" id=""> Como espera
                            </div>
                            <div class="radio col-sm-12">
                                <input type="radio" name="cljornadamotoristacomajudante"  {{old('cljornadamotoristacomajudante') == 'T' ? 'checked' : ''}} value="T" id="">Como hora trabalhada
                            </div>
                        </div>
                        <div class="col-sm-4 mot-sem-aju paran-cad-cli">
                            <h4>Regras</h4>
                            <h3>Motoristas <span class="sem-ajuda">sem</span> ajudante</h3>
                            <h4>Considerar tempo dentro do ponto:</h4>
                            <div class="radio col-sm-12">
                                <input type="radio" checked="checked" name="cljornadamotoristasemajudante" value="E" id=""> Como espera
                            </div>
                            <div class="radio col-sm-12">
                                <!-- <input type="radio" class="hidden" name="cljornadamotoristasemajudante" {{old('cljornadamotoristasemajudante') == 'T' ? 'checked' : ''}} value="T" id="">Como hora trabalhada -->
                            </div>
                            <!-- <div class="col-sm-12">
                                <select name="" class="form-control select-pontos-clientes" id="" multiple=""></select>
                            </div> -->
                        </div>
                        <div class="col-sm-4 paran-cad-cli">
                            <h4>Regras</h4>
                            <h3>Ajutantes</h3>
                            <h4>Considerar tempo em movimento:</h4>
                            <div class="radio col-sm-12">
                                <input type="radio" checked="checked" name="cljornadaajudante" value="E" id=""> Como espera
                            </div>
                            <div class="radio col-sm-12">
                                <input type="radio" name="cljornadaajudante" {{old('cljornadaajudante') == 'T' ? 'checked' : ''}} value="T" id="">Como hora trabalhada
                            </div>
                        </div>
                    </div>
                </div>
                <div id="modulosSistema" class="tab-pane fade">
                    <div class="panel panel-default pane-eagle">
                        @foreach($mdSistemas as $i => $mdSis)
                            <div class="block-mod-sis">
                                <input type="checkbox" id={{ $mdSis->msid }} class="check-descricao"

                                {{ !empty(old('mscliente')[$i]) && !in_array($mdSis->mscodigo, old('mscliente')) ? '' : 'checked' }}
                                value="{{$mdSis->mscodigo}}" name="mscliente[]"><span class="md-descricao">{{ $mdSis->msdescricao }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="block-salvar col-sm-12 text-right">
                    <div class="col-xs-12">
                        <button id="salvarCliente" type="submit" value="save" class="btn btn-lg btn-primary">
                        <span class="glyphicon glyphicon-ok"></span>
                            Salvar
                        </button>
                        <a href="{{url('painel/cadastros/clientes')}}" class="btn btn-danger btn-lg">
                        <span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop
