<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $attr = $request->validate(
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed',
            ]
        );

        $user = User::create([
            'name' => $attr['name'],
            'email' => $attr['email'],
            'password' => bcrypt($attr['password']),
        ]);

        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken,
        ], 200);
    }

    public function login(Request $request)
    {
        
        $attr = $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]
        );

        if (!Auth::attempt($attr)) {
            return response([
                'message' => 'Invalid Credentials'
            ], 403);
        }

        $user = Auth::user();

        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken,
        ], 200);
    }

    public function logout()
    {
        $user = Auth::user();
    
        $user->tokens()->delete();
        return response([
            'message' => 'logout success'
        ], 200);
    }

    public function user() {
        return response([
            'user' => Auth::user(),
        ], 200);
    }

    public function update(Request $request) {
        $attr = $request->validate([
            'name' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'profiles');

        Auth::user()->update([
            'name' => $attr['name'],
            'image' => $image,
        ]);

        return response([
            'message' => 'update profile success',
            'user' => Auth::user(),
        ], 200);
    }
}
