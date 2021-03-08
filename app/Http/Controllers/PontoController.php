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
            $ponto = new Ponto();

            $hoje = Carbon::now(new DateTimeZone('America/Recife'))->format('Y-m-d');

            $agora = Carbon::now(new DateTimeZone('America/Recife'))->format('h:i:s');

            $user = Auth::user();

            $ponto->inicio = $agora;
            $ponto->user_id = $user->id;
            $ponto->ativo = true;

            $trabalhoIniciado = Ponto::verificarOuIniciarPonto($ponto, $hoje, $agora);
            
            
            
            if($trabalhoIniciado){
                return response()->json([
                    'success' => 'Ponto iniciado, Bom trabalho!',
                    'ponto_id' => $trabalhoIniciado->id,
                    'ativo' => $trabalhoIniciado->ativo
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

    public function terminarPonto(Request $request)
    {
        try{

            $hoje = Carbon::now(new DateTimeZone('America/Recife'))->format('Y-m-d H:i');
            $agora = Carbon::now(new DateTimeZone('America/Recife'))->format('h:i:s');

            $user = Auth::user();

            $pontoId = $request->ponto_id;
        
            $pontoEncerrado = Ponto::encerrarPonto($pontoId, $agora);

            $tempoTrabalhado = Ponto::calcularDiferenca($pontoEncerrado->inicio, $pontoEncerrado->fim);


            if($pontoEncerrado){

                return response()->json([

                    'success' => 'Ponto encerrado, Bom descanso!',
                    'ponto_id' => $pontoEncerrado->id,
                    'ativo' => $pontoEncerrado->ativo,
                    'total_trabalhado' => $pontoEncerrado->total_trabalhado

                    ] , 200);

            }

        }catch(Exception $e)
        {

            return response()->json(['erro' => $e->getMessage()] , 404);
        }
    }
}
