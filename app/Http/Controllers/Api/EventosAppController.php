<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Response;
use DateTime;
use App\Models\EventosApp;
use App\Models\Veiculo;
use App\Http\Controllers\Controller;
use DB;

class EventosAppController extends Controller
{
  public function inserirEventosApp(Request $request)
  {
      $idusuarioapp = $request->input('idusuarioapp');
      $data = $request->input('data');
      $array_eventos = json_decode(json_encode($data), true);
      //dd($array_eventos);

      foreach ($array_eventos as $evento){
          $eventosApp = new EventosApp();
          $eventosApp->eatipoevento = $evento['codigoevento'];
          $eventosApp->eacodigomotorista = $evento['codigomotorista'];
          $eventosApp->eadataevento = $evento['dataprocessamento'];
          $eventosApp->eadataprocessamento = new DateTime();
          $eventosApp->ealatitude = $evento['localizacao_evento_latitude'];
          $eventosApp->ealongitude = $evento['localizacao_evento_longitude'];
          if(isset($evento['eaparametro'])){
              $eventosApp->eaparametro = $evento['eaparametro'];
          }
          $eventosApp->save();

          if($eventosApp->eatipoevento == 11 && $eventosApp->ealatitude != '' && $eventosApp->ealongitude != ''){
              //---Caso não possua módulos próximos retornará 0;
              $sql = DB::Raw("select buscaModuloProximo($eventosApp->ealongitude,$eventosApp->ealatitude,(select usacliente from \"usuario_apps\" where usacodigo = $idusuarioapp))");

              $modulo_proximo = DB::select($sql)[0]->buscamoduloproximo;

              if($modulo_proximo != '0'){
                // Neste momento tenho o módulo mais próximo a este usuário app.
                // Setar o motorista para este modulo na tabela veículos.
                // Atualizar bilhetes gerados a partir da data_evento do app e setar para o novo módulo
                DB::select(DB::raw('update veiculos set vemotorista = '.$eventosApp->eacodigomotorista.'
                          where vemodulo = '.$modulo_proximo.'
                          and veproprietario = (
                              select usacliente from "usuario_apps" where usacodigo = '.$idusuarioapp.')'));

                $data_evento = date("Y-m-d h:m:s", strtotime($eventosApp->eadataevento));

               // Atualizar também os bilhetes que foram gerados por este módulo desde $eventosApp->jadataEvento
               DB::select(DB::raw("update bilhetes set bimotorista = $eventosApp->eacodigomotorista
                         where bimodulo = $modulo_proximo
                         and bidataevento > '$data_evento'"));
              }
          }

          if($eventosApp->eatipoevento == 12){
              if(isset($eventosApp->eaparametro)){
                  $array = explode('|', $eventosApp->eaparametro);
                  $veiculo = Veiculo::where('veplaca', $array[0])->first();
                  if(isset($veiculo)){
                      //remover este motorista ou ajudante de outros veiculos
                      $this->removerMotoristaAjudanteVeiculos($idusuarioapp,$eventosApp->eacodigomotorista);
                      if($array[1] ==  'M'){
                          // alterar veículo para o motorista que veio neste evento..
                          $veiculo->vemotorista = $eventosApp->eacodigomotorista;
                      }else if($array[1] ==  'A'){
                          // alterar veículo para o ajudante que veio neste evento..
                          $veiculo->veajudante = $eventosApp->eacodigomotorista;
                      }
                      $veiculo->save();
                  }
              }

          }else if($eventosApp->eatipoevento == 13){
                // alterar veículo para o motorista nullo
                if(isset($eventosApp->eaparametro)){
                    $array = explode('|', $eventosApp->eaparametro);
                    $veiculo = Veiculo::where('veplaca', $array[0])->first();
                    if(isset($veiculo)){
                        if($array[1] ==  'M'){
                            $veiculo->vemotorista = null;
                        }else if($array[1] ==  'A'){
                            $veiculo->veajudante = null;
                        }
                        $veiculo->save();
                    }
                }
          }
      }
      return json_encode(['status'=>'ok']);
  }

  function removerMotoristaAjudanteVeiculos($idusuarioapp, $eacodigomotorista){
      $veiculos = Veiculo::whereVemotorista($eacodigomotorista)->get();
      foreach ($veiculos as $veiculo) {
          $veiculo->vemotorista = null;
          $veiculo->save();
      }

      $veiculos = Veiculo::whereVeajudante($eacodigomotorista)->get();
      foreach ($veiculos as $veiculo) {
          $veiculo->veajudante = null;
          $veiculo->save();
      }

  }

}
