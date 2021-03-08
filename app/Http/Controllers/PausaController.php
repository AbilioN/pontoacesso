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

            $hoje = Carbon::now(new DateTimeZone('America/Recife'))->format('Y-m-d');

            $agora = Carbon::now(new DateTimeZone('America/Recife'))->format('h:i:s');

            $requestData = $request->all();
            $pausaIniciada = Pausa::verificarOuIniciarPausa($requestData , $hoje, $agora);

        

            $ponto = Ponto::find($requestData['ponto_id']);
            $parcialTrabalhadoHoje = Ponto::calcularDiferenca($ponto->inicio ,$pausaIniciada->inicio , $pausaIniciada);
            if($ponto->nro_pausas >= 1){
                $ponto->total_trabalhado = Ponto::adicionarHoras($ponto->total_trabalhado ,  $parcialTrabalhadoHoje);
            }else{
                $ponto->total_trabalhado = $parcialTrabalhadoHoje;
            }

            $pontoSalvo = $ponto->save();

            if($pausaIniciada && $pontoSalvo)
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

            $hoje = Carbon::now(new DateTimeZone('America/Recife'))->format('Y-m-d H:i');
            $agora = Carbon::now(new DateTimeZone('America/Recife'))->format('h:i:s');

            $pausaId = $request->pausa_id;
            $pausaEncerrada = Pausa::terminarPausa($pausaId, $agora);
            $pontoAtualizado = Ponto::atualizarPonto($pausaEncerrada);
            if($pausaEncerrada && $pontoAtualizado){
                DB::commit();
                return response()->json([
                    'sucesso' => 'Bem vindo de volta, bom trabalho!',
                    'tempo_trabalhado' => $pontoAtualizado->total_trabalhado,
                    'ativo' => $pontoAtualizado->ativo
                ], 200);
            }

        }catch(Exception $e)
        {

            DB::rollBack();
            return response()->json(['erro' => $e->getMessage()] ,404);

        }
  
    }
}
