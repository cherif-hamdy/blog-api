<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('login' , 'register');
    }

    public function login(Request $request)
    {
        $data = $this->validate($request , [
            'email' => 'required|email',
            'password' => 'required|max:8'
        ]);

        if(! $token = auth()->attempt($data)){
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ],401);
        }

        return response()->json([
            'token' => $token,
            'type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function register(Request $request)
    {
        $data = $this->validate($request , [
            'name' => 'required|string|between:2,200',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8'
        ]);

        $user = User::create(array_merge($data, ['password' => bcrypt($data['password'])]));

        return response()->json([
            'status' => true,
            'message' => 'successful',
            'user' => $user
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
