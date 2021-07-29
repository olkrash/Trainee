<?php

namespace App\services;

use App\Models\ResetPassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @param array $data array from StoreUserRequest
     * @return User
     */
    public function create(array $data): User
    {
        $user = new User();
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->remember_token = Str::random(10);
        $user->save();

        return $user;
    }

    /**
     * @param array $data
     * @return User|null
     */
    public function login(array $data): ?User
    {
        $user = User::where('email', $data['email'])->first();
        //if $user not found
        if ($user === null) {
            return null;
        }

        //if $user found and password is correct
        if (Hash::check($data['password'], $user->password)) {
            return $user;
        }

        //if $user found and password is incorrect
        return null;
    }

    public function resetPassword(string $email): bool
    {
        $user = User::where('email', $email)->first();
        //if $user not found
        if ($user === null) {
            return false;
        }

        $resetPassword = new ResetPassword();
        $resetPassword->user_id = $user->id;
        $resetPassword->token = Str::random(10);
        $resetPassword->save();

        Mail::to($email)->send(new \App\Mail\ResetPassword($resetPassword->token));

        return true;
    }
}
