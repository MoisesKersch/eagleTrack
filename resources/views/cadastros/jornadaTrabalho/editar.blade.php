@extends('layouts.eagle')
@section('title')
Editar jornada de trabalho @parent
@stop
@section('content')
    <ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a href="{{url('painel/cadastros/jornadaTrabalho')}}">Jornada de trabalho</a></li>
        <li class="active">Editar</li>
    </ul>
    <div class="container">
        <div class="page-title">
            <h2>
                <span class="flaticon-icon007"></span> Edição da jornada de trabalho
            </h2>
        </div>
        <div id="formCadastro" class="panel panel-default">
            <div class="col-sm-4">
                <label>Tipo*</label>
                <select name="jttipo" id="" class="form-control tipo-jornada">
                    <option {{$jt->jttipo == 'F' ? 'selected' : '' }} value="F">Fixo</option>
                    <option {{$jt->jttipo == 'L' ? 'selected' : '' }} value="L">Livre</option>
                </select>
            </div>
            <hr class="col-sm-12" />
            <form id="formJornadaLivre" method="POST" action="{{ url('painel/cadastros/jornadaTrabalho/editar/'.$jt->jtcodigo) }}" class="hidden form-horizontal" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="jttipo" value="L">
                <input type="hidden" class="campo-status" name="jtstatus" value="A">
                <input type="hidden" class="campo-status" name="jtstatus" value="A">
                <div class="col-sm-6">
                    <div class="row">
                        <div class=" {{ ($errors->has('descrição')) ? 'has-error' : '' }}">
                            <label>Descrição*</label>
                            <input type="text" placeholder="Descrição da jornada ou código de controle"
                                name="descrição" value="{{ old('jtdescricao') ? : $jt->jtdescricao }}" class="form-control">
                            <p class="help-block">{{ ($errors->has('jtdescricao') ? $errors->first('jtdescricao') : '') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12" disabled>
                    <div class="col-sm-1 checks">
                        <span class="title-dia">Trabalha</span>
                        <div class="chek-trabalha check-dias">
                            <input type="hidden" name="horario[0][hjtcodigo]" {{!isset($lHjt[0]) ? 'disabled' : ''}} value="{{isset($lHjt[0]) ? $lHjt[0]->hjtcodigo : ''}}" class="ck-jt-livre ck-jt-livre-1">
                            <input type="checkbox" {{ isset($lHjt[0]->hjtintervalo) || isset(old('horario')[0]['hjtintervalo']) ? 'checked' : '' }} data-dia="1" name="checkbox-jt" value="1" id="" class="ck-jt-livre ck-jt-livre-1">
                        </div>
                        <div class="chek-trabalha check-dias">
                            <input type="hidden" name="horario[1][hjtcodigo]" {{!isset($lHjt[1]) ? 'disabled' : ''}} value="{{isset($lHjt[1]) ? $lHjt[1]->hjtcodigo : ''}}" class="ck-jt-livre ck-jt-livre-2">
                            <input type="checkbox" {{ isset($lHjt[1]->hjtintervalo) || isset(old('horario')[1]['hjtintervalo']) ? 'checked' : '' }} data-dia="2" name="checkbox-jt" value="2" id="" class="ck-jt-livre ck-jt-livre-2">
                        </div>
                        <div class="chek-trabalha check-dias">
                            <input type="hidden" name="horario[2][hjtcodigo]" {{!isset($lHjt[2]) ? 'disabled' : ''}} value="{{isset($lHjt[2]) ? $lHjt[2]->hjtcodigo : ''}}" class="ck-jt-livre ck-jt-livre-3">
                            <input type="checkbox" {{ isset($lHjt[2]->hjtintervalo) || isset(old('horario')[2]['hjtintervalo']) ? 'checked' : '' }} data-dia="3" name="checkbox-jt" value="3" id="" class="ck-jt-livre ck-jt-livre-3">
                        </div>
                        <div class="chek-trabalha check-dias">
                            <input type="hidden" name="horario[3][hjtcodigo]" {{!isset($lHjt[3]) ? 'disabled' : ''}} value="{{isset($lHjt[3]) ? $lHjt[3]->hjtcodigo : ''}}" class="ck-jt-livre ck-jt-livre-4">
                            <input type="checkbox" {{ isset($lHjt[3]->hjtintervalo) || isset(old('horario')[3]['hjtintervalo']) ? 'checked' : '' }} data-dia="4" name="checkbox-jt" value="4" id="" class="ck-jt-livre ck-jt-livre-4">
                        </div>
                        <div class="chek-trabalha check-dias">
                            <input type="hidden" name="horario[4][hjtcodigo]" {{!isset($lHjt[4]) ? 'disabled' : ''}} value="{{isset($lHjt[4]) ? $lHjt[4]->hjtcodigo : ''}}" class="ck-jt-livre ck-jt-livre-5">
                            <input type="checkbox" {{ isset($lHjt[4]->hjtintervalo) || isset(old('horario')[4]['hjtintervalo']) ? 'checked' : '' }} data-dia="5" name="checkbox-jt" value="5" id="" class="ck-jt-livre ck-jt-livre-5">
                        </div>
                        <div class="chek-trabalha check-dias">
                            <input type="hidden" name="horario[5][hjtcodigo]" {{!isset($lHjt[5]) ? 'disabled' : ''}} value="{{isset($lHjt[5]) ? $lHjt[5]->hjtcodigo : ''}}" class="ck-jt-livre ck-jt-livre-6">
                            <input type="checkbox" {{ isset($lHjt[5]->hjtintervalo) || isset(old('horario')[5]['hjtintervalo']) ? 'checked' : '' }} data-dia="6" name="checkbox-jt" value="6" id="" class="ck-jt-livre ck-jt-livre-6">
                        </div>
                        <div class="chek-trabalha check-dias">
                            <input type="hidden" name="horario[6][hjtcodigo]" {{!isset($lHjt[6]) ? 'disabled' : ''}} value="{{isset($lHjt[6]) ? $lHjt[6]->hjtcodigo : ''}}" class="ck-jt-livre ck-jt-livre-7">
                            <input type="checkbox" {{ isset($lHjt[6]->hjtintervalo) || isset(old('horario')[6]['hjtintervalo']) ? 'checked' : '' }} data-dia="7" name="checkbox-jt" value="7" id="" class="ck-jt-livre ck-jt-livre-7">
                        </div>
                        <div class="chek-trabalha check-dias">
                            <input type="hidden" name="horario[7][hjtcodigo]" {{!isset($lHjt[7]) ? 'disabled' : ''}} value="{{isset($lHjt[7]) ? $lHjt[7]->hjtcodigo : ''}}" class="ck-jt-livre ck-jt-livre-8">
                            <input type="checkbox" {{ isset($lHjt[7]->hjtintervalo) || isset(old('horario')[7]['hjtintervalo']) ? 'checked' : '' }} data-dia="8" name="checkbox-jt" value="8" id="" class="ck-jt-livre ck-jt-livre-8">
                        </div>
                    </div>
                    <div class="col-sm-1 checks">
                        <span class="title-dsr" title="Descanso semanal remunerado">DSR</span>
                        <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-1" data-dia="1" name="rd-dsr" value="1" {{ (isset($jt->jtdsr) && $jt->jtdsr == 1) ? 'checked': ( old('rd-dsr') == '1' ? 'checked' : 'checked')  }}  /></div>
                        <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-2" data-dia="2" name="rd-dsr" value="2" {{ (isset($jt->jtdsr) && $jt->jtdsr == 2) ? 'checked': ( old('rd-dsr') == '2' ? 'checked' : '')  }} /></div>
                        <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-3" data-dia="3" name="rd-dsr" value="3" {{ (isset($jt->jtdsr) && $jt->jtdsr == 3) ? 'checked': ( old('rd-dsr') == '3' ? 'checked' : '')  }} /></div>
                        <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-4" data-dia="4" name="rd-dsr" value="4" {{ (isset($jt->jtdsr) && $jt->jtdsr == 4) ? 'checked': ( old('rd-dsr') == '4' ? 'checked' : '')  }} /></div>
                        <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-5" data-dia="5" name="rd-dsr" value="5" {{ (isset($jt->jtdsr) && $jt->jtdsr == 5) ? 'checked': ( old('rd-dsr') == '5' ? 'checked' : '')  }} /></div>
                        <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-6" data-dia="6" name="rd-dsr" value="6" {{ (isset($jt->jtdsr) && $jt->jtdsr == 6) ? 'checked': ( old('rd-dsr') == '6' ? 'checked' : '')  }} /></div>
                        <div class="check-dsr check-dias"><input type="radio" class="rd-dsr rd-dsr-7" data-dia="7" name="rd-dsr" value="7" {{ (isset($jt->jtdsr) && $jt->jtdsr == 7) ? 'checked': ( old('rd-dsr') == '7' ? 'checked' : '')  }} /></div>
                    </div>
                    <div class="col-sm-1 checks">
                        <span class="title-dia">Dia</span>
                         <span class="dia-semana check-dias">Domingo</span>
                         <span class="dia-semana check-dias">Segunda</span>
                         <span class="dia-semana check-dias">Terça</span>
                         <span class="dia-semana check-dias">Quarta</span>
                         <span class="dia-semana check-dias">Quinta</span>
                         <span class="dia-semana check-dias">Sexta</span>
                         <span class="dia-semana check-dias">Sábado</span>
                         <span class="dia-semana check-dias">Feriado</span>
                     </div>
                     <div class="col-sm-3">
                         <div class="col-sm-12">
                             <div class="row">
                                <div class="title-ips">Total de horas</div>
                                 <input {{ isset($lHjt[0]->hjttotalhoras) || isset(old('horario')[0]['hjttotalhoras']) ? '' : 'disabled' }} type="text" name="horario[0][hjttotalhoras]"
                                    value="{{isset($lHjt[0]->hjttotalhoras) ? $lHjt[0]->hjttotalhoras : (isset(old('horario')[0]['hjttotalhoras']) ? old('horario')[0]['hjttotalhoras'] : '') }}"
                                    id="" class="form-control ip-total-horas total-horas-jornada-1 input-time dia-1">
                             </div>
                         </div>

                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[1]->hjttotalhoras) || isset(old('horario')[1]['hjttotalhoras']) ? '' : 'disabled' }} type="text" name="horario[1][hjttotalhoras]"
                                    value="{{isset($lHjt[1]->hjttotalhoras) ? $lHjt[1]->hjttotalhoras : (isset(old('horario')[1]['hjttotalhoras']) ? old('horario')[1]['hjttotalhoras'] : '')}}"
                                    id="" class="form-control ip-total-horas total-horas-jornada-2 input-time dia-2">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[2]->hjttotalhoras) || isset(old('horario')[2]['hjttotalhoras']) ? '' : 'disabled' }} type="text" name="horario[2][hjttotalhoras]"
                                    value="{{isset($lHjt[2]->hjttotalhoras) ? $lHjt[2]->hjttotalhoras : (isset(old('horario')[2]['hjttotalhoras']) ? old('horario')[2]['hjttotalhoras'] : '')}}"
                                    id="" class="form-control ip-total-horas total-horas-jornada-3 input-time dia-3">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[3]->hjttotalhoras) || isset(old('horario')[3]['hjttotalhoras']) ? '' : 'disabled' }} type="text" name="horario[3][hjttotalhoras]"
                                    value="{{isset($lHjt[3]->hjttotalhoras) ? $lHjt[3]->hjttotalhoras : (isset(old('horario')[3]['hjttotalhoras']) ? old('horario')[3]['hjttotalhoras'] : '')}}"
                                    id="" class="form-control ip-total-horas total-horas-jornada-4 input-time dia-4">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[4]->hjttotalhoras) || isset(old('horario')[4]['hjttotalhoras']) ? '' : 'disabled' }} type="text" name="horario[4][hjttotalhoras]"
                                     value="{{isset($lHjt[4]->hjttotalhoras) ? $lHjt[4]->hjttotalhoras : (isset(old('horario')[4]['hjttotalhoras']) ? old('horario')[4]['hjttotalhoras'] : '')}}"
                                     id="" class="form-control ip-total-horas total-horas-jornada-5 input-time dia-5">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[5]->hjttotalhoras) || isset(old('horario')[5]['hjttotalhoras']) ? '' : 'disabled' }} type="text" name="horario[5][hjttotalhoras]"
                                    value="{{isset($lHjt[5]->hjttotalhoras) ? $lHjt[5]->hjttotalhoras : (isset(old('horario')[5]['hjttotalhoras']) ? old('horario')[5]['hjttotalhoras'] : '')}}"
                                    id="" class="form-control ip-total-horas total-horas-jornada-6 input-time dia-6">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[6]->hjttotalhoras) || isset(old('horario')[6]['hjttotalhoras']) ? '' : 'disabled' }} type="text" name="horario[6][hjttotalhoras]"
                                    value="{{isset($lHjt[6]->hjttotalhoras) ? $lHjt[6]->hjttotalhoras : (isset(old('horario')[6]['hjttotalhoras']) ? old('horario')[6]['hjttotalhoras'] : '')}}"
                                    id="" class="form-control ip-total-horas total-horas-jornada-7 input-time dia-7">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[7]->hjttotalhoras) || isset(old('horario')[7]['hjttotalhoras']) ? '' : 'disabled' }} type="text" name="horario[7][hjttotalhoras]"
                                    value="{{isset($lHjt[7]->hjttotalhoras) ? $lHjt[7]->hjttotalhoras : (isset(old('horario')[7]['hjttotalhoras']) ? old('horario')[7]['hjttotalhoras'] : '')}}"
                                    id="" class="form-control ip-total-horas total-horas-jornada-8 input-time dia-8">
                             </div>
                         </div>
                     </div>
                     <div class="col-sm-3">
                         <div class="col-sm-12">
                             <div class="row">
                                <div class="title-ips">Intervalo</div>
                                 <input {{ isset($lHjt[0]->hjtintervalo) || isset(old('horario')[0]['hjtintervalo']) ? '' : 'disabled' }} type="text" name="horario[0][hjtintervalo]" value="{{isset($lHjt[0]->hjtintervalo) ? $lHjt[0]->hjtintervalo : (isset(old('horario')[0]['hjtintervalo']) ? old('horario')[0]['hjtintervalo'] : '' ) }}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-1 input-time dia-1">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[1]->hjtintervalo) || isset(old('horario')[1]['hjtintervalo']) ? '' : 'disabled' }} type="text" name="horario[1][hjtintervalo]" value="{{isset($lHjt[1]->hjtintervalo) ? $lHjt[1]->hjtintervalo : (isset(old('horario')[1]['hjtintervalo']) ? old('horario')[1]['hjtintervalo'] : '' ) }}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-2 input-time dia-2">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[2]->hjtintervalo) || isset(old('horario')[2]['hjtintervalo']) ? '' : 'disabled' }} type="text" name="horario[2][hjtintervalo]" value="{{isset($lHjt[2]->hjtintervalo) ? $lHjt[2]->hjtintervalo : (isset(old('horario')[2]['hjtintervalo']) ? old('horario')[2]['hjtintervalo'] : '' )}}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-3 input-time dia-3">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[3]->hjtintervalo) || isset(old('horario')[3]['hjtintervalo']) ? '' : 'disabled' }} type="text" name="horario[3][hjtintervalo]" value="{{isset($lHjt[3]->hjtintervalo) ? $lHjt[3]->hjtintervalo : (isset(old('horario')[3]['hjtintervalo']) ? old('horario')[3]['hjtintervalo'] : '' ) }}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-4 input-time dia-4">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[4]->hjtintervalo) || isset(old('horario')[4]['hjtintervalo']) ? '' : 'disabled' }} type="text" name="horario[4][hjtintervalo]" value="{{isset($lHjt[4]->hjtintervalo) ? $lHjt[4]->hjtintervalo : (isset(old('horario')[4]['hjtintervalo']) ? old('horario')[4]['hjtintervalo'] : '' ) }}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-5 input-time dia-5">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[5]->hjtintervalo) || isset(old('horario')[5]['hjtintervalo']) ? '' : 'disabled' }} type="text" name="horario[5][hjtintervalo]" value="{{isset($lHjt[5]->hjtintervalo) ? $lHjt[5]->hjtintervalo : (isset(old('horario')[5]['hjtintervalo']) ? old('horario')[5]['hjtintervalo'] : '' ) }}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-6 input-time dia-6">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[6]->hjtintervalo) || isset(old('horario')[6]['hjtintervalo']) ? '' : 'disabled' }} type="text" name="horario[6][hjtintervalo]" value="{{isset($lHjt[6]->hjtintervalo) ? $lHjt[6]->hjtintervalo : (isset(old('horario')[6]['hjtintervalo']) ? old('horario')[6]['hjtintervalo'] : '' ) }}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-7 input-time dia-7">
                             </div>
                         </div>
                         <div class="col-sm-12">
                             <div class="row">
                                 <input {{ isset($lHjt[7]->hjtintervalo) || isset(old('horario')[7]['hjtintervalo']) ? '' : 'disabled' }} type="text" name="horario[7][hjtintervalo]" value="{{isset($lHjt[7]->hjtintervalo) ? $lHjt[7]->hjtintervalo : (isset(old('horario')[7]['hjtintervalo']) ? old('horario')[7]['hjtintervalo'] : '' ) }}" id="" class="form-control ip-intervalo-jornada  intervalo-jornada-8 input-time dia-8">
                             </div>
                         </div>
                     </div>
                </div>
                <div class="col-sm-12">
                    <p class="help-block text-danger">{{ ($errors->has('horario') ? 'É necessário cadastrar pelo menos um horário válido.' : '') }}</p>
                    <p class="help-block text-danger">{{ ($errors->has('horario.*') ? 'Preencha os horários corretamente.' : '') }}</p>
                </div>
                <div class="col-sm-offset-1 col-sm-6 ">
                    <div class="row">
                        <label>Empresa*</label>
                        <select name="jtcliente" value="{{ old('$jt->jtcliente') }}" class="form-control desabilitar select2-noClear">
                            @foreach ($clientes as $key => $c)
                                @if((int)old('jtcliente') == $c->clcodigo)
                                    <option selected value="{{ $c->clcodigo }}">{{ $c->clnome }}</option>
                                @elseif(old('jtcliente') == null && $jt->jtcliente == $c->clcodigo)
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
            <form id="formJornadaFixa" method="POST" action="{{ url('painel/cadastros/jornadaTrabalho/editar/'.$jt->jtcodigo) }}" class="hidden form-horizontal" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="jttipo" value="F">
                <input type="hidden" class="campo-status" name="jtstatus" value="A">
                <div class="col-sm-6">
                    <div class="row">
                        <div class=" {{ ($errors->has('descrição')) ? 'has-error' : '' }}">
                            <label>Descrição*</label>
                            <input type="text" placeholder="Descrição da jornada ou código de controle"
                                name="descrição" value="{{ old('descrição') ?: $jt->jtdescricao }}" class="form-control">
                            <p class="help-block">{{ ($errors->has('descrição') ? $errors->first('descrição') : '') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
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
                                <!-- $hjt = horas jornada trabalho  -->
                                <?php $i = 0;
                                foreach($dias as $k => $d) {
                                    if(count($hjt) > $i && $k == $hjt[$i]->hjtdiasemana) {
                                         ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" value="{{ $d }}" name="checkbox-jt" class="checkbox-jt checkbox-jt-{{$k}}" data-val="{{$k}}" checked>
                                                <input type="hidden" value="{{ $hjt[$i]->hjtcodigo }}" name="horarios[{{ $k }}][hjtcodigo]">
                                            </td>
                                            <td >
                                                @if($k < 7)
                                                    @if(old('rd-jt') == $k)
                                                        <input type="radio" data-val="{{$k}}" value="{{ $k }}" name="rd-dsr" class="rd-jt td-jt-{{$k}}" checked >
                                                    @elseif(isset($jt->jtdsr) && (($jt->jtdsr == $k) && ($jt->jttipo == 'F') ))
                                                        <input type="radio" data-val="{{$k}}" value="{{$k }}" name="rd-dsr" class="rd-jt td-jt-{{$k}}" checked >
                                                    @else
                                                        <input type="radio" data-val="{{$k}}" value="{{ $k }}" name="rd-dsr" class="rd-jt td-jt-{{$k}}" {{ $k <= 0? 'checked': ''}} >
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $d }}</td>
                                            <td class="{{ ($errors->has('horarios.'.$k.'.hjtiniprimeirot')) ? 'has-error' : '' }}">
                                                <input value="{{ array_key_exists($k, old('horarios') ?: []) ? old('horarios')[$k]['hjtiniprimeirot'] : $hjt[$i]->hjtiniprimeirot }}" class="form-control {{ $d }} input-time ip-checked ip-ini-pri"
                                                    type="text" name="horarios[{{ $k }}][hjtiniprimeirot]">
                                            </td>
                                            <td class="{{ ($errors->has('horarios.'.$k.'.hjtfimprimeirot')) ? 'has-error' : '' }}">
                                                <input value="{{ array_key_exists($k, old('horarios') ?: []) ? old('horarios')[$k]['hjtfimprimeirot'] : $hjt[$i]->hjtfimprimeirot }}" class="form-control {{ $d }} input-time ip-checked ip-fim-pri"
                                                    type="text" name="horarios[{{ $k }}][hjtfimprimeirot]">
                                            </td>
                                            <td class="{{ ($errors->has('horarios.'.$k.'.hjtinisegundot')) ? 'has-error' : '' }}">
                                                <input value="{{ array_key_exists($k, old('horarios') ?: []) ? old('horarios')[$k]['hjtinisegundot'] : $hjt[$i]->hjtinisegundot }}" class="form-control {{ $d }} input-time ip-checked {{$d != 'Sábado' ? 'ip-ini-seg' : ''}}"
                                                    type="text" name="horarios[{{ $k }}][hjtinisegundot]">
                                            </td>
                                            <td class="{{ ($errors->has('horarios.'.$k.'.hjtfimsegundot')) ? 'has-error' : '' }}">
                                                <input value="{{ array_key_exists($k, old('horarios') ?: []) ? old('horarios')[$k]['hjtfimsegundot'] : $hjt[$i]->hjtfimsegundot }}" class="form-control {{ $d }} input-time ip-checked {{$d != 'Sábado' ? 'ip-fim-seg' : ''}}"
                                                    type="text" name="horarios[{{ $k }}][hjtfimsegundot]">
                                            </td>
                                        </tr>
                                    <?php
                                        $i++;
                                    } else {
                                    ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" data-val="{{$k}}" value="{{ $d }}" name="checkbox-jt" class="checkbox-jt checkbox-jt-{{$k}}">
                                            </td>
                                            <td>
                                                @if($k < 7)
                                                    @if(old('rd-dsr') == $k)
                                                        <input type="radio" data-val="{{$k}}" value="{{ $k }}" name="rd-dsr" class="rd-jt td-jt-{{$k}}" checked >
                                                    @elseif(isset($jt->jtdsr) && $jt->jtdsr == $k)
                                                        <input type="radio" data-val="{{$k}}" value="{{$k }}" name="rd-dsr" class="rd-jt td-jt-{{$k}}" checked >
                                                    @else
                                                        <input type="radio" data-val="{{$k}}" value="{{ $k }}" name="rd-dsr" class="rd-jt td-jt-{{$k}}" {{ $k <= 0? 'checked': ''}} >
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $d }}</td>
                                            <td class="{{ ($errors->has('horarios.'.$k.'.hjtiniprimeirot')) ? 'has-error' : '' }}">
                                                <input value="{{ isset(old('horarios')[$k]['hjtiniprimeirot']) ? old('horarios')[$k]['hjtiniprimeirot'] : '' }}" class="form-control {{ $d }} input-time ip-ini-pri"
                                                    type="text" name="horarios[{{ $k }}][hjtiniprimeirot]" disabled>
                                            </td>
                                            <td class="{{ ($errors->has('horarios.'.$k.'.hjtfimprimeirot')) ? 'has-error' : '' }}">
                                                <input value="{{ isset(old('horarios')[$k]['hjtfimprimeirot']) ? old('horarios')[$k]['hjtfimprimeirot'] : '' }}" class="form-control {{ $d }} input-time ip-fim-pri"
                                                    type="text" name="horarios[{{ $k }}][hjtfimprimeirot]" disabled>
                                            </td>
                                            <td class="{{ ($errors->has('horarios.'.$k.'.hjtinisegundot')) ? 'has-error' : '' }}">
                                                <input value="{{ isset(old('horarios')[$k]['hjtinisegundot']) ? old('horarios')[$k]['hjtinisegundot'] : '' }}" class="form-control {{ $d }} input-time {{$d != 'Sábado' ? 'ip-ini-seg' : ''}}"
                                                    type="text" name="horarios[{{ $k }}][hjtinisegundot]" disabled>
                                            </td>
                                            <td class="{{ ($errors->has('horarios.'.$k.'.hjtfimsegundot')) ? 'has-error' : '' }}">
                                                <input value="{{ isset(old('horarios')[$k]['hjtfimsegundot']) ? old('horarios')[$k]['hjtfimsegundot'] : '' }}" class="form-control {{ $d }} input-time {{$d != 'Sábado' ? 'ip-fim-seg' : ''}}"
                                                    type="text" name="horarios[{{ $k }}][hjtfimsegundot]" disabled>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                            </tbody>
                        </table>
                        <p class="help-block text-danger">{{ ($errors->has('horarios') ? 'É necessário cadastrar pelo menos um horário válido.' : '') }}</p>
                        <p class="help-block text-danger">{{ ($errors->has('horarios.*') ? 'Preencha os horários corretamente.' : '') }}</p>
                    </div>
                </div>
                <div class="col-sm-offset-1 col-sm-6 ">
                    <div class="row">
                        <label>Empresa*</label>
                        <select name="jtcliente" value="{{ old('$jt->jtcliente') }}" class="form-control desabilitar select2-noClear">
                            @foreach ($clientes as $key => $c)
                                @if((int)old('jtcliente') == $c->clcodigo)
                                    <option selected value="{{ $c->clcodigo }}">{{ $c->clnome }}</option>
                                @elseif(old('jtcliente') == null && $jt->jtcliente == $c->clcodigo)
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
