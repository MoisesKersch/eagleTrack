<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pontos;
use App\Models\Motorista;
use App\Models\MotoristaPonto;
use App\Models\GrupoMotorista;
use App\Models\Bilhete;
use App\Models\Veiculo;
use App\Models\Cliente;
use App\Helpers\DataHelper;
use App\Helpers\PontosHelper;
use Illuminate\Database\Eloquent\Collection;
use DB;
use Excel;
use Auth;

class ProximidadeController extends Controller
{
    public function listar()
    {
        //$gmotoristas = GrupoMotorista::where('gmcliente', '=', Auth::user()->usucliente)->get();
        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }

        $veiculos = Veiculo::where('veproprietario', '=', Auth::user()->usucliente)
            ->where('vestatus', '=', 'A')
            ->get();

        return view('relatorios.proximidade.listar', compact('veiculos','clientes'));
    }

    public function relatorio(Request $request)
    {
        $placas = '';
        if(empty($request->clientes) && empty($request->buscar)) {
            return response([
                'dados' => '',
                'placas' => ''
            ]);
        }
        if(!$request->buscar)
            $placas = Veiculo::whereIn('veproprietario', $request->clientes)->get();

        $dados = $this->query($request);
        return response([
            'dados' => $dados,
            'placas' => $placas
        ]);
    }

    public function exportar(Request $request)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $dia = new \Datetime();
        $type = $request->type;
        $dados = $this->query($request);

        $datahorainicio = $request['data_inicio']." ".$request['hora_inicio'];
        $datahorafim = $request['data_fim']." ".$request['hora_fim'];

        if($type == 'pdf'){
            $pdf = \PDF::loadView('relatorios.proximidade.pdf', compact('dados', 'datahorainicio', 'datahorafim'));
            return $pdf->stream();
        }else{
            $dados = json_decode(collect($dados),true);
            foreach ($dados as $key => $dado){
                unset($dados[$key]['bilatlog']);
                unset($dados[$key]['mtcodigo']);
            }
            return Excel::create('relatorioproximidade', function($excel) use ($dados){
                $excel->sheet('relatorioproximidade', function($sheet) use ($dados)
                {
                    $sheet->fromArray($dados);
                    $sheet->row(1, array(
                        'Placa', 'Nome Motorista', 'Prefixo','Descricao','Dia Semana','Data\Hora','Tempo Parado','Proximo'
                    ));
                });
            })->download($type);
        }
    }

    public function query($request)
    {
        //Itens de Requests
        //----------------------------------------------------------------------
        $data_inicio = $request['data_inicio'];
        $hora_inicio = $request['hora_inicio'];
        $data_fim = $request['data_fim'];
        $hora_fim = $request['hora_fim'];
        $veiculos = $request['buscar'];
        $grupo_motoristas = $request['grupo_motoristas'];

        //Variáveis
        //----------------------------------------------------------------------
        $dados = null;
        $clcodigo = \Auth::user()->usucliente;
        $helper_data = new DataHelper();
        $diasdasemana =  array("Domingo","Segunda-Feira","Terça-Feira","Quarta-Feira","Quinta-Feira","Sexta-Feixa","Sábado");

        //Agrupamento de data_hora
        //----------------------------------------------------------------------
        $date = \DateTime::createFromFormat('d/m/Y H:i', $data_inicio." ".$hora_inicio);
        $datahorainicio = $date->format('Y-m-d H:i:s');
        $date = \DateTime::createFromFormat('d/m/Y H:i', $data_fim." ".$hora_fim);
        $datahorafim = $date->format('Y-m-d H:i:s');


        if($datahorainicio < $datahorafim){

            //caso não haja veiculos selecionado, busca os veiculos do cliente;
            if($veiculos == null || $veiculos == 'undefined'){
                $veiculos = DB::table('veiculos')->select('vecodigo')->where('veproprietario', '=', $clcodigo)
                    ->where('vestatus', '=', 'A')
                    ->get();
                $veiculos = $veiculos->toArray();

                //transforma o array em string separando por vírgula;
                foreach ($veiculos as $key => $veiculo) {
                    $key == 0? $nveiculos = $veiculo->vecodigo.'' : $nveiculos = $nveiculos.','.$veiculo->vecodigo;
                }
            }elseif(is_array($veiculos)){
               $nveiculos = implode(',',$veiculos);
            }else{
               $nveiculos = $veiculos;
            }

         //SELECT busca os bilhetes gerados dentor da tada e com ignição ligada do cliente especificado...
         //Este select está em formato puro porque não foi identificado um método para fazer a subconsulta da forma necessária;
         //---------------------------------------------------------------------
          $bilhetes = DB::select(DB::raw("select biplaca, bilatlog, mtcodigo, mtnome, veprefixo, vedescricao, dia_semana, bidataevento, tempo_parado
                            from(select mtcodigo, biplaca, bilatlog, mtnome, veprefixo, vedescricao,
                                (select to_char(min(bidataevento), 'DD/MM/YYYY HH:MI:SS')) as bidataevento ,
                                (select age(max(bidataevento),min(bidataevento))) as tempo_parado,
                                (select extract(dow from  bidataevento)) as dia_semana,
                                min(bidataevento) as dataini,
                                max(bidataevento) as datafim,
                                count(bicodigo) as total
                    	from \"bilhetes\"
                    	left join \"motoristas\" on \"bilhetes\".\"bimotorista\" = \"motoristas\".\"mtcodigo\"
                    	left join \"clientes\" on \"clientes\".\"clcodigo\" = \"motoristas\".\"mtcliente\"
                    	left join \"veiculos\" on \"veiculos\".\"veplaca\" = \"bilhetes\".\"biplaca\"
                    	where \"bimovimento\" = 0
                    	and \"biignicao\" = 0
                    	and \"mtcliente\" = $clcodigo
                    	and \"vecodigo\" in ($nveiculos)
                    	group by biplaca, bilatlog,mtcodigo, mtnome, veprefixo, vedescricao, dia_semana
                    	order by \"biplaca\" asc)
                    	as f
                    	where \"dataini\" >= '$datahorainicio'
                    	and \"datafim\" <= '$datahorafim'"));

            //Buscar todos os pontos do cliente
            //----------------------------------------------------------------------
            $pontosCliente = Pontos::where('pocodigocliente',$clcodigo)->get();

            //Buscar os pontos em que ocorreu bilhetes dentro deste raio
            //----------------------------------------------------------------------
            $helper = new PontosHelper();
            $proximidade = $helper->relacionaPosicaoAPontoMaisProximo($pontosCliente, $bilhetes);

            foreach ($proximidade as $i => $p) {
                //Agrupar por latitude e longitude
                //--------------------------------------------------------------
                if($i != 0 &&
                    $p->biplaca == $proximidade[$i-1]->biplaca &&
                    $p->mtcodigo == $proximidade[$i-1]->mtcodigo &&
                    $p->dia_semana == $proximidade[$i-1]->dia_semana &&
                    $p->proximo == $proximidade[$i-1]->proximo &&
                    $p->veprefixo == $proximidade[$i-1]->veprefixo)
                {
                    $proximidade[$i]->tempo_parado = $helper_data->somaHora([$proximidade[$i]->tempo_parado,$proximidade[$i-1]->tempo_parado]);
                    unset($proximidade[$i-1]);
                }
            }

            foreach ($proximidade as $i => $p) {
                //Altera data da semana recebendo um array de dias correspondente a posição do array;
                //------------------------------------------------------------------
                $proximidade[$i]->dia_semana = $diasdasemana[$p->dia_semana];
            }
        }

      return $proximidade;
    }

    public function buscarPlacasGrupoMotorista(Request $request){
      $gmcodigo = $request['gm_codigo'];
      $clcodigo = \Auth::user()->usucliente;

      if($gmcodigo != null){
          $placas = DB::table('veiculos')
                      ->join('motoristas', 'motoristas.mtcodigo', '=', 'veiculos.vemotorista')
                      ->join('grupo_motorista', 'grupo_motorista.gmcodigo', '=', 'motoristas.mtgrupo')
                      ->select('veiculos.veplaca', 'veiculos.vecodigo')
                      ->where('motoristas.mtgrupo', $gmcodigo)
                      ->where('veproprietario', $clcodigo)
                      ->where('vestatus', '=', 'A')
                      ->get();
      }else{
          $placas = DB::table('veiculos')
                      ->select('veiculos.veplaca', 'veiculos.vecodigo')
                      ->where('veproprietario',$clcodigo)
                      ->where('vestatus', '=', 'A')
                      ->get();
      }

      return response(['placas' => $placas]);
      // return response()->placas;

    }

    public function getVeiculosCliente(){
      $placas = Veiculo::select('veplaca','vecodigo')->where('veproprietario', '=', \Auth::user()->usucliente)
          ->where('vestatus', '=', 'A')
          ->get();

      return response(['placas' => $placas]);
      // return response()->placas;
    }

    //////////////////////////////////////////////////////////////////////////////////////
    //---Relacionar posição com ponto próximo,
    //function relacionaPosicaoAPontoMaisProximo($pontos, $posicoesParado){

            //foreach ($posicoesParado as $key => $posicao) {
                //$latlog = explode(',', $posicao['bilatlog']);

                //foreach ($pontos as $ponto) {
                    ////pegar a latitude e longitude do bilhete e calcular distancia entre bilhete e ponto
                    //$distancia = round(calculaDistanciaLatLon($latlog[0], $latlog[1], $ponto['polatitude'], $ponto['polongitude']),2);

                    ////--- caso distancia seja menor que raio do ponto, atribui sua descrção para este ponto
                    //if($distancia < $ponto['poraio']){
                            //$posicoesParado[$key]['proximo'] = $ponto['podescricao'];
                            //break;
                    //}
                //}
            //}
        //return $posicoesParado;
    //}
}
