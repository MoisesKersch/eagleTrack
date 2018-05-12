<?php

namespace App\Http\Controllers\Roteirizador;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Pontos;
use App\Models\ItensRota;
use App\Models\Veiculo;
use App\Models\Regioes;
use App\Models\Rota;
use App\Services\RoteirizadorService;
use App\Helpers\RoteirizadorHelper;
use App\Helpers\DataHelper;

class RoteirizadorController extends Controller
{

    public function criar()
    {
        $clientes = Cliente::select('clnome', 'clcodigo');
            if(\Auth::user()->usumaster == 'N'){
                $clientes->join('usuarios_clientes', 'uclcliente', '=', 'clcodigo')
                ->where('uclusuario', '=', \Auth::user()->id);
            }
        $clientes = $clientes->get();

        return view('roteirizador.criar', compact('clientes'));
    }

    public function dadosParametrizacao(Request $request)
    {
        $cliente = Cliente::select('clcodigo','clnome')
                    ->where('clcodigo',$request->prproprietario)->first();
        $pontos = Pontos::select('pocodigo as id','podescricao as descricao')
                    ->where('pocodigocliente',$request->prproprietario)->get();

        // $pontoSaida = [['id'=>$cliente->clcodigo,'P/C' => 'C', 'descricao'=>$cliente->clnome]];
        // return $pontoSaida;

        // foreach ($pontos as $i => $ponto) {
        //     array_push($pontoSaida,['id'=>$ponto->pocodigo,'P/C'=>'P','descricao'=>$ponto->podescricao]);
        // }

        $regioes = Regioes::select('recodigo','redescricao')
                    ->where('recliente',$request->prproprietario)->get();

        return response([
            'ponto_saida'=>$pontos,
            'ponto_retorno'=>$pontos,
            'regioes'=>$regioes
        ]);
    }

    public function carregarParametros(Request $request)
    {

        $itens_rotas = ItensRota::select('ircodigo','irnome','irdocumento','irqtde','ircubagem','irpeso','irvalor','potipo')
                    ->join('pontos','pocodigoexterno','=','ircodigoexterno')
                    ->where('ircliente', '=', $request->prproprietario)
                    ->where('pocodigocliente', '=', $request->prproprietario)
                    ->where('irdata','=',$request->dtsaida)
                    ->where('irstatus','I')
                    ->where(function ($query){
                            return $query->where('potipo', '=', 'C')
                                  ->orWhere('potipo', '=', 'E');
                              })
                    ->get();

        $veiculos = Veiculo::select('vecodigo','veplaca','veprefixo')
                        ->addSelect(\DB::raw('coalesce(vemaxpeso,0) as vemaxpeso'))
                        ->addSelect(\DB::raw('coalesce(vecubagem,0) as vecubagem'))
                        ->addSelect(\DB::raw('coalesce(vemaxentregas,0) as vemaxentregas'))
                    ->where('veproprietario',$request->prproprietario)
                    ->where('veroterizar','S')
                    ->where('vestatus','A')->get();

        return response([
            'itens_rotas'=>$itens_rotas,
            'veiculos' => $veiculos
        ]);
    }

    public function edit(Request $request)
    {
        try {
            $item = ItensRota::find($request->id);
            $item->irdata = $request->value;
            $item->save();

            return response (['status' => true]);

        } catch (Exception $e) {
            return response (['status' => false]);
        }
    }
    public function destroy(Request $request)
    {
        try {
            ItensRota::destroy($request->id_item_rota);
            return response ([
                'status' => true
            ]);
        } catch (Exception $e) {
            return response ([
                'status' => false
            ]);
        }
    }

    public function importar()
    {
        $empresa = Cliente::select('clnome', 'clcodigo');
            if(\Auth::user()->usumaster == 'N'){
                $empresa->join('usuarios_clientes', 'uclcliente', '=', 'clcodigo')
                ->where('uclusuario', '=', \Auth::user()->id);
            }
            $empresa->where('clstatus', '=', 'A');
        $empresas = $empresa->get();

        return view('roteirizador.importar', compact('empresas'));
    }

    public function salvarImportacao(Request $request)
    {
        $file = $request->file;
        $cliente = $request->ircliente;

        $importar = new RoteirizadorService;
        $naoEncontrados = $importar->importacao($file, $cliente);
        return $naoEncontrados;
    }

    public function confirmar(Request $request)
    {
        $dados = explode(',', $request->codigo);
        $codigo = $dados[0];
        $cliente = $dados[9];
        $itensRota = Pontos::getPontosCargas($codigo, $cliente);

        if(empty($itensRota)){
            return response ([
                'mensagem' => 'Ponto não cadastrado!',
                'codigo' => '500'
            ]);
        }else{
            $roteirizador = new RoteirizadorService;
            $roteirizador->save($dados, $cliente);

            return response ([
                'mensagem' => 'Carga salva!',
                'codigo' => '200'
            ]);
        }
    }

    public function rotaManual()
    {
        $empresas = Cliente::select('clnome', 'clcodigo');
            if(\Auth::user()->usumaster == 'N') {
                $empresas->join('usuarios_clientes', 'clcodigo', 'uclcliente')
                    ->where('uclusuario', \Auth::user()->id);
            }
            $empresas = $empresas->get();
        return view('roteirizador.rotaManual', compact('empresas'));
    }

    public function regioes(Request $request)
    {
        $id = $request->id;
        $pontos = Pontos::where('pocodigocliente', $id)
            ->get();
        $regioes = Regioes::select('recodigo', 'redescricao')
            ->where('recliente', $id)
            ->get();


        return response ([
            'regioes' => $regioes,
            'pontos' => $pontos
        ]);
    }

    public function itens(Request $request)
    {
        $cliente = $request->cli;
        $id = $request->id ? : 0;
        $dia = $request->dia;
        $dia = \DateTime::createFromFormat('d/m/Y', $dia);
        $dia = $dia->format('d-m-Y');

        $itens = ItensRota::select('itens_rotas.*', 'podescricao',
            'potipo', 'poregiao', 'polatitude', 'polongitude',
            'poraio', 'pocodigo')
            ->leftJoin('pontos', 'ircodigoexterno', 'pocodigoexterno')
            // ->join('rotas','irrota','rocodigo')
            ->where('irdata', '=', $dia);
            // ->where('rostatus','V')
            //if(!empty($id)){
                //$itens->whereIn('poregiao', $id);
            //}
            $itens->where('irstatus', '=', 'I');
            $itens->where('ircliente', '=', $cliente);
            $itens->where('pocodigocliente', $cliente);
            $itens = $itens->get();

        $veiculos = Veiculo::select(\DB::raw('veiculos.*,
                (select sum(irpeso) from itens_rotas where irplaca = veiculos.veplaca) as peso,
                (select count(irpeso) from itens_rotas where irplaca = veiculos.veplaca) as qtde'))
            ->leftJoin('itens_rotas', 'irplaca', 'veplaca')
            ->where('veproprietario', $cliente)
            // ->where(function($query) use($dia) {
                ->where('vestatus', 'A')
                // ->orWhereRaw("(veplaca in (select irplaca from itens_rotas where irdata = '".$dia."'))");

            // })
            ->where('veroterizar', '=', 'S');
            // $veiculos->whereRaw("(veplaca not in (select irplaca from itens_rotas where irstatus = 'R' and irdata = '".$dia."'))");
            $veiculos->groupBy('veplaca', 'vecodigo', 'irstatus')
            ->orderBy('vemaxpeso', 'asc');
            $veiculos = $veiculos->get();
        // return $veiculos;

        $roteirizados = ItensRota::select('itens_rotas.*', 'p.podescricao',
            'p.potipo', 'p.poregiao', 'p.polatitude', 'p.polongitude', 'p.poraio',
            'p.pocodigo', 'po.polatitude as laretorno', 'po.polongitude as loretorno',
            'pt.polatitude as lasaida', 'pt.polongitude as losaida', 'rocodigo','rocor', 'rostatus')
            ->join('rotas', 'rocodigo', '=', 'irrota')
            ->join('pontos as pt', 'pt.pocodigo', '=', 'ropontosaida')
            ->join('pontos as po', 'po.pocodigo', '=', 'ropontoretorno')
            ->join('pontos as p', 'ircodigoexterno', 'p.pocodigoexterno')
            ->where('rostatus', 'P')
            ->where('ircliente', $cliente)
            ->where('p.pocodigocliente', $cliente)
            ->where('irdata', $dia)
            ->orderBy('irplaca', 'ASC')
            ->orderBy('irordem', 'ASC')
            ->get();

        if(count($roteirizados) > 0) {
            foreach ($roteirizados as $i => $rot) {
                $feitos[$rot->rocodigo][] = $rot;
            }

            $roteirizados = $feitos;
        }

        $regioes = [];
        if($id > 0)
            $regioes = Regioes::with('regioesCoordenadas')->whereIn('recodigo', $id)->get();

        return response ([
            'itens'         => $itens,
            'veiculos'      => $veiculos,
            'regioes'       => $regioes,
            'roteirizados'  => $roteirizados
        ]);
    }
    public function editarItens(Request $request)
    {
        $valor = $request->valor;
        $campo = $request->campo;
        $id = $request->id;

        $itemRota = ItensRota::find($id);
        $itemRota->$campo = $valor;
        $itemRota->save();
        return response ([
            'mensagem' => 'salvo com sucesso!',
            'codigo' => '200'
        ]);
    }

    public function rotaManualRotas(Request $request)
    {
        $arrayPontos = $request->latLong;

        $rota = [];
        $pontos = [];
        $kms = '';
        $tempo = '';

        if(count($arrayPontos) > 1) {
            $rotaHelper = new RoteirizadorHelper;
            $rota = $rotaHelper->defineRota($arrayPontos,  ['overview' => 'full', 'roundtrip' => 'false','geometries' =>  'polyline', 'destination' => 'last', 'source' => 'first']);
            $kms = ($rota->trips[0]->distance);
            $kms = (str_replace('.', ',', round($kms /1000, 2))).' Kms';
            $tempo = ($rota->trips[0]->duration);
            $helper = new DataHelper;
            $tempo = $helper->converteSegundosPorExtenso($tempo);
            $helper = new DataHelper;
            $pontos = $rota->waypoints;
            $rota = $rota->trips[0]->geometry;

        }
            return response ([
                'rota' => $rota,
                'ponto' => $pontos,
                'kms' => $kms,
                'tempo' => $tempo,
            ]);
    }

    public function requestRotasPolilyne(Request $request){
        //dd($request->latLong);
         $rotaHelper = new RoteirizadorHelper;
            $rota = $rotaHelper->defineRotaRoute($request->latLong,['geometries' =>  'polyline','overview' => 'full']);
            return ['response'=>$rota];
    }

    public function itensRota(Request $request)
    {
        $placa = $request->placa;
        $posicao = $request->posicaoPontos;
        $itens = $request->itens;
        $inicio = $request->inicio;
        $fim = $request->fim;
        $cli = $request->cli;
        $cliente = Cliente::find($cli);
        $cod = ItensRota::select('irrota')
            ->whereNotNull('irrota')
            ->orderBy('irrota', 'desc')
            ->first();

        $itensRota = ItensRota::select('itens_rotas.*', 'polatitude', 'polongitude', 'poraio', 'podescricao')
            ->join('pontos', 'pocodigoexterno', '=', 'ircodigoexterno')
            ->whereIn('ircodigo', $itens)
            ->where('pocodigocliente', $cli)
            ->orderBy('ircodigo', 'asc')
            ->get();

        if(count($itensRota) > 0) {
            $arrayPontos[] = $request->laloInicio;
            foreach ($itensRota as $i => $itm) {
                $arrayPontos[] = ['polatitude' => $itm->polatitude, 'polongitude' => $itm->polongitude];

            }
            $arrayPontos[] = $request->laloFim;
        }


        $rotaHelper = new RoteirizadorHelper;
        $rotas = $rotaHelper->defineRota($arrayPontos,  ['overview' => 'full', 'roundtrip' => 'false','geometries' =>  'polyline', 'destination' => 'last', 'source' => 'first']);

        $rotas = (array) $rotas;

        $valores = $rotas['trips'][0]->legs;
        $tempo = 0;
        $cub = 0;
        $qtde = 0;
        $peso = 0;
        $valor = 0;
        $kms = 0;
        $custo = 0;
        $cor = $request->color;
        foreach($itensRota as $i => $itns) {
            $tempo = $tempo + $valores[$i]->duration;
            $cub = $cub + $itns->ircubagem;
            $qtde = $qtde + $itns->irqtde;
            $peso = $peso + $itns->irpeso;
            $valor = $valor + $itns->irvalor;
        }
        $kms = ($rotas['trips'][0]->distance);
        $tempo = ($rotas['trips'][0]->duration);
        $tempo = gmdate("H:i:s", $tempo);
        $rota = new Rota;
        $rota->rotempo = $tempo;
        $rota->rokm = $kms;
        $rota->rocubagem = $cub;
        $rota->rocor = $cor;
        $rota->roqtde = $qtde;
        $rota->ropeso = $peso;
        $rota->rovalor = $valor;
        $rota->rodata = $itensRota[0]->irdata;
        $rota->roplaca = $placa;
        $rota->rocliente = $cli;
        $rota->ropontosaida = $inicio;
        $rota->ropontoretorno = $fim;
        $rota->rostatus = 'P';
        $rota->save();


        foreach ($itensRota as $i => $it) {
            $cont = isset($cod->irrota) ? $cod->irrota : 0;
            $it->irdistancia =  $valores[$i]->distance;
            $it->irtempoprevisto = $valores[$i]->duration;
            $it->irordem = $rotas['waypoints'][$i + 1]->waypoint_index;
            $it->irplaca = $placa;
            $it->irstatus = 'R';
            $it->irrota = $rota->rocodigo;
            $it->save();
        }

        return response ([
            'mesangem' => 'Salvo com sucesso!',
            'codigo' => '200'
        ]);
    }

    public function maisPedido(Request $request)
    {
        $id = $request->id;
        $po = Pontos::where('pocodigocliente', $id)
            ->leftJoin('itens_rotas','ircodigoexterno', 'pocodigoexterno')
            ->whereRaw('(
                            pocodigoexterno not in
                            (
                                select ircodigoexterno
                                from itens_rotas
                                where ircliente = '.$id.'
                            ))')
                            ->get();

        return response ([
            'pontos' => $po,
        ]);
    }
    public function novoPadido(Request $request)
    {
        $dados = $request->all();
        $ponto = Pontos::find($request->ponto);

        try {
            $iRota = new ItensRota($dados);
            $iRota->irvalor         = str_replace(',', '.', str_replace('.', '', $request->irvalor));
            $iRota->ircliente       = $ponto->pocodigocliente;
            $iRota->irnome          = $ponto->podescricao;
            $iRota->ircodigoexterno = $ponto->pocodigoexterno;
            $iRota->irstatus        = 'I';
            $iRota->save();
            return response ([
                'item' => $iRota,
                'ponto' => $ponto,
            ]);
        } catch (\Exception $e) {
            if($e->getCode() == '23505'){
                $mensagem = 'Item já cadastrado no banco de dados!';
            }else {
                $mensagem = 'Item não salvos tente novamante mais tarde!';
            }
            return response ([
                'mensagem' => $mensagem,
            ]);
        }
    }

    public function desassociarItem(Request $request)
    {
        $id = $request->id;
        $idRota = $request->idrota;
        $item = ItensRota::with('rota')
            ->select('itens_rotas.*', 'polongitude', 'polatitude',
            'poraio', 'pocodigo', 'ircodigo', 'podescricao', 'potipo')
            ->join('rotas', 'irrota', 'rocodigo')
            ->join('pontos', 'pocodigoexterno', '=', 'ircodigoexterno')
            ->whereRaw('pocodigocliente = itens_rotas.ircliente')
            ->where('ircodigo', $id)->first();

        $placa = $item->irplaca;
        $item->irplaca = null;
        $item->irstatus = 'I';
        $item->irrota = null;
        $item->save();

        $cliente = Cliente::find($item->ircliente);

        $arrayPontos = [];
        $itens = ItensRota::with('rota', 'rota.pontoSaida', 'rota.pontoRetorno')
            ->select('polongitude', 'polatitude', 'ircodigo', 'podescricao', 'irrota', 'pocodigo')
            ->join('pontos', 'pocodigoexterno', '=', 'ircodigoexterno')
            ->where('irrota', $idRota)
            ->where('irdata', $item->irdata)
            ->where('pocodigocliente', $item->ircliente)
            ->orderBy('irordem', 'ASC')
            ->get();

        if(count($itens) > 0) {
            $saida = $itens->first()->rota->pontoSaida;
            $arrayPontos[] = ['polatitude' => $saida->polatitude, 'polongitude' => $saida->polongitude];
            foreach ($itens as $i => $itm) {
                $arrayPontos[] = ['polatitude' => $itm->polatitude, 'polongitude' => $itm->polongitude];
                $idIndex[$itm->ircodigo] = $i;
            }
            $retorno = $itens->first()->rota->pontoRetorno;
            $arrayPontos[] = ['polatitude' => $retorno->polatitude, 'polongitude' => $retorno->polongitude];
        }

        $rota = [];
        $pontos = [];
        $kms = NULL;
        $tempo = '';
        if(count($arrayPontos) > 2) {
            $rotaHelper = new RoteirizadorHelper;
            $rota = $rotaHelper->defineRota($arrayPontos,  ['overview' => 'full', 'roundtrip' => 'false','geometries' =>  'polyline', 'destination' => 'last', 'source' => 'first']);
            $kms = ($rota->trips[0]->distance);
            $tempo = ($rota->trips[0]->duration);
            $helper = new DataHelper;
            $tempo = $helper->converteSegundosPorExtenso($tempo);
            $pontos = $rota->waypoints;
            $valores = $rota->trips[0]->legs;
            $rota = $rota->trips[0]->geometry;
        }
        $item->rota->rokm = $kms;
        $item->rota->save();

        $pontos = (array) $pontos;
        array_shift($pontos);
        array_pop($pontos);

        if(count($itens) > 0){

            foreach ($pontos as $i => $ponto) {
                $itens[$i]->irordem = $pontos[$i]->waypoint_index;
                $itens[$i]->irdistancia =  $valores[$i]->distance;
                $itens[$i]->irtempoprevisto = $valores[$i]->duration;

                $itens[$i]->save();
            }
        }


        for ($i = 0; $i < count($itens); $i++) {
            for ($j = $i + 1; $j < count($itens); $j++) {
                if($itens[$i]->irordem > $itens[$j]->irordem) {
                    $aux = $itens[$i];
                    $itens[$i] = $itens[$j];
                    $itens[$j] = $aux;
                }
            }
        }

        return response ([
            'itens' => $itens,
            'rota'  => $rota,
            'item'  => $item
        ]);


    }
    public function removeRota(Request $request)
    {
        $id = $request->id;


        try {
            $placa = $request->placa;
            \DB::table('itens_rotas')
                ->where('irrota', $id)
                ->update(['irplaca' => null, 'irstatus' => 'I', 'irrota' => null]);
                
            $rota = Rota::find($id);
            $rota->delete();

        return ([
            'mensagem' => 'Removido com sucesso!',
            'codigo' => '200'
        ]);
        } catch (\Exception $e) {
            return ([
                'mensagem' => 'Erro ao remover rota!',
                'codigo' => '500'
            ]);
        }
    }

    public function removeItem(Request $request)
    {
        $id = $request->id;
        $item = ItensRota::find($id);
        $item->delete();

        return response([
            'mensagem' => 'Deletado com sucesso!',
            'codigo' => '200'
        ]);
    }
    public function rotaAutomatica(Request $request)
    {
        $rotHelper = new RoteirizadorHelper();
        // dd($request->all());
        $clrequest = $request->prproprietario;
        $perequest = $request->pedidos;
        $verequest = $request->veiculos;
        $empresas = Cliente::select('clcodigo', 'clnome')->orderBy('clcodigo')->get();
        $veiculos = Veiculo::select('vecodigo', 'vedescricao', 'veproprietario')->orderBy('veproprietario')->get();
        $pedidos = ItensRota::select('ircodigo', 'ircliente', 'irnome')->orderBy('ircliente')->get();
        $result['ok'] = [];
        $result['erros'] = [];

        if ($clrequest && $perequest && $verequest) {
            $clientes = Cliente::select('clcodigo', 'cllatitude', 'cllongitude')->where('clcodigo','=', $clrequest)->orderBy('clcodigo')->get();

            $dadosPedidos = \DB::table('clientes as c')
                ->select(\DB::raw('ir.ircodigo, ir.ircodigoexterno, ir.irdocumento, ir.irnome, ir.irqtde, ir.ircubagem, ir.irpeso, ir.irvalor, p.poregiao, p.polatitude, p.polongitude, p.potipo'))
                ->leftJoin('itens_rotas as ir', 'ir.ircliente', '=', 'c.clcodigo')
                ->leftJoin('pontos as p', 'ir.ircodigoexterno', '=', 'p.pocodigoexterno')
                ->where('p.pocodigocliente', '=', $clrequest)
                ->whereIn('ir.ircodigo', $perequest)
                ->get();

            $dadosVeiculos = \DB::table('clientes as c')
                ->select(\DB::raw('vecodigo, veplaca, vemaxpeso, vecubagem, veautonomia, vemaxentregas, vemaxhoras, vecusto, vevelocidademax, vetipo, vehorainiciotrabalho, vehorafinaltrabalho, veestradaterra, vebalsas, vepedagios, array(select vrregiao from veiculo_regiao where vrveiculo = vecodigo) as vrregiao'))
                ->leftJoin('veiculos as v', 'c.clcodigo', '=', 'v.veproprietario')
                ->where('c.clcodigo', '=', $clrequest)
                ->whereIn('vecodigo', $verequest)
                ->get();

            //se for alterar avisar Adriano...
            $result = $rotHelper->montaCargas($dadosPedidos, $dadosVeiculos, $clientes[0]);

            $rotas = $result['cargas'];
            
            foreach ($rotas as $i => $route) {
                $qtde = 0;
                if(empty($route['itens'])) continue;
                    
                foreach ($route as $j => $rt) {
                    $sequencia = array_flip($route['itens']['totais']['entregas']->sequencia);
                    if(isset($rt->irqtde))
                        $qtde = $qtde + $rt->irqtde;
                }
                $rota = new Rota;
                $rota->ropontosaida = $request->prpontosaida;
                $rota->ropontoretorno = $request->prpontoretorno;
                $rota->rostatus = 'P';
                $rota->roplaca = $route['placa'];
                $rota->rocubagem = $route['itens']['totais']['cubagem'];
                // $rota->roplaca = $i;
                // $rota->rocubagem = $route['totais']['cubagem'];
                $rota->rocliente = $clrequest;
                $rota->ropeso = $route['itens']['totais']['peso'];
                $rota->rovalor = $route['itens']['totais']['valor'];
                $rota->roqtde = $qtde;
                $rota->rotemposegundos = $route['itens']['totais']['entregas']->trips[0]->duration;
                $rota->rokm = $route['itens']['totais']['entregas']->trips[0]->distance;
                $rota->rodata = $request->data_inicio;
                $rota->save();


                $count = 0; 
                foreach ($route['itens'] as $j => $rt) {
                    if(is_int($j)){
                        $item = ItensRota::find($j);

                        $item->irstatus = "R";
                        $item->irrota = $rota->rocodigo;
                        $item->irordem = $sequencia[$j] + 1;
                        $item->irdistancia = $route['itens']['totais']['entregas']->trips[0]->legs[$count]->distance;
                        $item->irtempoprevisto = $route['itens']['totais']['entregas']->trips[0]->legs[$count]->duration;
                        $item->save();
                        $count++;
                    }
                }
            }
            $veiculos = Veiculo::select('vecodigo','veplaca','veprefixo', 'vemaxpeso')
                ->leftJoin('rotas', 'roplaca', '=', 'veplaca')
                ->addSelect(\DB::raw('coalesce(vemaxpeso,0) as vemaxpeso'))
                ->addSelect(\DB::raw('coalesce(ropeso,0) as ropeso'))
                ->addSelect(\DB::raw('coalesce(vecubagem,0) as vecubagem'))
                ->addSelect(\DB::raw('coalesce(vemaxentregas,0) as vemaxentregas'))
            ->where('veproprietario',$clrequest)
            ->where('veroterizar','S')
            ->where(function($query){
                    $query->whereNull('rostatus')
                    ->orWhere('rostatus', '=', 'P');
                })
            ->where('vestatus','A')->get();

            return ['result' => $result, 'veiculos' => $veiculos];

        }
    }

    public function updateStatusRota(Request $r){
        $rotasId = $r->idsRota;
        $tam = sizeof($rotasId);
        $rotas = '';

        if(isset($rotasId)){
            $rotas = Rota::select()
            ->whereIn('rocodigo',$rotasId)
            ->update(['rostatus'=>'P']);
        }
        return ['updateRotas'=>$rotas];
    }
}
