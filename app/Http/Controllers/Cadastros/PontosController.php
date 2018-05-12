<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pontos;
use App\Models\Chip;
use App\Models\Cliente;
use App\Models\MotoristaPonto;
use App\Models\Regioes;
use App\Models\PontosDisponibilidade;
use App\Models\RegioesCoordenadas;
use App\Models\Veiculo;
use App\Models\PerfilItens;

use App\Helpers\MapaHelper;
use App\Helpers\PontosHelper;
use App\Helpers\CheckIdHelper;
use Validator;
use Auth;
use DB;
use Illuminate\Validation\Rule;

class PontosController  extends Controller
{

    public function index()
    {
        if(Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome', 'clfantasia')->get();
            $adm = true;
        }else {
            $clientes = \Auth::user()->clientes;
            $adm = false;
        }

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return response([
                'clientes' => $clientes
            ]);
        }else{
           return view('cadastros.pontos.index', compact('clientes', 'adm'));
        }
    }

    public function importar()
    {
        if(Auth::user()->usumaster == 'S') {
            $pontos = Pontos::all();
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else {
            $pontos = Pontos::join('clientes', 'pocodigocliente', '=', 'clcodigo')
                ->join('usuarios_clientes', 'uclcliente', '=', 'clcodigo')
                ->where('uclusuario', '=', Auth::user()->id)
                ->get();
            $clientes = \Auth::user()->clientes;
        }

        return view('cadastros.pontos.importar', compact('clientes'));
    }

    public function salvarImportacao(Request $request)
    {
        set_time_limit(1000);
        $tipo_ponto = $request->tipo_ponto;
        $raio_ponto = $request->raio_ponto;
        $file       = $request->file;
        $clientes   = $request->empresa_importacao;

        $pontos_conflitantes = [];
        switch ($file->getclientoriginalextension()) {
            case 'kml':
                $xml = simplexml_load_file($file->getRealPath());

                foreach (explode(',',$clientes) as $i => $c) {
                    $cliente = Cliente::find($c);
                    foreach($xml->Document->Placemark as $item){
                        $ponto = new Pontos();
                        $ponto->podescricao = (string) $item->name;
                        $ponto->poraio = $raio_ponto;
                        $ponto->potipo = $tipo_ponto;
                        $ponto->pocodigoexterno = (string) $item->id;
                        $latlng = explode(",", ((string)$item->Point->coordinates));
                        $ponto->polatitude = $latlng[1];
                        $ponto->polongitude = $latlng[0];
                        $ponto->pocodigocliente = $cliente->clcodigo;
                        $ponto->poregiao = $this->verificaPontointoRegioes($ponto, $clientes);

                        if($this->verifica_ponto_existe($ponto)){
                            array_push($pontos_conflitantes,$ponto);
                        }else{
                            $ponto->save();
                            if(empty($ponto->pocodigoexterno)){
                                $pontoCodExterno = Pontos::where('pocodigoexterno', '=', $ponto->pocodigo)->get();
                                if($pontoCodExterno->isEmpty()){
                                    $ponto->pocodigoexterno = $ponto->pocodigo;
                                }else{
                                    $ponto->pocodigoexterno = $ponto->pocodigo.count($pontoCodExterno);
                                }
                                $ponto->save();
                            }
                        }
                    }
                }

                break;
            case 'txt':
                $arq = fopen($file->getRealPath(),'r');
                $arrayResponse = array();
                $j = 0;
                while(!feof($arq))
                    for($i=0; $i<1; $i++){
                        $j++;
                        if($conteudo = fgets($arq)){
                            if(substr($conteudo, -2) == ";\n"){
                                $conteudo = substr_replace($conteudo, "", -2);
                            }
                            if(substr($conteudo, -3) == ";\r\n"){
                                $conteudo = substr_replace($conteudo, "", -3);
                            }
                            $pt = explode(';', $conteudo);
                            $ponto = new Pontos();

                            if(count($pt) != 6) {
                                array_push($arrayResponse, "Código: $pt[0] Tamanho da linha incoreto por favor verifique o layout do arquivo!");
                                continue;
                            }elseif (empty(floatval($pt[2])) || empty(floatval($pt[3]))) {
                               //Call nomination to set lat, lng;
                               $endereco = PontosHelper::buscaEnderecoNominatim($pt[4]);
                               $ret[] = $endereco;
                               if($endereco != []){
                                   $ponto->polatitude = $endereco[0]->lat;
                                   $ponto->polongitude = $endereco[0]->lon;
                               }else{
                                   array_push($arrayResponse, 'Código: '.$pt[0].' Não foi possível definir latitude e longitude através do endereço!');
                                   continue;
                               }
                            }elseif (empty($pt[1])) {
                                array_push($arrayResponse, "Código: $pt[0] O arquivo de importação tem valores de descricão invalidos!");
                                continue;
                            }

                            if(empty($pt[4])) {
                                $ender = PontosHelper::buscaEnderecoNominatim($pt[2].','.$pt[3]);
                            }
                            
                            $raio = preg_replace("/[^0-9]/", "", $pt[5]);// Deixa somente os números na importacao de raio, havia um \n e um \r
                            $raio = !empty($raio) ? $raio : $raio_ponto;
                            if(!is_numeric($raio)) {
                                array_push($arrayResponse, "Código: $pt[0] O campo raio esta invalido por favor verifique!");
                                continue;
                            }

                            $pt[2] = str_replace(',', '.', $pt[2]); //arquivo TXT, quando latitude e longitude estão com "virgula" é importado somente o valor inteiro, exemplo: -27,02254522 importa -27 (tem que fazer o tratamento e importar tanto com "ponto", quanto com "virgula")
                            $pt[3] = str_replace(',', '.', $pt[3]); // ||  ||

                            $ponto->polatitude = isset($pt[2]) ? round($pt[2], 7) : $ponto->polatitude;
                            $ponto->polongitude= isset($pt[3]) ? round($pt[3], 7) : $ponto->polongitude;
                            $ponto->pocodigoexterno = $pt[0];
                            $ponto->podescricao     = $pt[1];
                            $ponto->poendereco      = $pt[4] ? : $ender[0]->display_name;
                            $ponto->poraio          = $raio;
                            $ponto->pocodigocliente = $clientes;
                            $ponto->potipo          = $tipo_ponto;
                            $ponto->poregiao        = $this->verificaPontointoRegioes($ponto, $clientes);

                            if($this->verifica_ponto_existe($ponto)){
                                array_push($pontos_conflitantes,$ponto);
                            }else{
                                $ponto->save();
                                if(empty($ponto->pocodigoexterno)){
                                    $pontoCodExterno = Pontos::where('pocodigoexterno', '=', $ponto->pocodigo)
                                        ->where('pocodigocliente', '=', \Auth::user()->cliente->clcodigo)
                                        ->get();
                                    if($pontoCodExterno->isEmpty()){
                                        $ponto->pocodigoexterno = $ponto->pocodigo;
                                    }else{
                                        $ponto->pocodigoexterno = $ponto->pocodigo.count($pontoCodExterno);
                                    }
                                    $ponto->save();
                                }
                            }
                    }
                }
        // dd($ret);
            break;
        }

        return response([
            'erros' => $arrayResponse,
            'pontos_conflitantes' => $pontos_conflitantes
        ]);
    }

    public function verificaPontointoRegioes($ponto, $clientes){
        $regioes = Regioes::where('recliente',$clientes)->get();
        $mHelper = new MapaHelper;
        $ponto = array('lat' => $ponto->polatitude , 'log' => $ponto->polongitude);
        foreach ($regioes as $key => $regiao) {
            $coordenadas = RegioesCoordenadas::where('rcregiao', $regiao->recodigo)->orderBy('rccodigo')->get();
            $existe = $mHelper->pointInPolygon($ponto, $coordenadas, $pointOnVertex = true);
            if($existe == 'inside' || $existe == 'vertex'){
                return $regiao->recodigo;
            }
        }
        return null;
    }

    public function verifica_ponto_existe($ponto){

        $nplat = str_replace(',', '.', (string)$ponto->polatitude);
        $nplong = str_replace(',', '.', (string)$ponto->polongitude);
        $p = '';
        $p = Pontos::where("polatitude",round(((double)$nplat), 10))
                    ->where("polongitude",round(((double)$nplong), 10))
                    ->where('potipo', $ponto->potipo)
                    ->where("pocodigocliente",$ponto->pocodigocliente)
                    ->where("podescricao",$ponto->podescricao)->count();

        if($p > 0){
            return true;
        }else if($ponto->pocodigoexterno != ""){
            $p = Pontos::where("pocodigocliente",$ponto->pocodigocliente)
                        ->where("pocodigoexterno",$ponto->pocodigoexterno)->count();

            if($p > 0){
                return true;
            }
        }

        return false;
    }

    public function show($id)
    {
        if(!CheckIdHelper::checkId('pontos', 'pocodigo', 'pocodigocliente', $id)) return redirect()->back();

        if(Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else {
            $clientes = Auth::user()->clientes;
        }

        $ponto = Pontos::with('cliente', 'regiao')->find($id);
        $dispon = $ponto->disponibilidade;
        $semana = ["Domingo", "Segunda-Feira", "Terça-Feira", "Quarta-Feira", "Quinta-Feira", "Sexta-Feira", "Sábado", "Segunda a sexta"];
        foreach ($dispon as $i => $disp) {
            $dispon[$i]->pdidiasemana = $semana[$disp->pdidiasemana];
        }
        return view('cadastros.pontos.cadastro', compact('ponto', 'clientes', 'dispon'));
    }

    public function cadastro()
    {
        $usuario = Auth::user();
        if($usuario->usumaster == 'S') {
            $clientes = Cliente::all();
        }else{
            $clientes = Auth::user()->clientes;
        }
        return view('cadastros.pontos.cadastro')->with('usuario', $usuario)->with('clientes', $clientes);
    }

    public function save(Request $request)
    {
       $dados = $request->all();

        $validator = Validator::make($dados, [
            'descricao' => 'required',
            'tipo' => 'required',
            'cllatitude' => 'required',
            'cllongitude' => 'required',
            'veproprietario' => 'required|numeric',
            'pocodigoexterno' => [
                'nullable',
                Rule::unique('pontos')->where(function ($query) use ($dados) {
                                                return $query->where('pocodigocliente', $dados['veproprietario']);
                                            })->ignore($dados['pocodigo'], 'pocodigo')
            ],
        ]);

        if($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if(!is_null($dados["pocodigo"])){
            $ponto = Pontos::find($dados["pocodigo"]);
            $disp = PontosDisponibilidade::where('pdicodigoponto', '=', $ponto->pocodigo)->get();
        }else{
            $ponto = new Pontos();
        }
        $ponto->pocodigoexterno = $request->pocodigoexterno;
        $ponto->podescricao = $request->descricao;
        $ponto->pocodigocliente = $request->veproprietario;
        $ponto->potipo = $request->tipo;
        $ponto->polatitude = $request->cllatitude;
        $ponto->polongitude = $request->cllongitude;
        $ponto->poendereco = $request->endereco;
        $ponto->poraio = ($request->clraio <= 10) ? 10 : $request->clraio;
        $ponto->poregiao = $request->poregiao;

        $ponto->save();
        if(empty($ponto->pocodigoexterno)){
            $pontoCodExterno = Pontos::where('pocodigoexterno', '=', $ponto->pocodigo)->get();
            if($pontoCodExterno->isEmpty()){
                $ponto->pocodigoexterno = $ponto->pocodigo;
            }else{
                $ponto->pocodigoexterno = $ponto->pocodigo.count($pontoCodExterno);
            }
        }

        $ponto->save();
        if(isset($request->hora_inicio)) {
            foreach ($request->hora_inicio as $i => $inicio) {
                if(is_numeric($request->semana[$i])) {
                    $disp = new PontosDisponibilidade();
                    $disp->pdihorainicio = $inicio;
                    $disp->pdihorafim = $request->hora_fim[$i];
                    $disp->pdidiasemana = $request->semana[$i];
                    $disp->pdicodigoponto = $ponto->pocodigo;
                    $disp->save();
                }
            }
        }


      return redirect('/painel/cadastros/pontos')->with('success', 'Ponto salvo!!!');

    }


    public function reload(Request $request)
    {

        if(isset($request->placa)){
            $id = Veiculo::select('veproprietario')
                ->where('veplaca', $request->placa)
                ->first();

            $request->clientesbusca = [$id->veproprietario];
            $request->tipo_ponto = 0;
        }
        if($request->clientesbusca ==  null){
            return response([
                'pontos' => []
            ]);
        }

        $pontos = $this->buscarPontos($request);

        return response([
            'pontos' => $pontos
        ]);
    }

    public function buscarPontosProximos(Request $request)
    {
        $pontoHelper = new PontosHelper;

        $permissoes = PerfilItens::select('perfil_permissoes.*')
            ->join('perfil_permissoes', 'ppperfilitens', '=', 'picodigo')
            ->join('perfis', 'pecodigo', '=', 'ppperfilcodigo')
            ->join('users', 'usuperfil', 'pecodigo')
            ->where('piid', '=', 'cadpontos')
            ->where('id', '=', \Auth::user()->id)
            ->first();

        $pontos = $pontoHelper->ordenaPontosProximidade($request);
        return response ([
            'pontos' => $pontos,
            'permissoes' => $permissoes,
        ]);
    }

    private function buscarPontos(Request $request){

        $tipo_ponto = $request->tipo_ponto;
        $clientesbusca = $request->clientesbusca;

        $pontos = Pontos::select('pocodigo', 'podescricao', 'potipo', 'pocodigocliente', 'poendereco','poraio');
        $pontos->with("cliente");

        if($clientesbusca != null && $clientesbusca != 'Selecione'){
            $pontos->whereIn('pocodigocliente', $clientesbusca);
            // $pontos->where('pocodigocliente', $clientesbusca);
        }elseif(Auth::user()->usumaster == 'S' && ($clientesbusca == null || $clientesbusca == 'Selecione')){
            $pontos->whereNotNull('pocodigocliente');
        }else{
            $pontos->where('pocodigocliente', '=', Auth::user()->usucliente);
        }
        if($tipo_ponto != "0"){
            $pontos->where('potipo', $tipo_ponto);
        }

        $pontos = $pontos->get();

        $json_pontos = json_encode($pontos);
        $m = json_decode($json_pontos);

        $pontos = [];
        foreach ($m as $key => $array_ponto) {
            if($array_ponto->pocodigocliente != null){
                $array_ponto->pocodigocliente = $array_ponto->cliente->clnome;
            }
            $pontos[$key] = $array_ponto;
        }

        return $pontos;
    }

    public function destroy($id)
    {

        if(!CheckIdHelper::checkId('pontos', 'pocodigo', 'pocodigocliente', $id)) return redirect()->back();

        $pontosMotoristas = MotoristaPonto::where('mpponto', $id )->get();
        try {
            foreach ($pontosMotoristas as $key => $pm) {
                MotoristaPonto::destroy($pm->mpcodigo);
            }
            Pontos::destroy($id);
            return response ([
               'mensagem' => 'Ponto removido!',
               'codigo' => 200
            ]);
        } catch(\Exception $e) {
               return response ([
        'mensagem' => ' não foi possivel remover esse ponto',
        'codigo' => 500
      ]);
        }
    }

    public function destroyPontoMapa(Request $request, $id)
    {
      $pontosMotoristas = MotoristaPonto::where('mpponto', $id )->get();
      foreach ($pontosMotoristas as $key => $pm) {
          MotoristaPonto::destroy($pm->mpcodigo);
      }

      Pontos::destroy($id);
      return response ([
        'mensagem' => 'Ponto removido!',
        'codigo' => '200'
      ]);
    }

    public function salvarConflito(Request $rq)
    {
        $nplat = str_replace(',', '.', (string)$rq->polatitude);
        $nplong = str_replace(',', '.', (string)$rq->polongitude);

        $ponto = Pontos::where("polatitude",round(((double)$nplat), 10))
                   ->where("polongitude",round(((double)$nplong), 10))
                   ->where('potipo', $rq->potipo)
                   ->where("pocodigocliente",$rq->pocodigocliente)
                   ->where("podescricao",$rq->podescricao)->first();

        // $ponto = Pontos::where("polatitude",$rq->polatitude)
        //             ->where("polongitude",$rq->polongitude)
        //             ->where("pocodigocliente",$rq->pocodigocliente)
        //             ->where("podescricao",$rq->podescricao)->first();

        if($ponto == null){
            $ponto = Pontos::where("pocodigocliente",$rq->pocodigocliente)
                    ->where("pocodigoexterno",$rq->pocodigoexterno)->first();
        }
        // $ponto = Pontos::find((int)$request->pocodigo);
        // $ponto->fill($rq->data);
        $ponto->polatitude = $rq->polatitude;
        $ponto->polongitude = $rq->polongitude;
        $ponto->pocodigocliente = $rq->pocodigocliente;
        $ponto->podescricao = $rq->podescricao;
        $ponto->poraio = $rq->poraio;
        $ponto->potipo = $rq->potipo;
        $ponto->poregiao = $rq->poregiao;
        $ponto->save();
    }

    public function buscaInicial(Request $request)
    {

      $usuario = Auth::user();
      $cliente = Cliente::find($usuario->usucliente);

      $coleta =  $request->coleta;
      $entrega = $request->entrega;
      $referencia = $request->referencia;


      if($coleta == 'true' || $entrega == 'true' || $referencia == 'true'){
        $pontos = DB::table('pontos');
        //$pontos->where('pocodigocliente',$cliente->clcodigo);

        if($coleta == 'true'){
          $pontos->orWhere('potipo','C');
        }
        if($entrega == 'true'){
          $pontos->orWhere('potipo','E');
        }
        if($referencia == 'true'){
          $pontos->orWhere('potipo','P');
        }

      }else{
        return 0;
      }

      $pontos = $pontos->get();
      foreach ($pontos as $key => $ponto) {
        if($ponto->pocodigocliente != $cliente->clcodigo){
            unset($pontos[$key]);
        }
      }

      //$pontosj = json_encode($pontos);

      return response([
          'pontos' => $pontos
      ]);
    }
    public function cliente(Request $request)
    {
        $id = $request->id;
        if(empty($id)){
            return response ([
                'pontos' => ''
            ]);
        }
        $pontos = Cliente::select('pocodigo','podescricao', 'potipo', 'poendereco', 'poraio')
            ->join('pontos', 'pocodigocliente', '=', 'clcodigo')
            ->whereIn('clcodigo', $id)->get();

        return response ([
            'pontos' => $pontos,
        ]);
    }
    public function disponibilidade(Request $request)
    {
        $id = $request->id;
        $disp = PontosDisponibilidade::find($id);
        $disp->delete();
        return response([
            'mensagem' => 'Deletado com sucesso!',
            'status' => '200',
        ]);
    }
    public function regiao(Request $request)
    {
        $pontos = $request->pontos;
        $regioes = $request->regiao;
        $cliente = $request->cliente;
        if(empty($regioes[0])){
            $regioes = Regioes::with('regioesCoordenadas')
                ->where('recliente', '=', $cliente)
                ->get();
        }

        $mapa = new MapaHelper;

        foreach ($regioes as $i => $regiao) {
            foreach ($pontos as $j => $ponto) {
                $existe = $mapa->pointInPolygon($ponto, $regiao->regioesCoordenadas, $pointOnVertex = true);
                if($existe == 'inside'){
                    $regiaoPonto = $regiao;
                    break;
                }
            }
        }
        if(isset($regiaoPonto)){
            return response ([
                'regiao' => $regiaoPonto,
            ]);
        }else{
            return response ([
                'mensagem' => 'Região não encontrada',
                'codigo' => '204'
            ]);
        }
    }

    public function pesquisaPontoEndereco(Request $request)
    {
        $pontoHelper = new PontosHelper;
        // dd($pontoHelper->buscaEnderecoNominatim($request->q));
        return $pontoHelper->buscaEnderecoNominatim($request->q);
    }

    public function updateMapa(Request $request)
    {
        $dados = $request->all();
        $ponto = Pontos::find($request->pocodigo);
        $ponto->fill($dados);
        $ponto->save();

        return response([
            'ponto' => $ponto,
        ]);
    }

    public function reassociar(Request $request)
    {
        $ponto = Pontos::find($request->pocodigo);
        $ponto->polatitude = $request->polatitude;
        $ponto->polongitude = $request->polongitude;
        $ponto->save();

        return response([
            'ponto' => $ponto,
        ]);
    }
}
