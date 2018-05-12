<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use App\Models\Cliente;
use App\Models\Veiculo;
use App\Models\Bilhete;
use App\Models\Regioes;
use App\Models\Modulo;
use App\Models\Perfil;
use App\Models\ComandosFila;

use App\Helpers\DataHelper;
use App\Http\Controllers\Cadastros\TelefonesController as Telefones;
use App\Services\TempoParadoService;
use App\Services\VeiculoService;
use App\Services\AcionamentoPortasService;
use App\Models\PerfilItens;
use DateTime;

class VeiculosController extends Controller
{
    /*
    *   Carrega e atualiza os veiculos no mapa
    */
    public function ajaxRefreshVeiculos(Request $request)
    {
        $modulos = $request->modulo;

        $usuario = Auth::user();
        if (isset($usuario)) {
            $retorno = Array();
            if (count($modulos) > 0) {
                $dados = $this->getPosicaoVeiculo($modulos,false);
                $retorno = $this->montaDadosVeiculo($dados);
            } else {
                $dados = '';
                $retorno = '';
            }
        } else {
            abort(404, 'Usuário não encontrado');
        }

        echo json_encode($retorno);
    }

    /*
    *   Carrega os dados de um determinado veiculo ao ser clicado no mapa
    */
    public function carregaMarkersVeiculos(Request $request){
        $dado = $this->getPosicaoVeiculo(array(0 => $request->modulo),true);
        $dataI = explode(' ', $dado[0]->moultimoevento);
        $totalKm = 0;

        if ($dataI[0]) {
            $totalKm = Bilhete::select(DB::raw('coalesce(max(bihodometro) - min(bihodometro),0)/1000 as kms'))
                                 ->where('bimodulo', $dado[0]->mocodigo)
                                 ->whereBetween('bidataevento', array($dataI[0].' 00:00:00', $dado[0]->moultimoevento))
                                 ->get();

            $totalKm = $totalKm[0]->kms;
        }

        $dados = $this->montaDadosVeiculo($dado);
        $dados[$request->modulo]['totalKm'] = $totalKm;
        $dados[$request->modulo]['dataI'] = $dataI[0] ? $dataI[0].' 00:00' : (date('d/m/Y')).' 00:00';
        $dados[$request->modulo]['dataF'] = $dataI[0] ? $dado[0]->moultimoevento : date('d/m/Y');

        echo json_encode($dados);
    }

    /*
    *   Pega os dados do veiculo a partir da tabela
    *   modulo para carregar os veiculos e paineis de controle no mapa
    */
    function getPosicaoVeiculo($modulos,$getAll) {
        $retorno = Modulo::select('moultimalat', 'moultimalon', 'moultimadirecao', 'moultimobloqueio', 'moultimopanico',
                                'mocodigo', 'moultimaignicao', 'moultimavelocidade', 'momodelo','moultimomotivotransmissao',
                                'moultimareferencia','vedescricao', 'vecodigo', 'veplaca', 'veprefixo',
                                'vemotorista','veajudante', 'motoristas.mtcodigo', 'motoristas.mtnome', 'clnome', 'clfantasia', 'vetipo','veproprietario')
                            ->addSelect(DB::raw("to_char(moultimoevento, 'DD/MM/YYYY HH24:MI') as moultimoevento"))
                            ->addSelect(DB::raw("(select mtnome from motoristas where mtcodigo = veiculos.veajudante) as ajnome"))
                            ->from('modulos')
                            ->leftJoin('veiculos', 'modulos.mocodigo', '=', 'veiculos.vemodulo')
                            ->leftjoin('motoristas', 'veiculos.vemotorista', '=', 'motoristas.mtcodigo')
                            ->join('clientes', 'clcodigo', '=', 'veproprietario');
                            if(!$getAll){
                                $retorno = $retorno->where('vestatus', '=', 'A');
                            }
                            $retorno=$retorno->whereIn('mocodigo', $modulos)
                            ->orderBy('moultimoevento', 'ASC')
                            ->get();
        return $retorno;
    }

    /*
    *   Monta o array dos dados do veiculo para mandar para o mapa
    */
    function montaDadosVeiculo($dados)
    {
        $retorno = [];
        foreach ($dados as $dado) {
            $retorno[$dado->mocodigo] = Array(
                  "lat" => (float)$dado->moultimalat,
                  "lng" => (float)$dado->moultimalon,
                  "direcao" => (int)$dado->moultimadirecao,
                  "ignicao" => (int)$dado->moultimaignicao,
                  "velocidade" => $dado->moultimavelocidade,
                  "dataEvento" => $dado->moultimoevento,
                  "modulo" => $dado->mocodigo,
                  "placa" => $dado->veplaca,
                  "prefixo" => $dado->veprefixo,
                  "descricao" => $dado->vedescricao,
                  "veiculoId" => $dado->vecodigo,
                  "motoristaId" => $dado->mtcodigo,
                  "motorista" => $dado->mtnome,
                  "ajudanteId" => $dado->veajudante,
                  "ajudante" => $dado->ajnome,
                  "cliente" => ($dado->clfantasia ?: $dado->clnome),
                  "tipo" => $dado->vetipo,
                  "clcodigo" => $dado->veproprietario,
                  'ultimobloqueio' => $dado->moultimobloqueio,
                  'ultimopanico' => $dado->moultimopanico,
                  'modelo' => $dado->momodelo,
                  'moultimomotivotransmissao' => $dado->moultimomotivotransmissao,
                  'moultimareferencia' => $dado->moultimareferencia,
              );
          }
          return $retorno;
    }

    public function listaPosicoes(Request $request)
    {
        $placa = $request->placa;
        $dataInicial = $request->dataIni;
        $dataFinal = $request->dataFim;

        $posicoes = \DB::table('bilhetes as b')
            ->select(DB::raw("b.bilatlog as posicao,to_char(b.bidataevento, 'DD/MM/YYYY HH24:MI')
                                as data,coalesce(b.biendereco, 'Sem endereço') as endereco,
                                coalesce(b.bivelocidade,'0') as velocidade,
                                (b.bihodometro/1000) as hodometro, b.bimovimento as movimento,
                                coalesce(v.vevelocidademax, 80) as velocidademax"))
            ->leftJoin('veiculos as v', 'biplaca', '=', 'veplaca')
            ->where('biplaca','=', $placa)
            ->where('bidataevento', '>', $dataInicial)
            ->where('bidataevento', '<', $dataFinal)
            ->whereIn('bimotivotransmissao', [3, 4, 9, 10, 21, 22, 49])
            ->groupBy(DB::raw('posicao, endereco, velocidade, hodometro, data, movimento, velocidademax'))
            // ->groupBy('b.bilatlog', 'b.biendereco', 'v.vecodigo', 'b.bivelocidade', 'b.bihodometro', 'b.bidataevento', 'b.bimovimento')
            ->orderBy(DB::raw('data'))
            ->get();

        return response ([
            'array' => $posicoes
        ]);
    }

    public function rotasVeiculos(Request $r){
        $rota = new VeiculoService();
        return $rota->rotaVeiculo($r);
    }

    public function rastroCorrigidoVeiculo(Request $r){
        $rota = new VeiculoService();
        return $rota->rastroCorrigidoVeiculo($r);
    }

    public function paradas(Request $request)
    {
        $sp = new TempoParadoService();
        $dados = $sp->paradas($request);

        return $dados;
    }


    public function excessosVelocidades(Request $request)
    {
        $rota = new VeiculoService();
        return $rota->excessosVelocidades($request);
    }

    public function acionamentoPortas(Request $request)
    {
        $acpService = new AcionamentoPortasService;

        return $acpService->getAcionamentoPortas($request);
    }

    function getModulosPropietario($mod = '') {
        if(($mod === "0") && \Auth::user()->usumaster == 'S') {
            $modulos = null;
        }else{
            $modulos = Veiculo::select(DB::raw('vemodulo'));
                if(\Auth::user()->usumaster == 'N'){
                    $modulos->join('usuarios_clientes', 'uclcliente', '=', 'veproprietario')
                    ->where('uclusuario', '=', \Auth::user()->id);
                }
                $modulos->where('vemodulo' , '>' , 0)
                ->whereIn('vemodulo', function($query)
                {
                $query->select('bimodulo')
                  ->from('bilhetes');
                });
                $modulos = $modulos->get();
        }
            return $modulos;
    }

    public function atualizaPainel(Request $request)
    {
        //getall verifica se epra trazer todos os veiculos ou não
        $getAll = $request->getall;
        if($getAll == null) $getAll = false;
        $mods = '';
        if($request->id) {
            foreach($request->id as $mod) {
                $mods .= $mod.',';
            }
        } else {
            return;
        }
        $mod = trim($mods, ',');

            $permissoes = PerfilItens::select('perfil_permissoes.*', 'piid')
                ->join('perfil_permissoes', 'ppperfilitens', '=', 'picodigo')
                ->join('perfis', 'pecodigo', '=', 'ppperfilcodigo')
                ->join('users', 'usuperfil', 'pecodigo')
                ->where('id', '=', \Auth::user()->id)
                ->where('piid', '=', 'mappainelcontrole')
                ->first();

            $vePerfil = Perfil::select('pvvecodigo')
                ->join('perfil_veiculo', 'pecodigo', 'pvpecodigo')
                ->join('users', 'usuperfil', '=', 'pecodigo')
                ->where('id', '=', \Auth::user()->id)
                ->get();

            $modulos = Veiculo::select(DB::raw('vemodulo'))
                ->join('modulos','mocodigo','=','vemodulo')
                ->join('usuarios_clientes', 'uclcliente', '=', 'veproprietario');
                if(\Auth::user()->usumaster == 'N'){
                    $modulos->where('uclusuario', '=', \Auth::user()->id);
                }
                $modulos->where('vemodulo' , '>' , 0);
                if(!empty($mod)){
                    $modulos->whereRaw('veproprietario in ('.$mod.')');
                }
                $modulos->whereNotIn('vecodigo', $vePerfil)
                    ->where('moultimoevento','!=',null);
                $modulos = $modulos->get();


        $modulos = $this->getPosicaoVeiculo($modulos,$getAll);
        $modulos = $this->montaDadosVeiculo($modulos);

        return response ([
            'modulos' => $modulos,
            'permissoes' => $permissoes
        ]);
    }

    public function bloqueio(Request $request)
    {
        $valor = $request->val;
        $modulo = $request->modulo;
        $modelo = $request->modelo;

        try {
            $comandos = new ComandosFila;
            $comandos->cfmodelorastreador = $modelo;
            $comandos->cfmodulo = $modulo;
            $comandos->cfstatus = 'P';
            if($valor == 3) {
                $comandos->cfparametro = 1;
                $comandos->cfcomando = 'Bloquear';
            }elseif($valor == 0){
                $comandos->cfparametro = 0;
                $comandos->cfcomando = 'Desbloquear';
            }
            $comandos->save();
        } catch(\Exception $e) {
            return response ([
                'erro' => 'Não foi possível bloquear este veículo no momento!',
                'codigo' => '500'
            ]);
        }


        return response ([
            'mensagem' => 'Status de bloqueio alterado!',
            'codigo' => '200'
        ]);
    }
}
