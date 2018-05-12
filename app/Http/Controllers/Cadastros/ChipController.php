<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chip;
use App\Models\Modulo;
use Validator;
use Excel;
use DB;

class ChipController  extends Controller
{

    private $rules = [
      'status' => 'required',
      'iccid' => 'required',
      'numero' => 'required|unique:chips,chnumero',
      'operadora' => 'required',
      'franquiamb' => 'numeric',
      'franquiasms' => 'numeric'
    ];

    public function index(Request $request)
    {
        return view('cadastros.chips.index');
    }

    public function listar(Request $request)
    {
      $status = $request->status;
      $modulo = $request->modulo;
      $tipo = $request->tipo;
      $chips = Chip::All();

      if($status  != null){
        if($status == 'ativo'){
          $chips = Chip::where('chstatus', '=', 'A')->get();
        }elseif ($status == 'inativo') {
          $chips = Chip::where('chstatus', '=', 'I')->get();
        }
      }

      if($modulo != null){
        if($modulo == 'com_modulo'){
          foreach ($chips as $key => $chip) {
            $m = Modulo::where('mosim' , '=', $chip->chcodigo)->first();
            if($m == null){
              unset($chips[$key]);
            }
          }
        }elseif($modulo == 'sem_modulo'){
          foreach ($chips as $key => $chip) {
            $m = Modulo::where('mosim' , '=', $chip->chcodigo)->first();
            if($m != null){
              unset($chips[$key]);
            }
          }
        }
      }

        foreach ($chips as $i => $chip) {
            if($chip->modulo != null){
                $chips[$i]->modulo = $chip->modulo->mocodigo;
            }
            // var_dump($chip);
            if($chips[$i]->choperadora == 1) {
                $chips[$i]->choperadora = 'Vivo';
            }else if($chips[$i]->choperadora == 2){
                $chips[$i]->choperadora = 'Claro';
            }else if($chips[$i]->choperadora == 3){
                $chips[$i]->choperadora = 'Tim';
            }else if($chips[$i]->choperadora == 4){
                $chips[$i]->choperadora = 'Oi';
            }

        }

        return response([
            'chips' => $chips,
        ]);

    }

    public function show($id)
    {
        try {
            $chip = Chip::find($id);
        } catch (\Exception $e) {
            return back();
        }

      return view('cadastros.chips.cadastro')->with('chip', $chip);
    }

    public function cadastro()
    {
    	return view('cadastros.chips.cadastro');
    }

    public function save(Request $request)
    {
      $dados = $request->all();

      //Edição
      if(isset($dados["chcodigo"]) && !is_null($dados["chcodigo"])){
          $validator = Validator::make($dados, [
            'status' => 'required',
            'iccid' => 'required',
            'numero' => 'required|unique:chips,chnumero,'.$dados['chcodigo'].',chcodigo',
            'operadora' => 'required',
            'franquiamb' => 'numeric',
            'franquiasms' => 'numeric'
          ]);
          if($validator->fails()) {
              return redirect()->back()
                  ->withErrors($validator)
                  ->withInput();
          }
          $chip = Chip::find($dados["chcodigo"]);
      }else{
          $validator = Validator::make($dados, $this->rules);
          if($validator->fails()) {
              return redirect()->back()
                  ->withErrors($validator)
                  ->withInput();
          }
          $chip = new Chip();
      }
      $chip->chstatus = $dados["status"];
      $chip->chnumero = $dados["numero"];
      $chip->iccid = $dados["iccid"];
      $chip->choperadora = $dados["operadora"];
      $chip->chfranquiamb = $dados["franquiamb"];
      $chip->chfranquiasms = $dados["franquiasms"];
      $chip->chcusto = (float) str_replace(",",".",$dados["custo"]);

      $chip->save();

      return redirect('/painel/cadastros/chips')->with('success', 'Chip salvo com sucesso!!!');
    }

    public function destroy($id)
    {
      Chip::destroy($id);
      return redirect()->back()->with('success', 'Chip removido com sucesso!!!');
    }

    public function alterarStatus(Request $request)
    {
        $chcodigo = $request->chcodigo;
        $chip = Chip::find($chcodigo);

        if($chip->chstatus == "A") {
            $chip->chstatus = "I";
            $status = "I";
        } else if ($chip->chstatus == "I") {
            $chip->chstatus = "A";
            $status = "A";
        }

        $chip->save();

        return $status;
    }

}
