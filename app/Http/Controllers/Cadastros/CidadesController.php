<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cidade;

class CidadesController extends Controller
{
    public function buscar(Request $request)
    {
        $cidade = $request->cidade;
        $cidades = Cidade::where('cinome', 'ILIKE', '%'.trim($cidade).'%')->get();
        return response([
            'cidades' => $cidades,
        ]);
    }
}
