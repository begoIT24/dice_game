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

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */


//Rutas de creación y acceso de USER con autenticación
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
// Rutas ADMIN
Route::group([
  'prefix' => 'dice_game',  'middleware' => 'auth:api'
], function () { 
  Route::get('/players', [UserController::class, 'getPlayers']);
  Route::get('/players/{id}', [UserController::class, 'getPlayerGames']);
  Route::get('/players/ranking', [UserController::class, 'getRanking']);
  Route::get('/players/ranking/loser', [UserController::class, 'getLoser']);
  Route::get('/players/ranking/winner', [UserController::class, 'getWinner']);
});

Route::put('/players/{id}', [UserController::class, 'updateName']);

// Route::group([
//     'middleware' => ['auth:api', 'role:admin']
//   ], function() {
//     Route::get('/players', [UserController::class, 'getPlayers']);
//     Route::get('/players', [UserController::class, 'getPlayerGames']);
//     Route::get('/players/ranking', [UserController::class, 'getRanking']);
//     Route::get('/players/ranking/loser', [UserController::class, 'getLoser']);
//     Route::get('/players/ranking/winner', [UserController::class, 'getWinner']);
//   });

// Rutas PLAYER
Route::group([
    'middleware' => 'auth:api'
  ], function() {
    Route::post('/players/{id}/games', [GameController::class, 'playGame']);
    Route::delete('/players/{id}/games', [GameController::class, 'deletePlayerGames']);
    Route::get('/players/{id}/games', [GameController::class, 'showPlayerGames']);
});


    




