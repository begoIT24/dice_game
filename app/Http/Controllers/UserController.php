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
        $this->middleware('can:players_information', [
            'only' => ['getAllPlayers', 'getRanking', 'getLoser', 'getWinner']
        ]);
        $this->middleware('can:update_name', [
            'only' => ['updateName']
        ]);
    }

    public function getAllPlayers()
    {
        //authenticated and role admin comprobation        
        if (!auth()->user()->hasRole('admin')) {
            return response(['error' => 'Unauthorized'], 403);
        }

        $players = User::role('player')->orderBy('success_rate', 'desc')->paginate(10);   // ->get(); sin paginación

        if ($players) {
            return response([
                UserResource::collection($players),
                'message' => 'Request Successful'
            ], 200);
        } else {
            return response(['error' => 'Request failed', 400]);
        }
        /* Another call option for authenticated user:
            $user = Auth::user();
            if (!$user->hasRole('admin')).....*/
    }

    public function getRanking()
    {
        //authenticated and role admin comprobation              
        if (!auth()->user()->hasRole('admin')) {
            return response(['error' => 'Unauthorized'], 403);
        }

        $players = User::role('player')->get();
        $successRateSum = 0;
        $playersWithGames = 0;

        foreach ($players as $player) {
            if ($player->played_games > 0)
                $successRateSum += $player->success_rate;
                $playersWithGames++;
        }

        $averageRanking = ($playersWithGames > 0) ? ($successRateSum / $playersWithGames) : 0;

        if ($averageRanking) {
            return response([
                'average_ranking' => $averageRanking,
                'message' => 'Request succesful'
            ], 200);
        } else {
            return response(['error' => 'Request failed', 400]);
        }
    }

    public function getLoser()
    {
        //authenticated and role admin comprobation              
        if (!auth()->user()->hasRole('admin')) {
            return response(['error' => 'Unauthorized'], 403);
        }

        $players = User::role('player')->orderBy('success_rate', 'asc')->take(1)->get();

        if ($players) {
            return response([
                'loser_player' => UserResource::collection($players),
                'message' => 'Request Successful'
            ], 200);
        } else {
            return response(['error' => 'Request failed', 400]);
        }
        /* For testing / debugging
            $user = Auth::user(); 
            $role = $user->hasRole('admin');
            return response(['user' => $user, 'admin' => $role]); */
    }

    public function getWinner()
    {
        //authenticated and role admin comprobation              
        if (!auth()->user()->hasRole('admin')) {
            return response(['error' => 'Unauthorized'], 403);
        }

        $players = User::role('player')->orderBy('success_rate', 'desc')->take(1)->get();

        if ($players) {
            return response([
                'winner_player' => UserResource::collection($players),
                'message' => 'Request Successful'
            ], 200);
        } else {
            return response(['error' => 'Request failed', 400]);
        }
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

            if ($user) {
                return response([
                    'user' => new UserResource($user),
                    'message' => 'Request Successful'
                ], 200);
            } else {
                return response(['error' => 'Request failed', 400]);
            }
        } catch (\Illuminate\Validation\ValidationException) {
            return response(['error' => 'The name already exists'], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response(['error' => 'Player not found'], 404);
        }
    }
}
