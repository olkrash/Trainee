<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\services\UserService;

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
        $user = $this->userService->createUser($request->toArray());
        $token = $user->createToken('token')->accessToken;
        $response = ['token' => $token];

        return response($response, 201);
    }
}
