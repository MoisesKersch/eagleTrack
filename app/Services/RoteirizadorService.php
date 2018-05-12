<?php

namespace App\Services;

use App\Models\Pontos;
use App\Models\ItensRota;

class RoteirizadorService
{
	public function importacao($file, $cliente)
	{
        $naoEncontrados = [];

        switch ($file->getclientoriginalextension()) {
            case 'txt':
                $linhas = file($file);
                foreach ($linhas as $i => $linha) {
                    $cargas = '';
                    $cargas = explode(';', $linha);
                    if(count($cargas) != 9) {
                        return ([
                            'mensagem' => 'Formato de arquivo invalido!',
                            'codigo' => '415'
                        ]);
                    }elseif(empty($cargas[0])){
                        return ([
                            'mensagem' => 'Arquivo com codigo externo invalido por favor verifique!',
                            'codigo' => '417'
                        ]);
                    }/*elseif(empty($cargas[3])){
                        return ([
                            'mensagem' => 'Arquivo com data invalida por favor verifique!',
                            'codigo' => '417'
                        ]);
                    }*/
                    $cargas[4] = str_replace(',', '.', $cargas[4]);
                    $cargas[5] = str_replace(',', '.', $cargas[5]);
                    $cargas[6] = str_replace(',', '.', $cargas[6]);
                    $cargas[7] = str_replace(',', '.', $cargas[7]);
                    $buscar = $this->buscarCodigoExterno($cargas, $cliente);
                    if(!$buscar){
                        $nao = $this->save($cargas, $cliente);
                        if(isset($nao['codigo']))
                            $naoSalvo[] = $nao;
                    }else{
                        $cargas[] = $cliente;
                        $naoEncontrados[] = $cargas;
                    }
                }
                // dd($naoSalvo);
    			if(empty($naoEncontrados)) {
    				return ([
    					'mensagem' => 'Cargas importadas com sucesso!',
    					'codigo' => '200',
                        'naoSalvo' => isset($naoSalvo) ? $naoSalvo : []
					]);
    			}else{
    				return ([
                        'naoEncontrados' => $naoEncontrados,
                        // não endenteu pergunte ao adriano ;)
                        'naoSalvo' => isset($naoSalvo) ? $naoSalvo : []
                    ]);
    			}
			break;
    	}
	}
    public function buscarCodigoExterno($cargas, $cliente)
    {
    	$ponto = Pontos::getPontosCargas($cargas[0], $cliente);
    	if($ponto){
    		return false;
    	}else {
    		return true;
    	}
    }

    public function save($dados, $cliente)
    {
        try {
            $itensRota = new ItensRota;
            $itensRota->ircodigoexterno   = $dados[0];
            $itensRota->ircliente         = $cliente;
            $itensRota->irdocumento       = $dados[1];
            $itensRota->irnome            = $dados[2];
            $itensRota->irdata            = $dados[3] ? : date("Y-m-d");
            $itensRota->irqtde            = $dados[4];
            $itensRota->ircubagem         = $dados[5];
            $itensRota->irpeso            = $dados[6];
            $itensRota->irvalor           = $dados[7];
            $itensRota->irrota            = null;
            $itensRota->irstatus          = 'I';
            $itensRota->irplaca           = '';
            $itensRota->save();

            return 'sucesso!';
        } catch (\Exception $e) {
            return [
                'erro' => 'Documento já existe!',
                'codigo' => '500',
                'documento' => $dados[1]
            ];
        }
    }
}
