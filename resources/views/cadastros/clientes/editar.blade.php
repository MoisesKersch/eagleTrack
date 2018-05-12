@extends('layouts.eagle')
@section('title')
Editar de clientes @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li><a href="{{url('painel/cadastros/clientes')}}">Clientes</a></li>
        <li class="active">Editar</li>
    </ul>
    <form id="formCadastroCliente" method="POST" action="{{url('painel/cadastros/clientes/editar/'.$cliente->clcodigo)}}" class="form-horizontal" enctype="multipart/form-data">
        <div id="cadastroCliente" class="container">
            <div class="tab-content col-sm-12">
                <div class="page-title">
                    <h2>
                        <span class="flaticon-icon019"></span>Editar de clientes
                    </h2>
                </div>
                <ul class="nav nav-tabs nav-eagle">
                    <li class="active"><a data-toggle="tab" href="#home">Cadastro</a></li>
                    <li><a data-toggle="tab" href="#menu3">Configurações</a></li>
                    <li><a class="parametrosJornada" data-param="{{$errors->has('phcliente') && count($errors) == 1 ? 'true' : 'false'}}" data-toggle="tab" href="#parametrosJornada">Parâmetros de jornada</a></li>
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
                                            <input class="desabilitar" id="inputTipoPessoa" {{ $cliente->cltipo == 'J' ? 'checked' : '' }} type="checkbox" name="cltipo" value="J">
                                          <div class="slider round"></div>
                                        </label>
                                        <span class="col-xs-4 psa-juridica">Jurídica</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9">
                            <div class="col-sm-12 dados-cliente">
                                <div class="col-sm-12 block-nome {{ ($errors->has('clnome')) ? 'has-error' : '' }}">
                                    <label>Razão Social*</label>
                                    <input type="text" name="clnome" placeholder="Digite o nome" id="clnome" class="form-control vazio desabilitar" value="{{old('clnome') ? : $cliente->clnome}}">
                                    <input type="hidden" class="campo-status" value="{{ $cliente->clstatus }}">
                                    <p class="help-block">{{ ($errors->has('clnome') ? $errors->first('clnome') : '') }}</p>
                                </div>
                                @if($cliente->cltipo == 'J')
                                    <div class="col-sm-10 nome-fantasia">
                                        <div class="col-sm-10 form-group">
                                            <label>Nome fantasia</label>
                                            <input type="text" name="clfantasia" value="{{old('clfantasia') ? : $cliente->clfantasia}}" class="form-control desabilitar">
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-sm-12">
                                @if($cliente->cltipo == 'F')
                                    <div class="col-xs-3 block-cpf {{ ($errors->has('cldocumento')) ? 'has-error' : '' }}">
                                        <label>CPF*</label>
                                        <input type="text" placeholder="Digite nº do documento" name="cldocumento" id="cldocumento" class="form-control desabilitar cpf" value="{{old('cldocumento') ? : $cliente->cldocumento}}">
                                        <p class="help-block">{{ ($errors->has('cldocumento') ? $errors->first('cldocumento') : '') }}</p>
                                    </div>
                                    <div class="col-xs-3 block-rg">
                                        <label>RG</label>
                                        <input type="text" name="cldocumento2" value="{{old('cldocumento2') ? : $cliente->cldocumento2}}" placeholder="Digite nº do documento" id="cldocumento2" class="form-control desabilitar vazio">
                                    </div>
                                @else
                                    <div class="col-xs-3 block-cpf {{ ($errors->has('cldocumento')) ? 'has-error' : '' }}">
                                        <label>CNPJ*</label>
                                        <input type="text" placeholder="Digite nº do documento" name="cldocumento" id="cldocumento" class="form-control desabilitar cnpj" value="{{old('cldocumento') ? : $cliente->cldocumento}}">
                                        <p class="help-block">{{ ($errors->has('cldocumento') ? $errors->first('cldocumento') : '') }}</p>
                                    </div>
                                    <div class="col-xs-3 block-rg">
                                        <label>Insc. estadual</label>
                                        <input type="text" name="cldocumento2" value="{{old('cldocumento2') ? : $cliente->cldocumento2}}" placeholder="Digite nº do documento" id="cldocumento2" class="form-control desabilitar vazio">
                                    </div>
                                @endif
                                <div class="col-xs-3">
                                    <label>Segmento Cliente*</label>
                                    <select id="" name="clsegmento" class="form-control desabilitar">
                                        <option value="">Selecione</option>
                                        <option {{ $cliente->clsegmento == "T" ? "selected" : '' }} value="T">Transporte</option>
                                        <option {{ $cliente->clsegmento == "C" ? "selected" : '' }} value="C">Transporte coletivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="col-xs-6">
                                    <label>Logradouro</label>
                                    <input type="text" placeholder="Digite o logradouro" name="cllogradouro" value="{{old('cllogradouro') ? : $cliente->cllogradouro}}" id="cllogradouro" class="form-control desabilitar vazio">
                                </div>
                                <div class="col-xs-2">
                                    <label>Número</label>
                                    <input type="text" name="clnumero" value="{{old('clnumero') ? : $cliente->clnumero}}" placeholder="Digite o número" id="clnumero" class="form-control desabilitar vazio">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-xs-4">
                                    <label>Complemento</label>
                                    <input type="text" name="clcomplemento" value="{{old('clcomplemento') ? : $cliente->clcomplemento}}" id="clcomplemento" placeholder="Digite o complemento" class="form-control desabilitar vazio">
                                </div>
                                <div class="col-xs-3">
                                    <label>Bairro</label>
                                    <input type="text" placeholder="Digite o bairro" id="clbairro" name="clbairro" value="{{old('clbairro') ? : $cliente->clbairro}}" class="form-control desabilitar vazio">
                                </div>
                                <div class="col-xs-3 busca {{ ($errors->has('clcidade')) ? 'has-error' : '' }}">
                                    <label>Cidade*</label>
                                    <select class="form-control" name="clcidade">
                                        @foreach($cidades as $cidade)
                                            @if($cliente->clcidade == $cidade->cicodigo)
                                                <option selected value="{{$cidade->cicodigo}}">{{$cidade->cinome}}</option>
                                            @else
                                                <option value="{{$cidade->cicodigo}}">{{$cidade->cinome}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-xs-8">
                                    <label>Selecione o local da empresa</label>
                                    <input type="hidden" class="inputLatitude desabilitar" name="cllatitude" value="{{old('cllatitude') ? : $cliente->cllatitude}}">
                                    <input type="hidden" class="inputLongitude desabilitar" name="cllongitude" value="{{old('cllongitude') ? : $cliente->cllongitude}}">
                                    <input type="hidden" class="inputRaio desabilitar" name="clraio" value="{{old('clraio') ? : $cliente->clraio}}">
                                    <div class="mapa-cliente">
                                        <div id="mapaPrincipal"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3 tel-mail">
                            <div class="tel-cliente">
                                @if(old('clfone') || !$cliente->telefones->isEmpty())
                                    <div class="col-sm-offset-1 col-sm-11">
                                        <label>Telefone</label>
                                    </div>
                                    @foreach(old('clfone') ? : $cliente->telefones as $j => $fone)
                                        <div class="row">
                                            <div class="campos-add">
                                                <div class="col-xs-1 icon-campo-add">
                                                    @if($j == 0)
                                                        <a href="#" data-type="text" title="Adicionar telefone" data-mask="telefone" data-campo="clfone" data-parent="tel-cliente" class="add-campo"><span class="glyphicon glyphicon-plus"></span></a>
                                                    @else
                                                        @if(!old('clfone'))
                                                            <a href="telefones" data-id="{{ $fone->tlcodigo }}" class="remove-campo" title="Remover"><span class="glyphicon glyphicon-minus"></span></a>
                                                        @else
                                                            <a href="#" class="remove-campo" title="Remover"><span class="glyphicon glyphicon-minus"></span></a>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="col-xs-11">
                                                    <input type="text" name="clfone[]" value="{{ gettype($fone) == 'object' ? $fone->tlnumero : $fone }}" id="clfone" placeholder="Digite o telefone" class="form-control desabilitar telefone vazio">
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
                                            <input type="text" name="clfone[]" value="{{old('clfone.0')}}" id="clfone" placeholder="Digite o telefone" class="form-control desabilitar telefone vazio">
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="mail-cliente">
                                @if(old('clemail') || !$cliente->email->isEmpty())
                                    <div class="col-sm-offset-1 col-sm-11">
                                        <label>E-mail</label>
                                    </div>
                                    @foreach(old('clemail') ? : $cliente->email as $i => $email)
                                        <div class="row">
                                            <div class="campos-add">
                                                <div class="col-xs-1 icon-campo-add">
                                                    @if($i == 0)
                                                        <a href="#" data-type="email" title="Aticionar e-mail" data-campo="clemail" data-mask="" data-parent="mail-cliente" class="add-campo"><span class="glyphicon glyphicon-plus"></span></a>
                                                    @else
                                                        @if(!old('clemail'))
                                                            <a href="mail"  data-id="{{ $email->emcodigo }}" class="remove-campo" title="Remover"><span class="glyphicon glyphicon-minus"></span></a>
                                                        @else
                                                            <a href="#" class="remove-campo" title="Remover"><span class="glyphicon glyphicon-minus"></span></a>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="col-xs-11">
                                                    <input type="email" name="clemail[]" value="{{ gettype($email) == 'object' ? $email->ememail : $email }}" id="clfone" placeholder="Digite o email" class="form-control desabilitar">
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
                                            <input type="email" name="clemail[]" value="{{old('clemail.0')}}" id="clemail" placeholder="Digite o email"  class="form-control desabilitar vazio">
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-sm-12">
                                <label>Logo</label>
                                <input type="file" name="cllogo" value="{{'cllogo'}}" class="form-control desabilitar vazio">
                                @if(Auth::user()->usumaster == 'S')
                                    <img title="Logo atual" class="logo-atual" src="" alt="Logo Cliente">
                                @else(count($cliente->cllogo) > 0)
                                    <img title="Logo atual" class="logo-atual" src="{{ asset($cliente->cllogo) }}" alt="Logo Cliente">
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
                <div id="menu3" class="tab-pane fade">
                    <div class="panel panel-default pane-eagle">
                        <div class="col-sm-7">
                            <div class="col-sm-12 block-api">
                                <h4 class="title-configuracoes-cliente">Configurações de API</h4>
                                <a href="#" data-id="{{$cliente->clcodigo}}" class="btn btn-primary habilita-api">{{empty($cliente->clapikey) ? 'Habilitar' : 'Desabilitar'}} API</a>
                            </div>
                            <div class="col-sm-12 block-key-cliente {{empty($cliente->clapikey) ? 'hidden' : ''}}">
                                <label for="">Chave da API</label>
                                <div class="input-group">
                                    <input type="text" readonly="true" name="clapikey" value="{{$key}}" {{empty($cliente->clapikey) ? 'disabled' : ''}} class="form-control api-key-cliente">
                                    <span class="input-group-btn">
                                        <button class="btn btn-warning bt-copiar-key" type="button">Copiar</button>
                                    </span>
                                    <span class="input-group-btn">
                                        <button class="btn btn-info bt-new-key" type="button" data-toggle="modal" data-target="#modalAlerta">Atualizar chave</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="col-sm-12">
                                <h4 class="title-configuracoes-cliente">Regras Aplicativo</h4>
                                <label for="">Considerar confirmações de entrega/coleta:</label>
                                <div class="radio">
                                  <label><input type="radio" value="A" {{($cliente->clmodotratamentorota  == "A" || empty($cliente->clmodotratamentorota)) ? 'checked' : ''}} name="clmodotratamentorota">Através do aplicativo</label>
                                </div>
                                <div class="radio">
                                  <label><input type="radio" value="I" {{$cliente->clmodotratamentorota == "I" ? 'checked' : ''}} name="clmodotratamentorota">Baseado na ignição e posicionamento do veículo</label>
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
                                <input type="radio" name="cljornadamotoristacomajudante"  {{old('cljornadamotoristacomajudante') == 'T' || $cliente->cljornadamotoristacomajudante == 'T' ? 'checked' : ''}} value="T" id="">Como hora trabalhada
                            </div>
                        </div>
                        <div class="col-sm-4 mot-sem-aju paran-cad-cli">
                            <h4>Regras</h4>
                            <h3>Motoristas <span class="sem-ajuda">sem</span> ajudante</h3>
                            <h4>Considerar tempo dentro do ponto:</h4>
                            <div class="radio col-sm-12">
                                <input type="radio" class="sem-ajudante-espera" checked="checked" name="cljornadamotoristasemajudante" value="E" id=""> Como espera
                            </div>
                            @if(count($cliente->pontos) > 0)
                                <div class="radio col-sm-12">
                                    <input type="radio"  name="cljornadamotoristasemajudante" {{old('cljornadamotoristasemajudante') == 'T' || $cliente->cljornadamotoristasemajudante == 'T' ? 'checked' : ''}} value="T" class="sem-ajudante-trabalhada">Como hora trabalhada
                                </div>
                                <div class="col-sm-12 {{ ($errors->has('phcliente')) ? 'has-error' : '' }} hidden pontos-clientes">
                                    <label for="">Pontos a considerar horas espera</label>
                                    <select name="phcliente[]" class="form-control select-pontos-clientes" multiple="multiple">
                                        @foreach($pontos as $ponto)
                                            @if(in_array($ponto->pocodigo, $espera))
                                                <option selected value="{{$ponto->pocodigo}}">{{$ponto->podescricao}}</option>
                                            @else
                                                <option value="{{$ponto->pocodigo}}">{{$ponto->podescricao}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <p class="help-block">{{ ($errors->has('phcliente') ? $errors->first('phcliente') : '') }}</p>
                                </div>
                            @else
                                <p class="text-danger">Não há pontos cadastrados para essa cliente</p>
                            @endif
                        </div>
                        <div class="col-sm-4 paran-cad-cli">
                            <h4>Regras</h4>
                            <h3>Ajutantes</h3>
                            <h4>Considerar tempo em movimento:</h4>
                            <div class="radio col-sm-12">
                                <input type="radio" checked="checked" name="cljornadaajudante" value="E" id=""> Como espera
                            </div>
                            <div class="radio col-sm-12">
                                <input type="radio" name="cljornadaajudante" {{old('cljornadaajudante') == 'T' || $cliente->cljornadaajudante == 'T' ? 'checked' : ''}} value="T" id="">Como hora trabalhada
                            </div>
                        </div>
                    </div>
                </div>
                <div id="modulosSistema" class="tab-pane fade">
                    <div class="panel panel-default pane-eagle">
                        @foreach($mdSistemas as $i => $mdSis)
                            <div class="block-mod-sis">
                                <input type="checkbox" class="check-descricao" id="{{$mdSis->msid}}"
                                {{ ($i < 4) && ($i > 0) && !in_array(1, $modSisCliente) ? 'disabled' : '' }}
                                {{(!empty(old('mscliente')[$i]) && old('mscliente')[$i] == $mdSis->mscodigo ) ||
                                in_array($mdSis->mscodigo, $modSisCliente) ? 'checked' : ''}} value="{{$mdSis->mscodigo}}" name="mscliente[]">
                                <span class="md-descricao">{{ $mdSis->msdescricao }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="block-salvar col-sm-12 text-right">
                    <div class="col-xs-12">
                        <button id="salvarCliente" type="submit" value="save" class="btn desabilitar btn-lg btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>
                            Salvar
                        </button>
                        <a href="{{url('painel/cadastros/clientes')}}" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop
