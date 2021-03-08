<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DateTimeZone;
use Exception;

class Pausa extends Model
{
    protected $table = 'pausas';

    public $timestamps = false;
    

    public static function verificarOuIniciarPausa($novaPausa , $hoje , $agora)
    {
        
        $pausaIniciada = self::where([
            'ponto_id' => $novaPausa['ponto_id'],
            'ativo' => true,
            ])->where(
                'inicio','>=',$hoje
            )->get()->first();

          
        if($pausaIniciada)
        {

            $mensagem = 'Erro, voce ja tem uma pausa aberta hoje, encerre a pausa anterior antes de iniciar uma nova';
            throw new Exception($mensagem);
        }else{

            $pausa = new self();
        }

        $pausa->ponto_id = $novaPausa['ponto_id'];
        $pausa->inicio = $agora;
        $pausa->descricao = $novaPausa['descricao'];    
        $pausa->ativo = true;

        if($pausa->save()){
            return $pausa;
        }
        return null;
    }

    public static function terminarPausa($pausaId , $agora)
    {

        $pausa = self::find($pausaId);

        $pausa->fim = $agora;
        $pausa->ativo = false;

        if($pausa->save()){
            return $pausa;
        }
        return null;
    }
}
