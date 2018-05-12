@extends('layouts.eagle')
@section('title')
Cadastro de jornada de trabalho @parent
@stop
@section('content')
<ul class="breadcrumb">
    <li><a href="{{ url('painel') }}">Painel</a></li>
    <li class="active"><a href="{{ url('painel/cadastros/jornadaTrabalho') }}">Jornada de trabalho</a></li>
    <li class="active">Novo</li>
</ul>
<div class="container">
    <div class="page-title">
        <h2>
            <span class="flaticon-icon007"></span> Cadastro de jornada de trabalho
        </h2>
    </div>
    <div id="formCadastro" class=" panel panel-default">
        <div class="col-sm-4">
            <label>Tipo*</label>
            <select name="jttipo" id="" class="form-control tipo-jornada">
                <option {{old('jttipo') == 'F' ? 'selected' : ''}} value="F">Fixo</option>
                <option {{old('jttipo') == 'L' ? 'selected' : ''}} value="L">Livre</option>
            </select>
        </div>
        <hr class="col-sm-12" />
        <form id="formJornadaLivre" method="POST" action="{{ url('painel/cadastros/jornadaTrabalho/cadastrar') }}" class="hidden form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="jttipo" value="L">
            <input type="hidden" class="campo-status" name="jtstatus" value="A">
            <div class="col-sm-6">
                <div class="row">
                    <div class=" {{ ($errors->has('descrição')) ? 'has-error' : '' }}">
                        <label>Descrição*</label>
                        <input type="text" placeholder="Descrição da jornada ou código de controle"
                            name="descrição" value="{{ old('descrição') }}" class="form-control">
                        <p class="help-block">{{ ($errors->has('descrição') ? $errors->first('descrição') : '') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12" disabled>
                <div class="col-sm-1 checks">
                    <span class="title-dia">Trabalha</span>
                    <div class="chek-trabalha check-dias"><input type="checkbox" {{ !empty(old('horario')[0]) ? 'checked' : '' }}  data-dia="1" class="ck-jt-livre ck-jt-livre-1" name="checkbox-jt" value="1" id=""></div>
                    <div class="chek-trabalha check-dias"><input type="checkbox" {{ !empty(old('horario')[1]) ? 'checked' : '' }}  data-dia="2" class="ck-jt-livre ck-jt-livre-2" name="checkbox-jt" value="2" id=""></div>
                    <div class="chek-trabalha check-dias"><input type="checkbox" {{ !empty(old('horario')[2]) ? 'checked' : '' }}  data-dia="3" class="ck-jt-livre ck-jt-livre-3" name="checkbox-jt" value="3" id=""></div>
                    <div class="chek-trabalha check-dias"><input type="checkbox" {{ !empty(old('horario')[3]) ? 'checked' : '' }}  data-dia="4" class="ck-jt-livre ck-jt-livre-4" name="checkbox-jt" value="4" id=""></div>
                    <div class="chek-trabalha check-dias"><input type="checkbox" {{ !empty(old('horario')[4]) ? 'checked' : '' }}  data-dia="5" class="ck-jt-livre ck-jt-livre-5" name="checkbox-jt" value="5" id=""></div>
                    <div class="chek-trabalha check-dias"><input type="checkbox" {{ !empty(old('horario')[5]) ? 'checked' : '' }}  data-dia="6" class="ck-jt-livre ck-jt-livre-6" name="checkbox-jt" value="6" id=""></div>
                    <div class="chek-trabalha check-dias"><input type="checkbox" {{ !empty(old('horario')[6]) ? 'checked' : '' }}  data-dia="7" class="ck-jt-livre ck-jt-livre-7" name="checkbox-jt" value="7" id=""></div>
                    <div class="chek-trabalha check-dias"><input type="checkbox" {{ !empty(old('horario')[7]) ? 'checked' : '' }}  data-dia="8" class="ck-jt-livre ck-jt-livre-8" name="checkbox-jt" value="8" id=""></div>
                </div>
                <div class="col-sm-1 checks">
                    <span class="title-dsr" title="Descanso semanal remunerado">DSR</span>
                    <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-1" data-dia="1" name="rd-dsr" value="1" {{old('rd-dsr') == '1' ? 'checked' : 'checked'}} /></div>
                    <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-2" data-dia="2" name="rd-dsr" value="2" {{old('rd-dsr') == '2' ? 'checked' : ''}}/></div>
                    <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-3" data-dia="3" name="rd-dsr" value="3" {{old('rd-dsr') == '3' ? 'checked' : ''}}/></div>
                    <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-4" data-dia="4" name="rd-dsr" value="4" {{old('rd-dsr') == '4' ? 'checked' : ''}}/></div>
                    <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-5" data-dia="5" name="rd-dsr" value="5" {{old('rd-dsr') == '5' ? 'checked' : ''}}/></div>
                    <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-6" data-dia="6" name="rd-dsr" value="6" {{old('rd-dsr') == '6' ? 'checked' : ''}}/></div>
                    <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-7" data-dia="7" name="rd-dsr" value="7" {{old('rd-dsr') == '7' ? 'checked' : ''}}/></div>
                </div>
                 <div class="col-sm-1 checks">
                     <span class="title-dia">Dia</span>
                     <span class="dia-semana chdomindo check-dias">Domingo</span>
                     <span class="dia-semana check-dias">Segunda</span>
                     <span class="dia-semana check-dias">Terça</span>
                     <span class="dia-semana check-dias">Quarta</span>
                     <span class="dia-semana check-dias">Quinta</span>
                     <span class="dia-semana check-dias">Sexta</span>
                     <span class="dia-semana check-dias">Sábado</span>
                     <span class="dia-semana check-dias">Feriado</span>
                 </div>
                 <div class="col-sm-3">
                     <div class="col-sm-12 {{ ($errors->has('horario.0.hjttotalhoras')) ? 'has-error' : '' }}">
                         <div class="row">
                            <div class="title-ips">Total de horas</div>
                             <input {{empty(old('horario')[0]) ? 'disabled' : ''}} type="text" name="horario[0][hjttotalhoras]"
                             value="{{ !empty(old('horario')[0]) ? old('horario')[0]['hjttotalhoras'] : '' }}" id="" class="form-control ip-total-horas ip-total-horas total-horas-jornada-1 input-time dia-1">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.1.hjttotalhoras')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[1]) ? 'disabled' : ''}} type="text" name="horario[1][hjttotalhoras]"
                             value="{{ !empty(old('horario')[1]) ? old('horario')[1]['hjttotalhoras'] : '' }}" id="" class="form-control ip-total-horas input-time  input-totalhoras total-horas-jornada-2 dia-2">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.2.hjttotalhoras')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[2]) ? 'disabled' : ''}} type="text" name="horario[2][hjttotalhoras]"
                             value="{{ !empty(old('horario')[2]) ? old('horario')[2]['hjttotalhoras'] : '' }}" id="" class="form-control ip-total-horas input-time  input-totalhoras total-horas-jornada-3 dia-3">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.3.hjttotalhoras')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[3]) ? 'disabled' : ''}} type="text" name="horario[3][hjttotalhoras]"
                             value="{{ !empty(old('horario')[3]) ? old('horario')[3]['hjttotalhoras'] : '' }}" id="" class="form-control ip-total-horas input-time  input-totalhoras total-horas-jornada-4 dia-4">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.4.hjttotalhoras')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[4]) ? 'disabled' : ''}} type="text" name="horario[4][hjttotalhoras]"
                             value="{{ !empty(old('horario')[4]) ? old('horario')[4]['hjttotalhoras'] : '' }}" id="" class="form-control ip-total-horas input-time  input-totalhoras total-horas-jornada-5 dia-5">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.5.hjttotalhoras')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[5]) ? 'disabled' : ''}} type="text" name="horario[5][hjttotalhoras]"
                             value="{{ !empty(old('horario')[5]) ? old('horario')[5]['hjttotalhoras'] : '' }}" id="" class="form-control ip-total-horas input-time  input-totalhoras total-horas-jornada-6 dia-6">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.6.hjttotalhoras')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[6]) ? 'disabled' : ''}} type="text" name="horario[6][hjttotalhoras]"
                             value="{{ !empty(old('horario')[6]) ? old('horario')[6]['hjttotalhoras'] : '' }}" id="" class="form-control ip-total-horas input-time  input-totalhoras total-horas-jornada-7 dia-7">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.7.hjttotalhoras')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[7]) ? 'disabled' : ''}} type="text" name="horario[7][hjttotalhoras]"
                             value="{{ !empty(old('horario')[7]) ? old('horario')[7]['hjttotalhoras'] : '' }}" id="" class="form-control ip-total-horas input-time  input-totalhoras total-horas-jornada-8 dia-8">
                         </div>
                     </div>
                 </div>
                 <div class="col-sm-3">
                     <div class="col-sm-12 {{ ($errors->has('horario.0.hjtintervalo')) ? 'has-error' : '' }}">
                         <div class="row">
                            <div class="title-ips">Intervalo</div>
                             <input {{empty(old('horario')[0]) ? 'disabled' : '' }} value="{{!empty(old('horario')[0]) ? old('horario')[0]['hjtintervalo'] : ''}}" type="text" name="horario[0][hjtintervalo]" value="{{old('horario[0][hjtintervalo]')}}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-1 input-time dia-1">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.1.hjtintervalo')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[1]) ? 'disabled' : '' }} value="{{!empty(old('horario')[1]) ? old('horario')[1]['hjtintervalo'] : ''}}" type="text" name="horario[1][hjtintervalo]" value="{{old('horario[1][hjtintervalo]')}}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-2 input-time dia-2">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.2.hjtintervalo')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[2]) ? 'disabled' : '' }} value="{{!empty(old('horario')[2]) ? old('horario')[2]['hjtintervalo'] : ''}}" type="text" name="horario[2][hjtintervalo]" value="{{old('horario[2][hjtintervalo]')}}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-3 input-time dia-3">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.3.hjtintervalo')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[3]) ? 'disabled' : '' }} value="{{!empty(old('horario')[3]) ? old('horario')[3]['hjtintervalo'] : ''}}" type="text" name="horario[3][hjtintervalo]" value="{{old('horario[3][hjtintervalo]')}}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-4 input-time dia-4">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.4.hjtintervalo')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[4]) ? 'disabled' : '' }} value="{{!empty(old('horario')[4]) ? old('horario')[4]['hjtintervalo'] : ''}}" type="text" name="horario[4][hjtintervalo]" value="{{old('horario[4][hjtintervalo]')}}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-5 input-time dia-5">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.5.hjtintervalo')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[5]) ? 'disabled' : '' }} value="{{!empty(old('horario')[5]) ? old('horario')[5]['hjtintervalo'] : ''}}" type="text" name="horario[5][hjtintervalo]" value="{{old('horario[5][hjtintervalo]')}}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-6 input-time dia-6">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.6.hjtintervalo')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[6]) ? 'disabled' : '' }} value="{{!empty(old('horario')[6]) ? old('horario')[6]['hjtintervalo'] : ''}}" type="text" name="horario[6][hjtintervalo]" value="{{old('horario[6][hjtintervalo]')}}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-7 input-time dia-7">
                         </div>
                     </div>
                     <div class="col-sm-12 {{ ($errors->has('horario.7.hjtintervalo')) ? 'has-error' : '' }}">
                         <div class="row">
                             <input {{empty(old('horario')[7]) ? 'disabled' : '' }} value="{{!empty(old('horario')[7]) ? old('horario')[7]['hjtintervalo'] : ''}}" type="text" name="horario[7][hjtintervalo]" value="{{old('horario[7][hjtintervalo]')}}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-8 input-time dia-8">
                         </div>
                     </div>
                 </div>
            </div>
            <div class="col-sm-12">
                <p class="help-block text-danger">{{ ($errors->has('horario') ? 'É necessário cadastrar pelo menos um horário válido.' : '') }}</p>
                <p class="help-block text-danger">{{ ($errors->has('horario.*') ? 'Preencha os horários corretamente.' : '') }}</p>
            </div>
            <div class="col-sm-offset-1 col-sm-6">
                <div class="row">
                    <label>Empresa*</label>
                    <select name="jtcliente" value="{{ old('$cliente->jtcliente') }}" class="form-control desabilitar select2-noClear">
                        @foreach ($clientes as $key => $c)
                            @if((int)old('jtcliente') == $c->clcodigo)
                                <option selected value="{{ $c->clcodigo }}">{{ $c->clnome }}</option>
                            @elseif(old('jtcliente') == null && $cliente == $c->clcodigo)
                                <option selected value="{{ $c->clcodigo }}">{{ $c->clnome }}</option>
                            @else
                                <option value="{{ $c->clcodigo }}">{{ $c->clnome }}</option>
                            @endif
                        @endforeach;
                    </select>
                </div>
            </div>
            <div class="block-salvar">
                <div class="col-sm-12">
                    <button id="salvarCliente" type="submit" value="save" class="btn salvar btn-lg btn-primary">
                        <span class="glyphicon glyphicon-ok"></span>
                        Salvar
                    </button>
                    <a href="{{url('painel/cadastros/jornadaTrabalho')}}" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                </div>
            </div>
        </form>
        <form id="formJornadaFixa" method="POST" action="{{ url('painel/cadastros/jornadaTrabalho/cadastrar') }}" class="hidden form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="jttipo" value="F">
            <input type="hidden" class="campo-status" name="jtstatus" value="A">
            <div class="col-sm-6">
                <div class="row">
                    <div class=" {{ ($errors->has('descrição')) ? 'has-error' : '' }}">
                        <label>Descrição*</label>
                        <input type="text" placeholder="Descrição da jornada ou código de controle"
                            name="descrição" value="{{ old('descrição') }}" class="form-control">
                        <p class="help-block">{{ ($errors->has('descrição') ? $errors->first('descrição') : '') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 form-group">
                <div class="row">
                    <table id="table-jornada-trabalho">
                        <thead>
                            <tr colspan="6">
                                <th style="font-size: 14px;">Faixa Horária</th>
                            </tr>
                            <tr>
                                <th>Trabalha</th>
                                <th title="Descanso Semanal Remunerado" >DSR</th>
                                <th>Dia</th>
                                <th>Início 1° Turno</th>
                                <th>Fim 1° Turno</th>
                                <th>Início 2° Turno</th>
                                <th>Fim 2° Turno</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dias as $k => $d)
                                <tr>
                                    <td>
                                        <input type="checkbox" data-val="{{$k}}" value="{{ $d }}" name="checkbox-jt" class="checkbox-jt checkbox-jt-{{$k}}">
                                    </td>
                                    <td>
                                        @if($k < 7)
                                            @if(old('rd-jt') == $k)
                                                <input type="radio" data-val="{{$k}}" value="{{ $k }}" name="rd-dsr" class="rd-jt td-jt-{{$k}}" checked >
                                            @else
                                                <input type="radio" data-val="{{$k}}" value="{{ $k }}" name="rd-dsr" class="rd-jt td-jt-{{$k}}" {{ $k <= 0? 'checked': ''}} >
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $d }}</td>
                                    <td class="{{ ($errors->has('horarios.'.$k.'.hjtiniprimeirot')) ? 'has-error' : '' }}">
                                        <input value="{{ array_key_exists($k, old('horarios') ?: []) ? old('horarios')[$k]['hjtiniprimeirot'] : '' }}" class="form-control {{ $d }} input-time ip-ini-pri ip-ini-pri-{{$k}}"
                                            type="text" name="horarios[{{ $k }}][hjtiniprimeirot]" disabled>
                                    </td>
                                    <td class="{{ ($errors->has('horarios.'.$k.'.hjtfimprimeirot')) ? 'has-error' : '' }}">
                                        <input value="{{ array_key_exists($k, old('horarios') ?: []) ? old('horarios')[$k]['hjtfimprimeirot'] : '' }}" class="form-control {{ $d }} input-time ip-fim-pri ip-fim-pri-{{$k}}"
                                            type="text" name="horarios[{{ $k }}][hjtfimprimeirot]" disabled>
                                    </td>
                                    <td class="{{ ($errors->has('horarios.'.$k.'.hjtinisegundot')) ? 'has-error' : '' }}">
                                        <input value="{{ array_key_exists($k, old('horarios') ?: []) ? old('horarios')[$k]['hjtinisegundot'] : '' }}" class="form-control {{ $d }} input-time {{ ($d != 'Sábado') ? 'ip-ini-seg' : '' }} ip-ini-seg-{{$k}}"
                                            type="text" name="horarios[{{ $k }}][hjtinisegundot]" disabled>
                                    </td>
                                    <td class="{{ ($errors->has('horarios.'.$k.'.hjtfimsegundot')) ? 'has-error' : '' }}">
                                        <input value="{{ array_key_exists($k, old('horarios') ?: []) ? old('horarios')[$k]['hjtfimsegundot'] : '' }}" class="form-control {{ $d }} input-time {{ ($d != 'Sábado') ? 'ip-fim-seg' : '' }} ip-fim-seg-{{$k}}"
                                            type="text" name="horarios[{{ $k }}][hjtfimsegundot]" disabled>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="help-block text-danger">{{ ($errors->has('horarios') ? 'É necessário cadastrar pelo menos um horário válido.' : '') }}</p>
                    <p class="help-block text-danger">{{ ($errors->has('horarios.*') ? 'Preencha os horários corretamente.' : '') }}</p>
                </div>
            </div>
            <div class="col-sm-offset-1 col-sm-6">
                <div class="row">
                    <label>Empresa*</label>
                    <select name="jtcliente" value="{{ old('$cliente->jtcliente') }}" class="form-control desabilitar select2-noClear">
                        @foreach ($clientes as $key => $c)
                            @if((int)old('jtcliente') == $c->clcodigo)
                                <option selected value="{{ $c->clcodigo }}">{{ $c->clnome }}</option>
                            @elseif(old('jtcliente') == null && $cliente == $c->clcodigo)
                                <option selected value="{{ $c->clcodigo }}">{{ $c->clnome }}</option>
                            @else
                                <option value="{{ $c->clcodigo }}">{{ $c->clnome }}</option>
                            @endif
                        @endforeach;
                    </select>
                </div>
            </div>
            <div class="block-salvar">
                <div class="col-sm-12">
                    <button id="salvarCliente" type="submit" value="save" class="btn salvar btn-lg btn-primary">
                        <span class="glyphicon glyphicon-ok"></span>
                        Salvar
                    </button>
                    <a href="{{url('painel/cadastros/jornadaTrabalho')}}" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-remove"></span>Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
