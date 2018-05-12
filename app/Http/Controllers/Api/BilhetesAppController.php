<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Response;
use DateTime;
use DB;
use App\Models\Bilhete;
use App\Models\Cliente;
use App\Models\Modulo;
use App\Models\IgnicaoVeiculos;
use App\Http\Controllers\Controller;

class BilhetesAppController extends Controller
{
    public function inserirBilhetesApp(Request $request)
    {

      $idusuarioapp = $request->input('idusuarioapp');
      $data = $request->input('data');
      $array_bilhetes=json_decode(json_encode($data), true);

      foreach ($array_bilhetes as $bilhete){
          $b = new bilhete();
          $b->bimotorista = $bilhete['motorista'];
          $b->biignicao = $bilhete['ignicao'];
          $b->bimodulo = $bilhete['codigomodulo'];
          $b->bivelocidade = $bilhete['velocidade'];
          $b->biplaca = $bilhete['placa'];
          $b->bimovimento = $bilhete['movimento'];
          $b->bidirecao = $bilhete['direcao'];
          $b->bilatlog = $bilhete['latitude'].",".$bilhete['longitude'];
          $b->bidataprocessado = $bilhete['dataprocessamento'];
          $b->bidataevento = $bilhete['dataevento'];
          $b->save();
      }
       return Response::json([
         'data'=>'ok'
       ]);
    }

    public function lastBilhete($placa){
      $motivo = Bilhete::select()
      ->where('biplaca',$placa)
      ->where(function($query){
        $query->where('bimotivotransmissao','9')
              ->orWhere('bimotivotransmissao','10');
      })->orderBy('bidataevento','DESC')
      ->limit(1)
      ->get();
        return $motivo;
    }

    public function lastIgnicao($placa){
      $motivo = IgnicaoVeiculos::select()
      ->where('ivplaca',$placa)
      ->where(function($query){
        $query->where('ivmotivotransmissao','9')
              ->orWhere('ivmotivotransmissao','10');
      })->orderBy('ivdataevento','DESC')
      ->limit(1)
      ->get();
        return $motivo;
    }

    public function isInicioIgnicao($placa){
      if(($this->lastIgnicao($placa))[0]->ivmotivotransmissao == 9)
        return true;
      return false;
    }

    public function isInicioBilhetes($placa){
      if(($this->lastBilhete($placa))[0]->bimotivotransmissao == 9)
        return true;
      return false;
    }

    public function updateBilhetes(Request $request){
      $placa = $request->input('placa');
      $motorista = $request->input('motorista');
      $ajudante = $request->input('ajudante');
      $bidataevento = '';

      if($this->isInicioBilhetes($placa)){
        $bidataevento = ($this->lastBilhete($placa))[0]->bidataevento;
      }

        if($bidataevento != ""){
          $createDate = new DateTime($bidataevento);
          $strip = $createDate->format('d-m-Y');
          if($strip == date('d-m-Y')){
            Bilhete::select('bimotorista','biajudante')
            ->where('bidataevento','>=',$bidataevento)
            ->where('biplaca',$placa)
            ->update(['bimotorista'=>$motorista,'biajudante'=>$ajudante]);
          }
      }
    return Response::json(['response' => 'true']);
    }

    public function updateIgnicaoVeiculo(Request $request){
      $placa = $request->input('placa');
      $motorista = $request->input('motorista');
      $ajudante = $request->input('ajudante');
      $ivdataevento = '';

      if($ajudante == null){
        $ajudante = 0;
      }

      if($motorista == null){
        $motorista = 0;
      }

      if($this->isInicioIgnicao($placa)){
        $ivdataevento = ($this->lastIgnicao($placa))[0]->ivdataevento;
      }

      if($ivdataevento != ""){
        $createDate = new DateTime($ivdataevento);
        $strip = $createDate->format('d-m-Y');
        if($strip == date('d-m-Y')){
            IgnicaoVeiculos::select('ivmotorista','ivajudante')
            ->where('ivdataevento','>=',$ivdataevento)
            ->where('ivplaca',$placa)
            ->update(['ivmotorista'=>$motorista,'ivajudante'=>$ajudante]);
        }

      }
    return Response::json(['response' => 'true']);
    }

    //FUNCAO WEBSERVICE QUE RETORNA TODAS AS POSCIOES DA DATA REQUISITADA
    public function getHistoricoPosicoes(Request $request){
        $cliente = new cliente();
        $chave;
        if(!isset($request->chave)){
            return Response::json([
                'status' => 'Chave de API Obrigatória!'
            ]);
        }else{
          $chave = $request->chave;
            $codCliente = $cliente->getClienteApiKey($chave);
            if(!$codCliente){
                return Response::json([
                                    'status' => 'Chave Inválida!'
                                    ]);
            }
        }
        # SE NAO FOR ENVIADO DATA, ENTAO SERVICO RETORNA APENAS ULTIMA POSICAO DE CADA VEICULO
        if(!isset($request->data)){
            $modulos = Modulo::select(DB::raw("to_char(moultimoevento,'DD/MM/YYYY HH24:MI:SS') as dataevento,
                                               moultimalat||','||moultimalon as latlon,
                                               moultimaignicao,
                                               moultimavelocidade,
                                               moultimohodometro,
                                               coalesce(mtnome,'Nenhum') as mtnome,
                                               moultimoendereco,
                                               coalesce(veplaca,'AAA-0000') as veplaca,
                                               coalesce(redescricao,'Nenhuma') as redescricao,
                                               coalesce(podescricao,'Nenhum') as podescricao,
                                               coalesce(moultimareferencia,'Nenhum') as bireferencia"
                                      ))
                              ->leftJoin('motoristas', 'moultimomotorista','=','mtcodigo')
                              ->leftJoin('veiculos','vemodulo','=','mocodigo')
                              ->leftJoin('regioes','recodigo','=','moultimaregiao')
                              ->leftJoin('pontos','pocodigo','=','moultimoponto')
                              ->where('veproprietario','=',$codCliente)
                              ->where('vestatus','=','A')
                              ->get();
            if(count($modulos) > 0){
                $retorno = array();
                $retorno['status'] = 'OK';
                foreach($modulos as $modulo){
                    $retorno[$modulo['veplaca']][] = array(
                                                        'data_evento' => $modulo['dataevento'],
                                                        'lat_lon'       => $modulo['latlon'],
                                                        'ignicao'       => $modulo['moultimaignicao'],
                                                        'velocidade'    => $modulo['moultimavelocidade'],
                                                        'hodometro'     => ($modulo['moultimohodometro']/1000),
                                                        'motorista'     => $modulo['mtnome'],
                                                        'endereco'      => $modulo['moultimoendereco'],
                                                        'regiao'        => $modulo['redescricao'],
                                                        'ponto'         => $modulo['podescricao'],
                                                        'referencia'    => $modulo['bireferencia']
                                                    );
                }
                return Response::json($retorno);
            }else{
                return Response::json([
                                      'status' => 'Não há dados nos filtros requisitados.'
                                      ]);
            }
          }
        else{
            #verificar se data eh valida
            $data = explode('/', $request->data);
            if(count($data) <> 3){
                return Response::json([
                                    'status' => 'Data inválida!'
                                    ]);
            }else{
                $data = checkdate($data[1],$data[0],$data[2]);
                if(!$data){
                    return Response::json([
                                    'status' => 'Data inválida!'
                                    ]);
                }
            }
            $dataI = $request->data." 00:00:00";
            $dataF = $request->data." 23:59:59";
            $placa = $request->placa;
            $limite = $request->limite;
            $bilhetes = Bilhete::select(DB::raw("to_char(max(bidataevento),'DD/MM/YYYY HH24:MI:SS') as bidataevento,
                                                 bilatlog,biignicao,bivelocidade,bihodometro,
                                                 coalesce(mtnome,'Nenhum') as mtnome,biendereco,biplaca,
                                                 coalesce(redescricao,'Nenhuma') as redescricao,
                                                 coalesce(podescricao,'Nenhum') as podescricao,
                                                 coalesce(bireferencia,'Nenhum') as bireferencia"
                                              ));
            $bilhetes->leftJoin('veiculos', 'biplaca', '=', 'veplaca')
                    ->leftJoin('clientes', 'veproprietario', '=', 'clcodigo')
                    ->leftJoin('motoristas', 'bimotorista', '=', 'mtcodigo')
                    ->leftJoin('regioes', 'biregiao', '=', 'recodigo')
                    ->leftJoin('pontos', 'pocodigo', '=', 'biponto')
                    ->whereBetween('bidataevento',[$dataI,$dataF])
                    ->where('clcodigo', '=', $codCliente)
                    ->groupBy('bilatlog','biignicao','bivelocidade','bihodometro','mtnome','biendereco','biplaca',
                              'redescricao','podescricao','bireferencia')
                    ->orderBy('bidataevento','desc');
            if(isset($placa))
                $bilhetes->where('biplaca',$placa);
            if(isset($limite))
                $bilhetes->limit($limite);
            $bilhetes = $bilhetes->get();
            if(count($bilhetes) > 0){
                $retorno = array();
                $retorno['status'] = 'OK';
                foreach($bilhetes as $bilhete){
                    $retorno[$bilhete['biplaca']][] = array(
                                                        'data_evento' => $bilhete['bidataevento'],
                                                        'lat_lon'       => $bilhete['bilatlog'],
                                                        'ignicao'       => $bilhete['biignicao'],
                                                        'velocidade'    => $bilhete['bivelocidade'],
                                                        'hodometro'     => ($bilhete['bihodometro']/1000),
                                                        'motorista'     => $bilhete['mtnome'],
                                                        'endereco'      => $bilhete['biendereco'],
                                                        'regiao'        => $bilhete['redescricao'],
                                                        'ponto'         => $bilhete['podescricao'],
                                                        'referencia'    => $bilhete['bireferencia']
                                                    );
                }
                return Response::json($retorno);
            }else{
                return Response::json([
                'status' => 'Não há dados nos filtros requisitados.'
            ]);
            }
        }
    }

    public function getBilhetesByModulo($mocodigo){
        $lastBilhete = DB::table('bilhetes')->join('modulos', 'bimodulo', 'mocodigo')
            ->join('modulo_modelos', 'mmcodigo', 'momodelo')
            ->where('bimodulo', $mocodigo)
	    ->whereRaw('bidataevento >= current_date')
	    ->orderBy('bidataevento', 'DESC')
	    ->first();
        return Response::json([
            'response' => $lastBilhete
        ]);

    }

}

