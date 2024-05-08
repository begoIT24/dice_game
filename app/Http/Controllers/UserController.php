<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

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
        //authenticated and role admin comprobation
        $user = Auth::user();
        $admin = Role::where('name', 'admin')->where('guard_name', 'api')->first();

        if (!$user || !$user->hasRole($admin)) {
             return response(['error' => 'Unauthorized'], 403);
        }

        $players = User::role('player') -> orderBy('successRate', 'desc') -> paginate(10);   // ->get(); sin paginación
        
        if($players){   
            return response([UserResource::collection($players),
                       'message' => 'Request succesful'], 200);
        } else {
            return response(['error' => 'Request failed', 400]);
        }
        //return response (['message' => $user]);
    }    
    
    public function getRanking()
    {
        //authenticated and role admin comprobation
        $user = Auth::user();
        $admin = Role::where('name', 'admin');
                  
        if (!$user || !$user->hasRole($admin)) {
            return response(['error' => 'Unauthorized'], 403);
        }

        $players = User::role('player')->get();
        $successRateSum = 0;
        $playersWithGames = 0;       

        foreach ($players as $player) {
            if($player->playedGames > 0)
                $successRateSum += $player->successRate;
                $playersWithGames++;         
        }             

        if ($playersWithGames > 0) {
           $averageRanking = $successRateSum / $playersWithGames;
        } else {
            $averageRanking = 0;
        } 
        
        if($averageRanking){   
            return response(['averageRanking' => $averageRanking,
                             'message' => 'Request succesful'], 200);
        } else {
            return response(['error' => 'Request failed', 400]);
        }       
    }          

    public function getLoser()
    {
        //authenticated and role admin comprobation
        $user = Auth::user();
                  
        if (!$user || !$user->hasRole('admin')) {
            return response(['error' => 'Unauthorized'], 403);
        }
        
        $players = User::role('player')->orderBy('successRate', 'asc')->take(1)->get();

        return response([
            'Loser player' => UserResource::collection($players),
            'message' => 'Request Successful'], 200);
    }

    public function getWinner()
    {
        //authenticated and role admin comprobation      
        if (!auth()->user()->hasRole('admin')) {
             return response(['error' => 'Unauthorized'], 403);
        }
        
        $players = User::role('player')->orderBy('successRate', 'desc')->take(1)->get();     

        return response([
            'Winner player' => UserResource::collection($players),
            'message' => 'Request Successful'], 200);
    }

    public function updateName(Request $request, $id)
    {   
         //authenticated selfplayer comprobation
         $user = Auth::user(); 
                  
         if ($user->id != $id) {
             return response(['error' => 'Unauthorized'], 403);
          }
        try {        
            $data = $request->validate([
                'name' => 'max:255|unique:users,name', 
            ]);
        
            if ($data['name'] === null) {
                $data['name'] = "anonymous";
            }

            $user = User::findOrFail($id);
           
            $user->name = $data['name'];

            $user->save();

        } catch (\Illuminate\Validation\ValidationException){            
            return response(['error' => 'The name already exists'], 422);
        
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {      
            return response(['error' => 'Player not found'], 404);
        }

        return response(['user' => new UserResource($user), 'message' => 'Request Successful'], 200);
    } 
    
    // Da el mismo error que hasRole:

    // if (!auth()->user()->hasPermissionTo('players information')) {
    //    return response(['error' => 'Unauthorized'], 403);   }

}
