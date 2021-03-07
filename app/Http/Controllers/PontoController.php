<?php

namespace App\Http\Controllers;

use App\Ponto;
use Carbon\Carbon;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PontoController extends Controller
{
    
    public function iniciarPonto(Request $request)
    {

        try{
            $now = Carbon::now(new DateTimeZone('America/Recife'))->format('Y-m-d H:i');
            $ponto = new Ponto();

            $user = Auth::user();

            
            $ponto->inicio = $now;
            $ponto->user_id = $user->id;
            $ponto->ativo = true;

            $trabalhoIniciado = Ponto::verificarOuIniciarPonto($ponto);

            if($trabalhoIniciado){
                return response()->json([
                    'success' => 'Ponto iniciado, Bom trabalho!',
                    'ponto_id' => $trabalhoIniciado->id
                    ] , 200);
            }else{
                $mensagem = 'Erro ao iniciar seu ponto , tente novamente';
                throw new Exception($mensagem);
            }

        }catch (Exception $e)
        {
            return response()->json(['erro' => $e->getMessage()] , 404);
        }
     


    }
}
