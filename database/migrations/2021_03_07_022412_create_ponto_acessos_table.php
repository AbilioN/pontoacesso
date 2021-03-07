<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePontoAcessosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pontos', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->dateTime('inicio');
            $table->dateTime('fim')->nullable();
            $table->integer('nro_pausas')->nullable();
            $table->boolean('ativo');
            $table->dateTime('total_trabalhado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ponto_acessos');
    }
}
