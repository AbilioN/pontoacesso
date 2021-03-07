<?php

namespace App\Http\Controllers;

use App\PontoAcesso;
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
            // por datetimezone e formar em configuracao global
            $now = Carbon::now(new DateTimeZone('America/Recife'))->format('Y-m-d H:i');

            $ponto = new PontoAcesso();

            $user = Auth::user();

            
            $ponto->inicio = $now;
            $ponto->user_id = $user->id;
            $ponto->ativo = true;

            $trabalhoIniciado = PontoAcesso::verificarOuIniciarPonto($ponto);

            if($trabalhoIniciado){
                return response()->json(['success' => 'Ponto iniciado, Bom trabalho!'] , 200);
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
