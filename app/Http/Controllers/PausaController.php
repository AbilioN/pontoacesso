<?php

namespace App\Http\Controllers;

use App\Pausa;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTimeZone;
use Exception;

class PausaController extends Controller
{
    public function iniciarPausa(Request $request)
    {
        try{

            $data = $request->all();

            $now = Carbon::now(new DateTimeZone('America/Recife'))->format('Y-m-d H:i');
     
     
            $pausaIniciada = Pausa::verificarOuIniciarPausa($data);
     
            if($pausaIniciada)
            {
                return response()->json([ 
                    'sucesso' => 'Pausa iniciada, bom descanso!',
                    'pausa_id' => $pausaIniciada->id    
                ], 200);
            }

        }catch(Exception $e)
        {
            return response()->json(['erro' => $e->getMessage()] ,404);
        }
  
    }
}
