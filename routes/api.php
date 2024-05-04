<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//USER login system routes with authentication (passport)
Route::group([
     'prefix' => 'dice_game'
    ], function () {
    Route::post('login', [AuthController::class, 'login']);  
    Route::post('signup', [AuthController::class, 'signUp']);
  
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

//ADMIN routes
Route::group([
  'prefix' => 'dice_game',  'middleware' => 'auth:api'
], function () { 
  Route::get('/players', [UserController::class, 'getAllPlayers']);
  Route::get('/players/ranking', [UserController::class, 'getRanking']);
  Route::get('/players/ranking/loser', [UserController::class, 'getLoser']);
  Route::get('/players/ranking/winner', [UserController::class, 'getWinner']);
});

//ADMIN & PLAYER routes
Route::group([
  'prefix' => 'dice_game', 'middleware' => 'auth:api'
], function() {
  Route::put('/players/{id}', [UserController::class, 'updateName']);
  Route::delete('/players/{id}/games', [GameController::class, 'deletePlayerGames']);
  Route::get('/players/{id}/games', [GameController::class, 'showPlayerGames']);
});

//PLAYER route
  Route::post('/players/{id}/games', [GameController::class, 'playGame'])->middleware('auth:api');
    

    




