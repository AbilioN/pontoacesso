<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login' , 'AuthController@login')->name('login');

Route::group(['middleware' => ['apiJwt'] ], function(){
    Route::get('users' , 'UserController@listUsers')->name('users.list');
    Route::post('ponto/iniciar' , 'PontoController@iniciarPonto')->name('pontos.iniciar');
    Route::post('ponto/terminar' , 'PontoController@terminarPonto')->name('pontos.terminar');
    Route::post('pausa/iniciar' , 'PausaController@iniciarPausa')->name('pausa.iniciar');
    Route::post('pausa/terminar' , 'PausaController@terminarPausa')->name('pausa.terminar');
});