<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\ResetPassword;
use App\services\UserService;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @var userService created dependency injection
     */
    protected $userService;

    /**
     * UserController constructor
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }

    /**
     * @param StoreUserRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->create($request->toArray());
        $token = $user->createToken('token')->accessToken;
        $response = ['token' => $token];

        return response($response, 201);
    }

    /**
     * @param LoginUserRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|string[]
     */
    public function login(LoginUserRequest $request)
    {
        $user = $this->userService->login($request->toArray());
        if ($user === null) {
            return ['errors'=> 'user not found'];
        }
        $token = $user->createToken('token')->accessToken;
        $response = ['token' => $token];

        return $response;
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
       $result = $this->userService->resetPassword($request->get('email'));
    }
}
