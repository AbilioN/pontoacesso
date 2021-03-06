<?php

namespace App;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;

class Ponto extends Model
{
    //

    public function pausas()
    {
        return $this->hasMany(Pausa::class);
    }

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

    public static function calcularDiferenca($inicio , $fim , $verificarPausas = null)
    {

        $inicio = Carbon::parse($inicio);
        $fim = Carbon::parse($fim);

        if($verificarPausas)
        {

            $hoje = Carbon::now(new DateTimeZone('America/Recife'))->format('Y-m-d');

            $ultimaPausa = Pausa::where([
                'ponto_id' => $verificarPausas->ponto_id,
                'ativo' => false,
                ])->where(
                    'inicio','>=',$hoje
                )->get()->last();
            if($ultimaPausa){
                $inicio = $ultimaPausa->fim;

            }
        }
        $parcialTrabalhado = $fim->diff($inicio);
        $parcialTrabalhado = $parcialTrabalhado->format('%h:%i:%s');

        return $parcialTrabalhado;

    }
    
    public static function adicionarHoras($atual, $mais)

    {
        $atual = Carbon::parse($atual);
        $mais = Carbon::parse($mais);
        $atual = $atual->addHours($mais);
        $atual = $atual->format('H:i:s');

        return $atual;
    }

    public static function atualizarPonto($pausaEncerrada)
    {
        $ponto = self::find($pausaEncerrada->ponto_id);
        $nroPausas = $ponto->nro_pausas;
        $ponto->nro_pausas = $nroPausas + 1;

        if($ponto->save()){
            return $ponto;
        }
        return null;
    }

    public static function encerrarPonto($pontoId, $agora)
    {
        $ponto = self::find($pontoId);
        $ponto->ativo = false;
        $ponto->fim = $agora;
        $tempoTrabalhado = self::calcularDiferenca($ponto->inicio, $ponto->fim);
        $ponto->total_trabalhado = $tempoTrabalhado;

        if($ponto->save()){
            return $ponto;
        }

        return null;

    }
}
