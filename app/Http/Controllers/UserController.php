<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function store(StoreUserRequest $request)
    {
        $user = new User();
        $user->email = $request['email'];
        $user->password = Hash::make($request['password']);
        $user->remember_token = Str::random(10);
        $user->save();

        $token = $user->createToken('token')->accessToken;
        $response = ['token' => $token];
        return response($response, 201);
    }
}
