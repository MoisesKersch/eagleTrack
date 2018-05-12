<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Justificativa;

class JustificativasController extends Controller
{
    public function cadastro(Request $request)
    {
    	$dados = $request->all();

    	$descricao = $request->judescricao;

    	$just = Justificativa::where('judescricao', '=', $descricao)->first();

    	if(empty($just)) {
            $clientes = explode(',', $request->jucliente);

            foreach ($clientes as $i => $cliente) {
                if($cliente > 0) {
                    $just = new Justificativa($dados);
                    $just->jucliente = $cliente;
                    $just->save();
                }
            }

    		return response ([
    			'codigo' => '200',
    			'message' => 'salvo com sucesso!'
    		]);
    	}else{
    		return response ([
    			'codigo' => '500',
    			'message' => 'Justificativa já cadastrada!'
    		]);
    	}
    }
}
