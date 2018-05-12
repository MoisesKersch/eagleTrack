@extends('layouts.eagle')
@section('content')
<ul class="breadcrumb">
    <li><a href="{{url('painel')}}">Painel</a></li>
    <li><a href="{{url('painel/coletivos/cadastros/linhas/listagem')}}">Linhas</a></li>
    @if(isset($linha))
        <li class="active"><a>Editar</a></li>
    @else
        <li class="active"><a>Novo</a></li>
    @endif

</ul>
<div class="col-xs-12 col-md-12 col-sm-12">
  <div class="col-xs-12 col-md-6 col-sm-6 page-title">
      <h2>
          <span class="flaticon-icon045"></span> Cadastro de Linhas
      </h2>
  </div>
</div>
<div class="coletivos-linhas" class="page-content-wrap">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-sm-12">
                        <p class="val-error lipontos-error hidden"></p>
                    </div>

                    <div class="col-sm-4">
                        <label>Empresa*</label>
                        <select id="selectCliCadLinhas" class="col-sm-12 form-control select-selecionar-todos" name="cliente">
                            @foreach($clientes as $cliente)
                                @if(isset($linha) && $linha->licliente == $cliente->clcodigo)
                                    <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clfantasia }}</option>
                                @elseif(!isset($linha) && \Auth::user()->cliente->clcodigo == $cliente->clcodigo)
                                    <option selected value="{{ $cliente->clcodigo }}">{{ $cliente->clfantasia }}</option>
                                @else
                                    <option value="{{ $cliente->clcodigo }}">{{ $cliente->clfantasia }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-8">
                        <label>Descrição*</label>
                        <input id="lidescricao" value="{{isset($linha)?$linha->lidescricao:''}}" type="text" placeholder="Nome da linha" class="form-control lidescricao" ></input>
                        <p class="lidescricao-error hidden"></p>
                    </div>
                    <input id="licodigo" hidden value="{{isset($linha)?$linha->licodigo:''}}" ></input>

                    <div class="tab-content col-sm-12">

                        <div class="col-sm-12" id='loader'>
                            <span class="fa fa-spinner fa-spin fa-5x fa-fw margin-bottom"></span>
                        </div>

                        <ul class="nav nav-tabs nav-eagle">
                            <li class="active"><a data-toggle="tab" href="#escolherPontos">Escolher Pontos</a></li>
                            <li><a data-toggle="tab" href="#definirHorario">Definir Horário</a></li>
                        </ul>
                        <div id="escolherPontos" class="tab-pane fade in active pane-eagle">
                            <div class="panel panel-default pane-eagle col-sm-12">
                                <div class="nopadding col-sm-4">
                                    <div class="col-sm-12 nopadding">
                                     {{--    <input id="seguirOrdemInsercaoPontos" {{isset($linha)? $linha->liseguirordeminsercao? 'checked' : '': ''}}
                                            type="checkbox" > Seguir ordem de inserção dos pontos</input> --}}
                                    </div>
                                    <!-- <div class="form-inline col-sm-12 nopadding">
                                        <h4 class="lbl-vel-media col-sm-6"> Velocidade média: </h4>
                                        <input id="velocidadeMedia" type="number"  placeholder="30km/h" min="0" max="200" class="col-sm-6 form-control"></input>
                                    </div> -->
                                    <table class="table col-sm-12">
                                        <thead>
                                            <tr>
                                                <th colspan="5" >
                                                    <select type="text" placeholder="Buscar Pontos" class="search-tb-pontos form-control" />
                                                    <span class="bt-search-pontos" > <span class="glyphicon glyphicon-search"></span> </span>
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <div class="scoll-ponto col-sm-12">
                                        <table class="table datatable tb-pontos table-striped">
                                            <thead class="thead-hidde">
                                                <tr>
                                                    <th class="click-order-ordem" >Ordem</th>
                                                    <th>Ordem</th>
                                                    <th>Ordem</th>
                                                    <th>Ordem</th>
                                                    <th>Ordem</th>
                                                    <th>Ordem</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="parent-mapa-linhas col-sm-8 ">
                                    <div class="col-sm-12">
                                        <div class="col-sm-3"><input id="liPoReferencia"  type="checkbox" >Pontos de referência</input></div>
                                        <div class="col-sm-3"><input id="liPoColeta"  type="checkbox" >Pontos de coleta</input></div>
                                        <div class="col-sm-3"><input id="liPoEntrega"  type="checkbox" >Pontos de entrega</input></div>
                                        <div class="col-sm-3"><input id="liRegiao" type="checkbox" >Regiões</input></div>
                                    </div>
                                    <div id="mapaLinhas" class="col-sm-12"></div>
                                    <div id="totalizadores" class="totalizadores col-sm-4">
                                        <label>Distância total: <span class="dst-total"></spam></label>
                                        <label>Quantidade de pontos: <span class="qtd-pontos" ></spam></label>
                                        <label>Tempo estimado: <span class="tempo-estimado" ></spam></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="definirHorario" class="tab-pane">
                            <div class="panel panel-default pane-eagle">
                                <div class="col-sm-6">
                                    <div class="col-sm-6">
                                        <label>Dia da Semana</label>
                                        <select id="selectDiaSemana" class="col-sm-12 form-control" name="cliente">
                                            <option value="0">Domingo</option>
                                            <option value="1">Segunda feira</option>
                                            <option value="2">Terça feira</option>
                                            <option value="3">Quarta feira</option>
                                            <option value="4">Quinta feira</option>
                                            <option value="5">Sexta feira</option>
                                            <option value="6">Sábado</option>
                                        </select>
                                        <p class=" hidden"></p>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Horário</label>
                                        <!-- <input   type="text" class="form-control input-hora" ></input> -->
                                        <input id="horario" type="text" value="08:00"  class="form-control input-time">
                                        <p class=" hidden"></p>
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="buttom" class="bt-add-dia-semana btn btn-primary">Adicionar</button>
                                    </div>
                                    <div class="col-sm-12">
                                        <table class="">
                                            <thead>
                                                <tr>
                                                    <th colspan="3">Horários da rota</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div id="scroll-div-pqp" class="scroll col-sm-12">
                                        <table class="table-horarios table-striped ">
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <p class="val-error lihorarios-error hidden"></p>
                                </div>
                                <div class="block-salvar col-xs-12">
                                    <div class="col-xs-6 float-left">
                                        <button value="save" class="btn bt-salvar-linha btn-lg btn-primary">
                                            <span class="glyphicon glyphicon-ok"></span>Salvar
                                        </button>
                                        <a href="/painel/coletivos/cadastros/linhas/listagem" class="btn btn-danger btn-lg ">
                                            <span class="glyphicon glyphicon-remove"></span>Cancelar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


               </div>
           </div>
       </div>
   </div>
</div>
@stop
