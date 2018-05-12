<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Telefones;

class TelefonesController extends Controller
{
    public function criar($id, $telefone)
    {
        foreach($telefone as $fone){
            $telefone = new Telefones();
            $telefone->tlnumero = $fone;
            $telefone->tlproprietario = $id;
            $telefone->save();
        }
    }
    public function editar($id, $telefone)
    {
        $oldFone = Telefones::where('tlproprietario', '=', $id)->get();

        foreach($oldFone as $old) {
            if(!in_array($old->tlnumero, $telefone)){
                $old->delete();
            }
        }
        foreach($telefone as $fone){
            $tel = Telefones::firstOrCreate(['tlnumero' => $fone, 'tlproprietario' => $id]);
        }
    }
    public function excluir(Request $request)
    {
        $id = $request->id;
        $telefone = Telefones::find($id);
        if($telefone){
            $telefone->delete();
        }
        return response([
            'mensagem' => 'Deletado com sucesso',
            'status' => '200',
        ]);
    }
}
