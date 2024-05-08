<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\CarbonController;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
     public function signUp(Request $request)
    {
        $request->validate([
            'name' => 'max:255|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        //anonymous when there is no name
        if ($request['name'] == null) {
            $request['name'] = "anonymous";
        }
        $playerRole = Role::where('name', 'player')->where('guard_name', 'api')->first();
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ])->assignRole($playerRole);

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }
  
    /**
     * Inicio de sesión y creación de token
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'guard' => 'api',          
        ]);

        if (!auth()->attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer'
           
        ]);
    }
  
    /**
     * Cierre de sesión (anular el token)
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
  
    /**
     * Obtener el objeto User como json
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}