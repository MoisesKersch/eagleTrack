@extends('layouts.eagle')
@section('title')
Listagem de jornada de trabalho @parent
@stop
@section('content')
<ul class="breadcrumb">
        <li><a href="{{url('painel')}}">Painel</a></li>
        <li class="active"><a>Jornada de trabalho</a></li>
    </ul>
<div class="page-title">
    <h2>
        <span class="flaticon-icon007"></span> Listagem das jornadas de trabalho
        @if(\App\Helpers\AcessoHelper::acessosPermissao('cadjornadatrabalho','ppcadastrar'))
            <a href="{{ url('painel/cadastros/jornadaTrabalho/cadastrar') }}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>Nova Jornada</a>
        @endif
    </h2>
</div>
@if(\App\Helpers\AcessoHelper::acessosPermissao('cadjornadatrabalho','ppvisualizar'))
    <div id="ppeditar" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadjornadatrabalho','ppeditar')}}"></div>
    <div id="ppexcluir" data-permissao="{{\App\Helpers\AcessoHelper::acessosPermissao('cadjornadatrabalho','ppexcluir')}}"></div>
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-4 col-xs-12 form-group">
                            <span class="label-botoes-table">Selecione as empresas</span>
                            <select multiple class="col-sm-12 select-selecionar-todos buscar-clientes-jt" name="clientesbusca">
                                <option value="T">Selecionar todos</option>
                                @foreach($clientes as $cliente)
                                    <option {{ $adm ? '' : 'selected' }} value="{{ $cliente->clcodigo }}">{{ $cliente->clnome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <span class="label-botoes-table">Status</span>
                            <div class="btn-group btn-group-altera-status">
                                <button type="button" value="ativo"
                                    class="btn {{ $status=='ativo' ? 'btn-primary' : 'btn-default' }} bt-filtro-listas jt-status bt-filtros-update">Ativos</button>
                                <button type="button" value="inativo"
                                    class="btn {{ $status=='inativo' ? 'btn-primary' : 'btn-default' }} bt-filtro-listas jt-status bt-filtros-update">Inativos</button>
                                <button type="button" value="todos" class="btn {{ $status=='todos' ? 'btn-primary' : 'btn-default' }}
                                    {{ $status== null ? 'btn-primary' : 'btn-default' }} bt-filtro-listas jt-status bt-filtros-update">Todos</button>
                            </div>
                        </div>
                        <table id="table-jornada-trabalho" class="table datatable-col-4">
                            <thead>
                                <tr>
                                    <th>Descrição</th>
                                    <th>tipo</th>
                                    <th>Empresa</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jt as $jornadaTrabalho)
                                    <tr>
                                        <td>{{ $jornadaTrabalho->jtdescricao }}</td>
                                        <td>
                                            @if($jornadaTrabalho->jttipo == 'F')
                                                Fixa
                                            @else
                                                Livre
                                            @endif
                                            {{--@foreach($jornadaTrabalho->horasJornadaTrabalho as $hjt)
                                                <div>{{ $dias[$hjt->hjtdiasemana].' - 1° Turno '.substr($hjt->hjtiniprimeirot,0,5).' à '.substr($hjt->hjtfimprimeirot,0,5).($hjt->hjtinisegundot != null ? (' - 2° Turno '.substr($hjt->hjtinisegundot,0,5).' à '.substr($hjt->hjtfimsegundot,0,5)) : '') }}</div>
                                            @endforeach--}}
                                        </td>
                                        <td>
                                            {{ $jornadaTrabalho->clienteJornada->clnome }}
                                        </td>
                                        <td>
                                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadjornadatrabalho','ppexcluir'))
                                                @if ($jornadaTrabalho->jtstatus == 'A')
                                                    <a title="Desativar Jornada de Trabalho" class="btDelModal btn btn-danger desativar-cadastros {{ $jornadaTrabalho->jtstatus == 'I' ? 'hidden' : '' }}" data-toggle="modal" data-target="#modalDelataDesativa" data-delete-action="{!! url('painel/cadastros/jornadaTrabalho/desativar/'.$jornadaTrabalho->jtcodigo) !!}">
                                                        <span class="fa fa-ban"></span>
                                                    </a>
                                                @else
                                                    <a href='#' title="Ativar jornada" data-url="{{ url('painel/cadastros/jornadaTrabalho/ativar') }}" data-id="{{ $jornadaTrabalho->jtcodigo }}" class="btn {{ $jornadaTrabalho->jtstatus == 'A' ? 'hidden' : '' }} btn-success ativar-cadastros">
                                                        <span class="fa fa-check"></span>
                                                    </a>
                                                @endif
                                            @endif
                                            @if(\App\Helpers\AcessoHelper::acessosPermissao('cadjornadatrabalho','ppeditar'))
                                                <a title="Editar jornada" class="btn btn-info" href="{{url('painel/cadastros/jornadaTrabalho/editar/'.$jornadaTrabalho->jtcodigo)}}"><span class="fa fa-pencil"></span></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@stop
