<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Response;
use DateTime;
use App\Models\Bilhete;
use App\Models\Veiculo;
use App\Models\Modulo;
use App\Models\UsuarioApp;
use App\Services\VeiculoService;
use App\Services\TempoParadoService;
use App\Services\AcionamentoPortasService;
use DB;
use App\Http\Controllers\Controller;

class VeiculosController extends Controller
{
  public function findByCodCliente(Request $request)
  {
      $codigo_cliente = $request->input('codigo_cliente');

      $veiculos = Veiculo::select('*')
                ->addSelect(DB::raw('coalesce(vemodulo, 0 ) as vemodulo'))
                ->addSelect(DB::raw('coalesce(vemotorista, 0 ) as vemotorista'))
                ->addSelect(DB::raw('coalesce(vemaxpeso, 0 ) as vemaxpeso'))
                ->addSelect(DB::raw('coalesce(vecubagem, 0 ) as vecubagem'))
                ->addSelect(DB::raw('coalesce(veautonomia, 0 ) as veautonomia'))
                ->addSelect(DB::raw('coalesce(vemaxentregas, 0 ) as vemaxentregas'))
                ->addSelect(DB::raw('coalesce(vevelocidademax, 0 ) as vevelocidademax'))
                ->where('vemodulo', '!=', 0)
                ->where('veproprietario', '=', $codigo_cliente)
                ->where('vestatus','A')->get();
      if (count($veiculos) <= 0) {
         abort(404, 'Nenhum veículo encontrado');
      }

      return Response::json([
        'data'=>$veiculos
      ]);
    }

    public function rotaVeiculo(Request $r){
        $rota = new VeiculoService();
        return $rota->rotaVeiculo($r);
    }

    public function excessosVelocidades(Request $request){
        $rota = new VeiculoService();
        return $rota->excessosVelocidades($request);
    }

    public function paradas(Request $request)
    {
        $tempoParadoService = new TempoParadoService();
        return $tempoParadoService->paradas($request);
    }

    public function acionamentoPortas(Request $request)
    {
        $acpService = new AcionamentoPortasService;
        return $acpService->getAcionamentoPortas($request);
    }

    public function relacionaVeiculoMotorista(Request $request){

      $usuarioApp = new UsuarioApp();
      $motorista = new Motorista();
      $veiculo = new Veiculo();
      $idusuarioapp = $request->input('idusuarioapp');

      $usuarioApp = UsuarioApp::where('usacodigo', '=', $idusuarioapp)->first();
      if ($usuarioApp === null) {
         abort(404, 'Usuário Não encontrado');
      }

      if($usuarioApp->usaperfil == "M"){

        $motorista = Motorista::where('mtcodigo', '=', $idusuarioapp)->first();
        if(isset($motorista)){
         $veiculo = Veiculo::where('vemotorista', '=', $motorista->mtcodigo)->first();
        }
      }

      //--todo calcular distância entre veículos e este motorista cuja posição é representada po lat-lng que vem request

      //TODO --if successe return success, else return erros description
    }

    public function findByModulo(Request $request)
    {
        $modulos_str = $request->input('modulos');
        #array str_split ( string $modulos [, int $split_length = 1 ] )
        $modulos_str = str_replace('[', '', $modulos_str);
        $modulos_str = str_replace(']', '', $modulos_str);
        $modulos_str = str_replace(' ','', $modulos_str);
        $array = explode(',', $modulos_str);

        foreach ($array as $id_modulo){
          $veiculo = Veiculo::select('*')->addSelect(DB::raw('coalesce(vemotorista, 0) as vemotorista'))->where('vemodulo', '=', $id_modulo)->get();
          $veiculos[] = $veiculo;
        }

        if (count($veiculos) <= 0) {
           abort(404, 'Nenhum veículo encontrado');
        }

       return Response::json([
           'data'=>$veiculos
       ]);
    }

    public function findAllByUserApp(Request $request)
    {

        $idusuarioapp = $request->input('idusuarioapp');

        $usuarioApp = UsuarioApp::where('usacodigo', '=', $idusuarioapp)->first();
        if ($usuarioApp === null) {
           abort(404, 'Usuário Não encontrado');
        }

        if($usuarioApp->cliente->clstatus != 'A'){
            abort(405, 'Cliente Inativo');
        }

        if($usuarioApp->usuario->usuativo != 'S'){
            abort(406, 'Usuário Inativo');
        }

        $veiculos = Veiculo::select('vecodigo','vedescricao','veprefixo','veplaca','mocodigo',
                DB::raw("coalesce(moultimalon, '0') as moultimalon"), DB::raw("coalesce(moultimalat,'0') as moultimalat"),
                DB::raw("coalesce(moultimadirecao,0) as moultimadirecao"),
                DB::raw("coalesce(moultimaignicao,0) as moultimaignicao"),
                DB::raw("replace(moultimoendereco,'Nenhum','Sem Posição') as moultimoendereco"),
                'vetipo','veprefixo','clfantasia',"mtnome",'mtcodigo','veajudante','moultimoevento')
                ->addSelect(DB::raw("(select mtnome from motoristas where mtcodigo = veiculos.veajudante) as ajnome"))
                ->join('modulos','mocodigo','vemodulo')
                ->join('clientes','clcodigo','veproprietario')
                ->leftJoin('motoristas','mtcodigo','vemotorista')
                ->where('vestatus', 'A')
                ->whereIn('veproprietario',function($query) use($idusuarioapp){
                    $query->select('uclcliente')->from('usuarios_clientes')
                        ->join('usuario_apps','usausuario','uclusuario')
                        ->where('usacodigo', $idusuarioapp);
                });

        if($request->input('idveic') > 0){
            $veiculos->where('vecodigo',$request->input('idveic'));
        }

        $veiculos = $veiculos->get();


        foreach ($veiculos as $key => $veiculo){

            if($veiculo["mocodigo"] != null){
                $totalKm = Modulo::find($veiculo["mocodigo"])->kmsPercorridosHoje();
            }else{
                $totalKm = 0;
            }

            $veiculos[$key]["total_km"] = $totalKm;
        }

        return Response::json([
          'data' => $veiculos
        ]);
    }


    public function getPosicoesVeiculo(Request $request)
    {
        $idmodulo = $request->input('idmodulo');
        $inicial = $request->input('data_inicial');
        $final = $request->input('data_final');

        $modulo = Modulo::find($idmodulo);

        $data_inicial = new DateTime($inicial);
        $data_final = new DateTime($final);

        if($data_inicial > $data_final){
            abort(400, 'Data inicial maior que data final');
        }else{
            $bilhetes = Bilhete::where('bimodulo', '=', $modulo->mocodico)
            ->whereBetween('bidataevento', array( $data_inicial , $data_final))->get();

            if (count($bilhetes) <= 0) {
                abort(404, 'Nenhum bilhete encontrado');
            }else{
                return Response::json([
                    'data'=>$bilhetes
                ]);
            }
        }
    }


    public function findAllVeiculosLastPosition(Request $request)
    {
        $codigo_cliente = $request->input('codigo_cliente');
        //->leftJoin('bilhetes', 'veiculos.vemodulo', '=', 'bilhetes.bimodulo')

        $veiculos = DB::table('veiculos')
                ->leftJoin('modulos', 'veiculos.vemodulo', '=', 'modulos.mocodico')
                ->leftJoin('bilhetes', function ($join){
                    $join->on('veiculos.vemodulo', '=', 'bilhetes.bimodulo')
                    ->max('bilhetes.bidataevento');
                      // ->max('bilhetes.bidataevento');
                })
                ->where('moproprietario', '=', $codigo_cliente)
                ->get();

        return Response::json([
          'data'=>$veiculos
        ]);
    }



}
