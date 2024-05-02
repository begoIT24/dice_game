<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;


class UserController extends Controller
{
    //Middleware filter from controller, not from api routes
    public function __construct(){
         $this->middleware('can:players information', [
             'only' => ['getPlayers', 'getPlayerGames', 'getRanking', 'getLoser', 'getWinner']
         ]);
         $this->middleware('can:update name', [
             'only' => ['updateName']
         ]);
     }

    public function getPlayers(){
        $players = User::role('player') -> orderBy('successRate', 'asc') -> paginate(10);   // ->get(); sin paginación
        return UserResource::collection($players);      
    }
    
    public function getPlayerGames($id){
        $user = User::findOrFail($id);
        return response(['user' => new UserResource($user), 'message' => 'Success'], 200);
    }
    
    public function getRanking(){

    }

    public function getLoser(){

    }

    public function getWinner(){

    }

    public function updateName(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'max:255|unique:users',
        ]);
        $user = User::findOrFail($id);
        $user->name = $data['name'];
        $user->save();

        return response(['user' => new UserResource($user), 'message' => 'Success'], 200);
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
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
