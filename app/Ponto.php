<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ponto extends Model
{
    //

    public static function verificarOuIniciarPonto($ponto)
    {
        $trabalhoIniciado = self::where([
            'user_id' => $ponto->user_id ,
            'ativo' => true])
             ->where(
                'inicio' , '>=' , Carbon::today())
            ->get()->first();

        if(!$trabalhoIniciado){
            $trabalhoIniciado = new self();
        }else{
            $trabalhoIniciado = self::find($trabalhoIniciado->id);
        }

        $trabalhoIniciado->user_id = $ponto->user_id;
        $trabalhoIniciado->inicio = $ponto->inicio;
        $trabalhoIniciado->ativo = true;
        $trabalhoIniciado->nro_pausas = 0;
        if($trabalhoIniciado->save())
        {
            return $trabalhoIniciado;
        }
        return null;
    }
}
