<?php

namespace App\services;

use App\Models\UpdateUser;
use Carbon\Carbon;
use App\Models\ResetPassword;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

    /**
     * @param string $email
     * @return bool
     */
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

    /**
     * @param string $token
     * @param string $password
     * @return bool
     */
    public function changePassword(string $token, string $password): bool
    {
        $resetPassword = ResetPassword::where('token', $token)->first();
        //if $user not found
        if ($resetPassword === null) {
            return false;
        }

        $user = User::where('id', $resetPassword->user_id)->first();
        //if $user not found
        if ($user === null) {
            return false;
        }

        $diff = $resetPassword->created_at->diffInHours(Carbon::now());
        if ($diff > 2) {
            return false;
        }

        $user->password = Hash::make($password);
        $user->save();
        $resetPassword->delete();

        return true;
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $user = User::find($id);
        //if $user not found
        if ($user === null) {
            return false;
        }

        if (Auth::user()->cannot('update', $user)) {
            return false;
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return true;
    }

    public function list(): array
    {
        $collection = User::all();

        $plucked = $collection->pluck('email');

        return $plucked->all();
    }

    public function view(int $id): ?User
    {
        $user = User::find($id);
        //if $user not found
        if ($user === null) {
            return null;
        }

        if (Auth::user()->cannot('view', $user)) {
            return null;
        }

        return $user;
    }
}
