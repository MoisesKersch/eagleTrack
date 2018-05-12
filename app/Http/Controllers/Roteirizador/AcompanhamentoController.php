<?php

namespace App\Http\Controllers\Roteirizador;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use DB;
use App\Models\ItensRota;
use App\Models\Veiculo;
use App\Models\Rota;
use App\Models\Regioes;
use App\Services\RoteirizadorService;
use App\Helpers\RoteirizadorHelper;
use App\Models\Justificativa;

class AcompanhamentoController extends Controller
{
    public function acompanhar(){
        $clientes = Cliente::getClientesUserLogado();
        return view('roteirizador.acompanhamento.acompanhar', compact('clientes'));
    }

    public function buscarRotas(Request $r){
        $clientes = $r->clientes;
        $d = explode('/', $r->data);
        $menosD = ($d[0] - 1).'/';
        $data = \DateTime::createFromFormat('d/m/Y', $r->data);
        $status = "'".str_replace(',', "', '", $r->status)."'";
        $data = $data->format('d-m-Y');

        $rotas = Rota::select('vecodigo', 'veprefixo', 'veplaca','clnome','irplaca')
            ->addSelect('clnome','irrota','irdata_hora_evento','irordem','irstatus')
            ->addSelect('irnome','irdata_hora_evento','irhodometro','polatitude','polongitude')
            ->addSelect('*')
            ->addSelect(DB::raw("(select mtnome from motoristas where mtcodigo = veiculos.vemotorista) as mtnome"))
            ->addSelect(DB::raw("(select mtnome from motoristas where mtcodigo = veiculos.veajudante) as ajnome"))
            ->join('veiculos','veplaca','=','roplaca')
            ->join('itens_rotas','itens_rotas.irrota','=','rocodigo')
            ->join('clientes','clcodigo','=','veproprietario')
            ->join('pontos','pocodigoexterno','=','ircodigoexterno')
            ->whereRaw("(rodata = '".$data."' and rostatus in (".$status.") or (rodata < '".$data."' and rostatus <> 'F'))");
            if($r->status != null){
                $rotas = $rotas->whereIn('rostatus', explode(',',$r->status));
            }
            $rotas = $rotas->whereIn('ircliente', $clientes)
            ->whereIn('pocodigocliente', $clientes)
            ->where('irstatus','!=','I')
            ->orderBy('irrota','ASC'   )
            ->orderBy('irordem','ASC')
            ->get();

            // return $rotas;

        $agrupado = $this->agruparPorRota($rotas);

        return response([
            'dados' => $agrupado
        ]);
    }

    public function agruparPorRota($dados){
        $rotaHelper = new RoteirizadorHelper;
        $placas = [];
        $old = '';
        //Agrupa por placa
        $j = 0;
        $i = 0;

        foreach($dados as $r) {
            if($i == 0 || $r->rocodigo != $old_rota){
                $j = 0;
                $old_rota = $r->rocodigo;
            }

            if($r->rocodigo == $old_rota){
                //atributos da rota
                $placas[$r->rocodigo]['rocodigo'] = $r->rocodigo;
                $placas[$r->rocodigo]['rohodometroinicio'] = $r->rohodometroinicio;
                $placas[$r->rocodigo]['rohodometrofim'] = $r->rohodometrofim;
                $placas[$r->rocodigo]['rodatahorainicio'] = $r->rodatahorainicio;
                $placas[$r->rocodigo]['rodatahorafim'] = $r->rodatahorafim;
                $placas[$r->rocodigo]['ropontosaida'] = $r->ropontosaida;
                $placas[$r->rocodigo]['ropontoretorno'] = $r->ropontoretorno;
                $placas[$r->rocodigo]['rokm'] = $r->rokm;
                $placas[$r->rocodigo]['roplaca'] = $r->roplaca;
                $placas[$r->rocodigo]['rodata'] = $r->rodata;
                $placas[$r->rocodigo]['rostatus'] = $r->rostatus;
                $placas[$r->rocodigo]['veprefixo'] = $r->veprefixo;
                $placas[$r->rocodigo]['vedescricao'] = ($r->vedescricao != null) ? ($r->vedescricao) : ('');
                $placas[$r->rocodigo]['clfantasia'] = $r->clfantasia;
                $placas[$r->rocodigo]['mtnome'] = ($r->mtnome != null) ? ($r->mtnome) : ('');
                $placas[$r->rocodigo]['mtajudante'] = ($r->ajnome != null) ? ($r->ajnome) : ('');

                    //atributos dos ítens da rota
                    $placas[$r->rocodigo]['item'][$j]['irnome'] = $r->irnome;
                    $placas[$r->rocodigo]['item'][$j]['irordem'] = $r->irordem;
                    $placas[$r->rocodigo]['item'][$j]['irstatus'] = $r->irstatus;
                    $placas[$r->rocodigo]['item'][$j]['irdata_hora_evento'] = $r->irdata_hora_evento;
                    $placas[$r->rocodigo]['item'][$j]['irtempoparado'] = ($r->irtempoparado != null) ? ($r->irtempoparado) : (0);
                    $placas[$r->rocodigo]['item'][$j]['irqtde'] = ($r->irqtde != null) ? ($r->irqtde) : (0);
                    $placas[$r->rocodigo]['item'][$j]['irhodometro'] = $r->irhodometro;
                    $placas[$r->rocodigo]['item'][$j]['irtempoprevisto'] = $r->irtempoprevisto;
                    $placas[$r->rocodigo]['item'][$j]['irdistancia'] = $r->irdistancia;
                    $placas[$r->rocodigo]['item'][$j]['polatitude'] = $r->polatitude;
                    $placas[$r->rocodigo]['item'][$j]['polongitude'] = $r->polongitude;

                $j = $j + 1;
            }else{
                $j = 0;
            }
            $old_rota = $r->rocodigo;
            $i = $i + 1;
        }
        return $placas;
    }

    public function getJustificativa($idcliente){
        $teste = Justificativa::select('jucodigo','judescricao')
        ->where('jucliente',$idcliente)
        ->get();
        return response(['response'=>$teste]);
    }

    public function itensRotaNaoFinalizada($idrota){
        $teste = ItensRota::select('ircodigo','irnome','ircliente')
        ->where('irrota',$idrota)
        ->where('irstatus','!=','F')
        ->get();
        return json_encode(['response'=>$teste]);
    }

    public function updateItensRota(Request $request){
        $data = $request->input('teste');
            $id = $request->input('idcliente');
            $lista = array();

            if(isset($data)){
            foreach($data as $key => $value) {
                array_push($lista, $data[$key]['ircodigo']);
            }

            $itensRota = ItensRota::select()
            ->where('irrota',$id)
            ->where('irstatus','!=','F')
            ->get();

            foreach ($itensRota as $key => $value) {
                ItensRota::select()
                ->where('ircodigo',$lista[$key])
                ->where('irstatus','!=','F')
                ->update(['irjustificativa'=>$data[$key]['jucodigo']]);
            }
        }

        Rota::where('rocodigo',$id)
        ->update(['rostatus'=>'F']);
        return ['status'=>200];
    }

}
