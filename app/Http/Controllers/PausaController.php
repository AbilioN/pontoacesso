<?php

namespace App\Http\Controllers;

use App\Pausa;
use App\Ponto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\DB;

class PausaController extends Controller
{
    public function iniciarPausa(Request $request)
    {
        DB::beginTransaction();
        try{
            $hoje = Carbon::now(new DateTimeZone('America/Recife'))->format('Y-m-d H:i');
            $agora = Carbon::now(new DateTimeZone('America/Recife'))->format('h:i:s');


            $data = $request->all();

            $pausaIniciada = Pausa::verificarOuIniciarPausa($data , $hoje);

            $parcialTrabalhadoHoje = Ponto::calcularTotalParcial($data['ponto_id'], $hoje , $pausaIniciada);
            if($pausaIniciada)
            {
                DB::commit();
                return response()->json([ 
                    'sucesso' => 'Pausa iniciada, bom descanso!',
                    'total_trabalhado' => $parcialTrabalhadoHoje,
                    'pausa_id' => $pausaIniciada->id,
                ], 200);
            }

        }catch(Exception $e)
        {   
            DB::rollBack();
            return response()->json(['erro' => $e->getMessage()] ,404);
        }
  
    }

    public function terminarPausa(Request $request)
    {
        DB::beginTransaction();
        try{

            $agora = Carbon::now(new DateTimeZone('America/Recife'))->format('Y-m-d H:i');

            $pausaId = $request->pausa_id;
            $pausaEncerrada = Pausa::terminarPausa($pausaId, $agora);

            // list($pontoAtualizado, $tempoTrabalhado) = Ponto::atualizarPonto($pausaEncerrada);

            $pontoAtualizado = true;
            // dd($pontoAtualizado);
            // $tempoRestante = Ponto::calcularTempoRestante($agora, $pontoAtualizado, $pausaEncerrada);

            if($pausaEncerrada && $pontoAtualizado){
                DB::commit();

                return response()->json([
                    'sucesso' => 'Bem vindo de volta, bom trabalho!',
                    'tempo_trabalhado' => $tempoTrabalhado
                ], 200);
            }

        }catch(Exception $e)
        {
            DB::rollBack();
            return response()->json(['erro' => $e->getMessage()] ,404);

        }
  
    }
}
