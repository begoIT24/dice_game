<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
   
   public function playGame(Request $request)
   {
      $idPlayer = $request->user()->id;

      $dice1 = rand(1, 6);
      $dice2 = rand(1, 6);

      $resultWin = $this->winLogic($dice1, $dice2);

      if ($resultWin) {
          $resultGame = "You has won!";
      } else {
          $resultGame = "You has lost";
      }

      $game = new Game;
      $game->user_id = $idPlayer;
      $game->dice1Value = $dice1;
      $game->dice2Value = $dice2;
      $game->resultWin = $resultWin;
      $game->save();   //save actual game

      //update number of played and won games for actual user
      $game->user->playedGames++;
      $game->user->save();

      if ($resultWin) {       
        $game->user->wonGames++;
        $game->user->save();
      }

      //update successRate at users table
      $playedGames = $game->user->playedGames;
      $wonGames = $game->user->wonGames;
      $successRate = ($wonGames / $playedGames) * 100;
      $game->user->successRate =  $successRate;
      $game->user->save();
     
      return response(['message' => 'Request successful', $resultGame ], 200);          
    }

    private function winLogic($dice1, $dice2): bool
    {
        if (($dice1 + $dice2) == 7) {
            $resultWin = true;
        } else {
            $resultWin = false;
        }
        return $resultWin;
    }


    
   
   public function deletePlayerGames(){

   }

   public function showPlayerGames(){
    
   }
   
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        //
    }
}
