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

    public static function calcularTotalParcial($pontoId , $agora)
    {
        $ponto = self::find($pontoId);
        
        $agora = Carbon::parse($agora);
        $horaInicial = Carbon::parse($ponto->inicio);
        $parcialTrabalhado = $agora->diff($horaInicial);
        $parcialTrabalhado = $parcialTrabalhado->format('%h:%i:%s');

        $ponto->total_trabalhado .= $parcialTrabalhado;

        if($ponto->save()){
            return $parcialTrabalhado;
        }

        return null;
        // $parcialTrabalhado = Carbon::instance($parcialTrabalhado);
        // $parcialTrabalhado = $parcialTrabalhado->toDateTimeString();

        // TODO, validacao de demais pausas;
        // $totalParcial = Carbon()
        // dd($totalParcial);
        // $pausasEncerradas = Pausa::where([
        //     'ponto_id' => $pontoId , 
        //     'ativo' => false
        //     ])->get();
        // foreach($pausasEncerradas as $pausaEncerrada)
        // {

        // }
    }

    public static function atualizarPonto($pausaEncerrada)
    {
        $ponto = self::find($pausaEncerrada->ponto_id);
        $nroPausas = $ponto->nro_pausas;
        $ponto->nro_pausas = $nroPausas++;
        if($ponto->save()){
            return $ponto;
        }
        return null;
    }

    public static function calcularTempoRestante($agora, $ponto)
    {
        $pausaIniciada = Pausa::where([
            'ponto_id' => $ponto->id,
            'ativo' => false,
            ])->where(
                'inicio','>=',Carbon::today()
            )->get();

        
        foreach($pausaIniciada as $pausa){
            $inicio = $pausa->inicio;
            $fim = $pausa->fim;

            $fim = Carbon::parse($fim);
            $horaInicial = Carbon::parse($inicio);
            
            $parcialTrabalhado = $agora->diff($horaInicial);
            $parcialTrabalhado = $parcialTrabalhado->format('%h:%i:%s');

        }
        $agora = Carbon::parse($agora);
        $horaInicial = Carbon::parse($ponto->inicio);
        
        $parcialTrabalhado = $agora->diff($horaInicial);
        $parcialTrabalhado = $parcialTrabalhado->format('%h:%i:%s');
    }
}
