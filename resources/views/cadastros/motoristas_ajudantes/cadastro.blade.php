@extends('layouts.eagle')
@section('title',  'Cadastro de Motoristas/Ajudante')
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li class="active"><a href="{{url('painel/cadastros/motoristas')}}">Motoristas e Ajudantes</a></li>
    <li class="active">Editar</li>
</ul>
<div id='cadastroMotoristaAjudante'>
    <div class="container motorista-ajudante">
        <div class="page-title">
            <h2>
                <span class="flaticon-icon010"></span> Cadastro de Motorista/ajudante
            </h2>
        </div>
        <div class="col-sm-12 col-xs-12" style="margin-top: 20px;">
            @include('addons.mensagens')
        </div>

        <div id="formCadastro" class="panel panel-default" style="width: 89%;">
            @if(isset($motorista->mtcodigo))
                <form class="form-horizontal" action="{{url('/painel/cadastros/motoristas/cadastrar')}}" method="POST">
            @else
                <form class="form-horizontal" action="{{url('/painel/cadastros/motoristas/editar')}}" method="POST">
            @endif

                <div class="col-xs-12 col-sm-12 {{ ($errors->has('mtperfil')) ? 'has-error' : '' }} ">
                <div class="col-xs-4 col-sm-4">
                    <label>Status</label>
                    <div>
                        <div class="chec-tipo-cliente col-xs-8">
                            <input type="hidden" name="status" value="I" {!! (isset($motorista) && ( $motorista->mtstatus == "I" ))?  'checked' : ''  !!}  >
                            <span class="col-xs-4 ">Inativo</span>
                            <label class="col-xs-4 switch">
                                <input type="checkbox" name="status" value="A"   {{ isset($motorista)? 'unchecked' : 'checked' }}  {!! (isset($motorista) && ( $motorista->mtstatus == "A" ))?  'checked' : ''  !!} >
                                <div class="slider round"></div>
                            </label>
                            <span class="col-xs-4 ">Ativo</span>
                        </div>
                    </div>
                </div>
                    <input type="hidden" name="mtperfil" value="" />
                    <div class="col-xs-4 col-sm-4">
                        <label>Motorista</label>
                        <div>
                            <div class="chec-tipo-cliente col-xs-8">
                                <label class="col-xs-4 switch">
                                    <input type="checkbox" {{ isset($motorista)? '': 'checked' }} name="mtperfilm" value="M"
                                        @if(null !== old('mtperfilm'))
                                            checked
                                        @elseif(isset($motorista))
                                            @if($motorista->mtperfil == "M" || $motorista->mtperfil == "MA")
                                                checked
                                            @else
                                                unchecked
                                            @endif
                                        @else
                                            unchecked
                                        @endif
                                        />
                                    <div class="slider round"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-4 col-sm-4">
                        <label>Ajudante</label>
                        <div>
                            <div class="chec-tipo-cliente col-xs-8">
                                <label class="col-xs-4 switch">
                                    <input type="checkbox" name="mtperfila" value="A"
                                        @if(null !== old('mtperfila'))
                                            checked
                                        @elseif(isset($motorista))
                                            @if($motorista->mtperfil == "A" || $motorista->mtperfil == "MA")
                                                checked
                                            @else
                                                unchecked
                                            @endif
                                        @else
                                            unchecked
                                        @endif
                                        />
                                        <div class="slider round"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <p class=" col-sm-12 col-xs-12 help-block">{{ ($errors->has('mtperfil') ? $errors->first('mtperfil') : '') }}</p>
                </div>


                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="col-xs-12 col-sm-6 {{ ($errors->has('mtnome')) ? 'has-error' : '' }} ">
                            <label>Nome*</label>
                                <input  id="mtnome"
                                @if(null !== old('mtnome'))
                                    value="{{ old('mtnome') }}"
                                @elseif(isset($motorista))
                                    value="{{ $motorista->mtnome}}"
                                @else
                                    value=""
                                @endif
                                    name="mtnome" class="form-control vazio " type="text" maxlength="100" autocomplete="off" />
                                <p class="help-block">{{ ($errors->has('mtnome') ? $errors->first('mtnome') : '') }}</p>
                        </div>

                        <div class="col-xs-12 col-sm-5 {{ ($errors->has('mtcracha')) ? 'has-error' : '' }} ">
                            <label>Crachá</label>
                            <input  id="mtcracha"
                                @if(null !== old('mtcracha'))
                                    value="{{ old('mtcracha') }}"
                                @elseif(isset($motorista))
                                    value="{{ $motorista->mtcracha}}"
                                @else
                                    value=""
                                @endif
                                    name="mtcracha" class="form-control vazio " type="text" maxlength="100" autocomplete="off" />
                                <p class="help-block">{{ ($errors->has('mtcracha') ? $errors->first('mtcracha') : '') }}</p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="col-xs-12 col-sm-5 {{ ($errors->has('mtcpf')) ? 'has-error' : '' }} ">
                            <label>CPF</label>
                            <input  id="mtcpf"
                                @if(null !== old('mtcpf'))
                                    value="{{ old('mtcpf') }}"
                                @elseif(isset($motorista))
                                    value="{{ $motorista->mtcpf}}"
                                @else
                                    value=""
                                @endif
                                    name="mtcpf" class="form-control vazio  cpf" type="text" maxlength="50" autocomplete="off" />
                                <p class="help-block">{{ ($errors->has('mtcpf') ? $errors->first('mtcpf') : '') }}</p>
                        </div>

                        <div class="col-xs-12 col-sm-6 {{ ($errors->has('mtrg')) ? 'has-error' : '' }} ">
                            <label>RG</label>
                            <input  id="mtrg"
                                @if(null !== old('mtrg'))
                                    value="{{ old('mtrg') }}"
                                @elseif(isset($motorista))
                                    value="{{ $motorista->mtrg}}"
                                @else
                                    value=""
                                @endif
                                    name="mtrg" class="form-control vazio " type="text" maxlength="50" autocomplete="off" />
                                <p class="help-block">{{ ($errors->has('mtrg') ? $errors->first('mtrg') : '') }}</p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="col-xs-12 col-sm-3 {{ ($errors->has('mttelefone')) ? 'has-error' : '' }} ">
                            <label>Telefone</label>
                            <input  id="mttelefone"
                                @if(null !== old('mttelefone'))
                                    value="{{ old('mttelefone') }}"
                                @elseif(isset($motorista))
                                    value="{{ $motorista->mttelefone}}"
                                @else
                                    value=""
                                @endif
                                    name="mttelefone" class="form-control vazio telefone" type="text" maxlength="50" autocomplete="off" />
                                <p class="help-block">{{ ($errors->has('mttelefone') ? $errors->first('mttelefone') : '') }}</p>
                        </div>

                        <div class="col-xs-12 col-sm-3 {{ ($errors->has('mtdatanasc')) ? 'has-error' : '' }}  ">
                            <label>Data Nascimento</label>
                            <input type="text" id="mtdatanasc"
                                @if(null !== old('mtdatanasc'))
                                    value="{{ old('mtdatanasc') }}"
                                @elseif(isset($motorista))
                                    value="{{ $motorista->mtdatanasc}}"
                                @else
                                    value=""
                                @endif
                                    name="mtdatanasc" class=" data-data form-control vazio inputData" type="text" maxlength="100" autocomplete="off"/>
                               <p class="help-block">{{ ($errors->has('mtdatanasc') ? $errors->first('mtdatanasc') : '') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12"><hr /></div>
                <div class="col-xs-7 {{ ($errors->has('mtcliente')) ? 'has-error' : '' }} ">
                    <label>Cliente*</label>
                    <select name="mtcliente" id="mtcliente" class="form-control cliente-motorista select2-noClear ">
                        <option value="">Selecione</option>
                        @foreach($clientes as $cliente)
                            @if(isset($motorista->cliente) && $motorista->cliente->clcodigo == $cliente->clcodigo)
                                <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                            @else
                                <option value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                            @endif
                        @endforeach
                    </select>
                    <p class="help-block">{{ ($errors->has('mtcliente') ? $errors->first('mtcliente') : '') }}</p>
                </div>
                <div class="col-sm-3  {{ ($errors->has('mtjornada')) ? 'has-error' : '' }}">
                    <label for="">Selecione a jornada</label>
                    <select name="mtjornada" id="mtjornada" class="form-control"></select>
                    <p class="help-block">{{ ($errors->has('mtjornada') ? $errors->first('mtjornada') : '') }}</p>
                </div>
                    <input type="hidden" class="mtjornada" value="{{isset($motorista) ? $motorista->mtjornada : ''}}">

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="col-xs-6 col-sm-5">
                            <label>Grupo De Motorista</label>
                            <select name="mtgrupo" class="form-control  select2-noClear " id="mtgrupo">
                                <option value='null'>Nenhum grupo</option>
                                    @foreach($gmotoristas as $grupo)
                                <option value="{{ $grupo->gmcodigo }}" {{(isset($motorista) && $grupo->gmcodigo == $motorista->mtgrupo) ? 'selected' : '' }}>{{ $grupo->gmdescricao }}</option>
                                    @endforeach
                            </select>
                        </div>

                        <div class="col-xs-6 col-sm-4">
                            <label>Pontos Relacionados</label>
                            <select name="pontosRelacionados[]" class="form-control" id="pontosRelacionados" multiple="multiple">
                                @if(isset($pontosMt) && isset($pontos))
                                    @foreach($pontos as $ponto)
                                        @if($pontosMt && in_array($ponto->pocodigo, $pontosMt))
                                            <option selected value="{{ $ponto->pocodigo }}">{{ $ponto->podescricao }}</option>
                                        @else
                                            <option value="{{ $ponto->pocodigo }}">{{ $ponto->podescricao }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>

                        </div>
                    </div>
                </div>

                <div class="col-xs-12"><hr /></div>

                <div class="col-xs-12 col-sm-4 {{ ($errors->has('mtcnhnumero')) ? 'has-error' : '' }} ">
                    <label>Número CNH</label>
                    <input id="mtcnhnumero" class="form-control" type="text" name="mtcnhnumero" value="{{ (old('mtcnhnumero') ?: (isset($motorista) ? $motorista->mtcnhnumero : ''))}}">
                </div>

                <div class="col-xs-12 col-sm-5">
                    <label>Tipo CNH</label>
                    @if(null !== old('mtcnh'))
                    <select id="mtcnh" value="{{ old('mtcnh') }}" name="mtcnh" class="form-control select2-noClear" autocomplete="off">
                        <option>Selecione</option>
                        <option value="A" {{ old('mtcnh') == "A"? 'selected' : '' }} >A</option>
                        <option value="AB" {{ old('mtcnh') == "AB"? 'selected' : '' }} >AB</option>
                        <option value="C" {{ old('mtcnh') == "C"? 'selected' : '' }} >C</option>
                        <option value="D" {{ old('mtcnh') == "D"? 'selected' : '' }} >D</option>
                        <option value="E" {{ old('mtcnh') == "E"? 'selected' : '' }} >E</option>
                    </select>
                    @else
                    <select id="mtcnh" value="{{isset($motorista) ? $motorista->mtcnh : '' }}" name="mtcnh" class="form-control" maxlength="20" autocomplete="off">
                        <option value="" >Selecione</option>
                        <option value="A" {{isset($motorista) ? ($motorista->mtcnh == "A") ? 'selected' : '' : '' }} >A</option>
                        <option value="AB" {{isset($motorista) ? ($motorista->mtcnh == "AB") ? 'selected' : '' : '' }} >AB</option>
                        <option value="C" {{isset($motorista) ? ($motorista->mtcnh == "C") ? 'selected' : '' : '' }} >C</option>
                        <option value="D" {{isset($motorista) ? ($motorista->mtcnh == "D") ? 'selected' : '' : '' }} >D</option>
                        <option value="E" {{isset($motorista) ? ($motorista->mtcnh == "E") ? 'selected' : '' : '' }} >E</option>
                    </select>
                    @endif
                </div>

                <div class="col-xs-12 col-sm-3 {{ ($errors->has('mtcnhvalidade')) ? 'has-error' : '' }}  ">
                    <label>Validade CNH</label>
                    <input type="text" id="mtcnhvalidade"
                       @if(null !== old('mtcnhvalidade'))
                        value="{{ old('mtcnhvalidade') }}"
                       @elseif(isset($motorista))
                        value="{{ $motorista->mtcnhvalidade}}"
                       @else
                        value=""
                       @endif
                        name="mtcnhvalidade" class=" data-data form-control vazio inputData" type="text" maxlength="100" autocomplete="off"/>
                       <p class="help-block">{{ ($errors->has('mtcnhvalidade') ? $errors->first('mtcnhvalidade') : '') }}</p>
                </div>
                <div class="col-sm-3">
                    <label for="">Licenças/Certificações</label>
                    <select class="form-control" id="selectMtLicenca"></select>
                </div>
                <div class="mais-licenca disabled-bt" title="Primeiramente selecione um cliente">
                    <a href="#" data-toggle="modal" data-target="#modalAlerta" class="disabled-bt tb-mais-licenca"><span class="fa fa-plus-circle"></span></a>
                </div>
                <div class="col-sm-3">
                    <label for="">Validade</label>
                    <input type="text" class="form-control data-data" id="ipLivalidade">
                </div>
                <div class="add-licenca-motorista">
                    <a href="#" class="tb-add-licenca btn btn-info"><span class="fa fa-plus-circle"></span>Adicionar</a>
                </div>
                <div class="col-sm-12">
                    <table class="table table-licenca">
                        <thead>
                            <th>Descrição</th>
                            <th>Validade</th>
                            <th>Ações</th>
                        </thead>
                        <tbody>
                            @if(isset($motorista) && $motorista->licencas)
                                @foreach($motorista->licencas as $licenca)
                                    <tr>
                                        <td class="licenca-desc">{{$licenca->lidescricao}}</td>
                                        <td>{{date('d/m/Y', strtotime($licenca->pivot->mlvalidade))}}</td>
                                        <td>
                                            <a href="#" class="btn btn-danger remover-licenca" data-motorista="{{$motorista->mtcodigo}}" data-id="{{$licenca->licodigo}}" title="Desassociar licença"><span class="fa fa-minus"></span></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

               <input value="{{isset($motorista) ? $motorista->mtcodigo : '' }}" name="mtcodigo" id="mtcodigo" type="hidden" />
               <input value="{{isset($motorista) ? $motorista->mtcodigo : '' }}" name="mtcodigo" id="mtcodigo" type="hidden" />
               <input value="{{isset($pontosMt) ? implode(',',$pontosMt) : '' }}" name="pontosMt[]" id="pontosMt" type="hidden" />





                <div class="col-xs-12"><hr /></div>

                <div class="col-xs-12 col-sm-8 {{ ($errors->has('mtendereco')) ? 'has-error' : '' }}">
                    <label>Endereço</label>
                    <input  id="mtendereco"
                        @if(null !== old('mtendereco'))
                            value="{{ old('mtendereco') }}"
                        @elseif(isset($motorista))
                            value="{{ $motorista->mtendereco}}"
                        @else
                            value=""
                        @endif
                            placeholder="Só preencha este campo se você não tiver as coordenadas de latitude e longitude."  name="mtendereco" class="form-control vazio" type="text" maxlength="200" autocomplete="off" />
                        <p class="help-block">{{ ($errors->has('mtendereco') ? $errors->first('mtendereco') : '') }}</p>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="col-xs-12 col-sm-4 {{ ($errors->has('mtlatitude')) ? 'has-error' : '' }} ">
                            <label>Latitude</label>
                            <input  id="inputPontoLatitude"
                                @if(null !== old('mtlatitude'))
                                    value="{{ old('molatitude') }}"
                                @elseif(isset($motorista))
                                    value="{{ $motorista->mtlatitude }}"
                                @else
                                    value=""
                                @endif
                                    name="mtlatitude" class="form-control vazio inputLatitude" type="text" maxlength="50" autocomplete="off" />
                                <p class="help-block">{{ ($errors->has('molatitude') ? $errors->first('mtlatitude') : '') }}</p>
                        </div>

                        <div class="col-xs-12 col-sm-4 {{ ($errors->has('mtlongitude')) ? 'has-error' : '' }} ">
                            <label>Longitude</label>
                            <input  id="inputPontoLongitude"
                                @if(null !== old('mtlongitude'))
                                    value="{{ old('mtlongitude') }}"
                                @elseif(isset($motorista))
                                    value="{{ $motorista->mtlongitude}}"
                                @else
                                    value=""
                                @endif
                                    name="mtlongitude" class="form-control vazio inputLongitude" type="text" maxlength="200" autocomplete="off" />
                                <p class="help-block">{{ ($errors->has('molongitude') ? $errors->first('mtlongitude') : '') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12">
                    <label>Selecione a residência do motorista</label>
                    <input type="hidden" class="inputRaio" id="inputRaio" name="mtraio" value="{{isset($motorista) ? $motorista->mtraio : 50 }}">
                    <div class="mapa-cliente">
                        <div id="mapaPrincipal"></div>
                    </div>
                </div>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="block-salvar col-xs-12">
                    <div class="col-xs-6" style="float:right">
                        <button type="submit" value="save" class="btn salvar btn-lg btn-primary"><span class="glyphicon glyphicon-ok"></span>Salvar</button>
                        <a href="{{url('/painel/cadastros/motoristas')}}"  class="btn btn-danger btn-lg "><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
