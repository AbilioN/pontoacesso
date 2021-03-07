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
            $agora = Carbon::now(new DateTimeZone('America/Recife'))->format('Y-m-d H:i');

            $data = $request->all();

            $pausaIniciada = Pausa::verificarOuIniciarPausa($data , $agora);
            $parcialTrabalhadoHoje = Ponto::calcularTotalParcial($data['ponto_id'], $agora , $pausaIniciada);
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
}
