<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;


class UserController extends Controller
{
    //Middleware role/permission filter from controller, not from api routes
    public function __construct()
    {
         $this->middleware('can:players information', [
             'only' => ['getAllPlayers', 'getRanking', 'getLoser', 'getWinner']
         ]);
         $this->middleware('can:update name', [
             'only' => ['updateName']
         ]);
     }

    public function getAllPlayers()
    {
        $players = User::role('player') -> orderBy('successRate', 'desc') -> paginate(10);   // ->get(); sin paginación
        return response([UserResource::collection($players), 'message' => 'Request Successful'], 200);      
    }    
   
    public function getRanking()
    {
        $players = User::role('player')->get();
        $totalGamesPlayed = 0;
        $totalGamesWon = 0;       

        foreach ($players as $player) {
            $totalGamesPlayed += $player->playedGames;
            $totalGamesWon += $player->WonGames;
        } 
           
        if ($totalGamesPlayed > 0) {
            $averageRanking = ($totalGamesWon / $totalGamesPlayed) * 100;
        } else {
            $averageRanking = 0;
        }    
        return $averageRanking;
    }
          

    public function getLoser()
    {

    }

    public function getWinner(){

    }

    public function updateName(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'max:255|unique:users',
        ]);
        if ($data['name'] == null) {
            $data['name'] = "anonymous";
        }
        $user = User::findOrFail($id);
        $user->name = $data['name'];
        $user->save();

        return response(['user' => new UserResource($user), 'message' => 'Request Successful'], 200);
    }  
   
}
