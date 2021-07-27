<?php

namespace App\services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @param array $data array from StoreUserRequest
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = new User();
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->remember_token = Str::random(10);
        $user->save();

        return $user;
    }
}
