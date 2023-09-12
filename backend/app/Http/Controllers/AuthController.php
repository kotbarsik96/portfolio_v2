<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|unique:users,name',
            'email' => 'required|string:unique:users,name',
            'telegram' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => e($fields['name']),
            'email' => e(filter_var($fields['email'], FILTER_SANITIZE_EMAIL)),
            'telegram' => e($fields['telegram']),
            'password' => bcrypt($fields['password'])
        ]);

        return response(['user' => $user]);
    }

    public function checkAuth(Request $request)
    {
        $user = User::where('name', e($request['name']))->first();
        if (empty($user))
            return response(['is_me' => false]);
        
        $token = $user->tokens()->where('name', $user->name)->first();

        if ($user && $token)
            return response(['is_me' => true]);

        return response(['is_me' => false]);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('name', e($fields['name']))->first();
        if (empty($user) || !Hash::check($fields['password'], $user->password))
            return response(['success' => false]);

        $token = $user->createToken($user->name)->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'success' => true
        ];
    }

    public function logout(Request $request)
    {
        $user = User::where('name', e($request['name']))->first();
        if (empty($user))
            return response(['success' => false, 'no_user' => true]);

        return response([$user->tokens()->delete()]);
    }
}