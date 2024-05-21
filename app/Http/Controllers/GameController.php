<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use App\Http\Resources\GameResource;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    //Middleware role/permission filter from controller, not from api routes
    public function __construct()
    {
        $this->middleware('can:game_actions', [
            'only' => ['playGame', 'deletePlayerGames', 'showPlayerGames']
        ]);
    }

    public function welcome()
    {
        return response()->json(['message' => 'Welcome to the Get 7 Dice Game']);
    }
    public function playGame($id)
    {
        //authenticated selfplayer condition
        $user = Auth::user();

        if ($user->id != $id) {
            return response(['error' => 'Unauthorized'], 40);
        }

        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);

        //private function winLogic (logic of the game)
        $winGame = $this->winLogic($dice1, $dice2);

        $game = new Game;
        $game->user_id = $id;
        $game->dice1 = $dice1;
        $game->dice2 = $dice2;
        $game->win_game = $winGame;
        $game->save();   //save actual game

        //update number of played and won games for actual user
        $game->user->played_games++;
        $game->user->save();

        if ($winGame) {
            $game->user->won_games++;
            $game->user->save();
        }

        //update successRate at users table
        $playedGames = $game->user->played_games;
        $wonGames = $game->user->won_games;
        $successRate = ($wonGames / $playedGames) * 100;
        $game->user->success_rate =  $successRate;
        $game->user->save();

        if ($game) {
            return response([
                'game' => new GameResource($game),
                'message' => 'Request successful'
            ], 200);
        } else {
            return response(['error' => 'Request failed'], 400);
        }
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

    public function deletePlayerGames($id)
    {
        //authenticated selfplayer condition
        $user = Auth::user();

        if ($user->id != $id) {
            return response(['error' => 'Unauthorized'], 403);
        }

        $deleted = Game::where('user_id', $id)->delete();

        if ($deleted) {
            $user->played_games = 0;
            $user->won_games = 0;
            $user->success_rate = 0;
            $user->save();

            return response(['message' => 'All games deleted'], 200);
        } else {
            return response(['error' => 'Failed to delete games'], 400);
        }
    }

    public function showPlayerGames($id)
    {
        //authenticated selfplayer condition
        $user = Auth::user();

        if ($user->id != $id) {
            return response(['error' => 'Unauthorized'], 403);
        }

        $playerGames = User::find($id)->games;
        $successRate = User::find($id)->success_rate;

        if ($playerGames) {
            if ($playerGames->isEmpty()) {
                return response(['message' => 'You have no games'], 200);
            } else {
                return response([
                    'your_success_rate'  =>  $successRate,
                    'your_games' => GameResource::collection($playerGames),
                    'message' => 'Request successful'
                ], 200);
            }
        } else {
            return response(['error' => 'Request failed', 400]);
        }
        // return response (['player id'=> $idPlayer]);    
    }
}
