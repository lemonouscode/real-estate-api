<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',[
            'except' => [
                'login',
                'register'
            ]
            ]);
    }

    public function register(Request $request)
    {


        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::create($request->all());

        $token = Auth::login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered succefully',
            'user' => $user,
            'token' => $token
        ]);

    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('email','password');

        $token = Auth::attempt($credentials);

        if(!$token)
        {
            return response()->json([
                'status'=> 'error',
                'message' => 'Invalid credentials'
            ]);
        }

        $user = Auth::user();

        return response()->json([
            'status'=> 'success',
            'message' => 'loged in succefully',
            'token' => $token,
            'name' => $user->name,
            'is_admin' => $user->is_admin
        ]);

    }
}
