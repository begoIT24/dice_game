<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\GameResource;

class GameController extends Controller
{
   //Middleware role/permission filter from controller, not from api routes
   public function __construct()
   {
        $this->middleware('can:game actions', [
            'only' => ['playGame', 'deletePlayerGames', 'showPlayerGames']
        ]);       
    }

   public function playGame(Request $request)
   {           
        $idPlayer = $request->user()->id;      

        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);
            
        //private function winLogic (logic of the game)
        $winGame = $this->winLogic($dice1, $dice2);

        $game = new Game;
        $game->user_id = $idPlayer;
        $game->dice1 = $dice1;
        $game->dice2 = $dice2;
        $game->winGame = $winGame;
        $game->save();   //save actual game

        //update number of played and won games for actual user
        $game->user->playedGames++;
        $game->user->save();

        if ($winGame) {       
            $game->user->wonGames++;
            $game->user->save();
        }

        //update successRate at users table
        $playedGames = $game->user->playedGames;
        $wonGames = $game->user->wonGames;
        $successRate = ($wonGames / $playedGames) * 100;
        $game->user->successRate =  $successRate;
        $game->user->save();
       
        return response(['game' => new GameResource($game),
                        'message' => 'Request successful'], 200);          
    }

    private function winLogic($dice1, $dice2): bool
    {
        if (($dice1 + $dice2) == 7) {
            $winGame = true;
        } else {
            $winGame = false;
        }
        return $winGame;
    }    
   
   public function deletePlayerGames(Request $request)
   {
        $idPlayer = ($request->user()->id) - 1;

        $deleted = Game::where('user_id', $idPlayer)->delete();

        if($deleted){   
            return response(['message' => 'Request succesful'], 200);
        } else{
            return response(['message' => 'Request failed', 400]);
        }
   }

   public function showPlayerGames(Request $request)
   {
        try {     
            $idPlayer = $request->user()->id - 1;

            $playerGames = User::findOrFail($idPlayer)->games;
        
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {      
            return response(['error' => 'Player not found'], 404);
        }
        
       if (sizeof($playerGames) == 0) {
             return response(['message' => 'You have no games'], 200);
        } else {
            return response(['Your games' => GameResource::collection($playerGames),
                             'message' => 'Request succesful'], 200);
        }
        // return response (['player id'=> $idPlayer]);
    }
}

