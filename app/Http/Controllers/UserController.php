<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\models\User;
use App\Http\Resources\UserResource;
use App\services\UserService;
use Illuminate\Http\Request;

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
            return ['errors' => 'user not found'];
        }
        $token = $user->createToken('token')->accessToken;
        $response = ['token' => $token];

        return $response;
    }

    /**
     * @param ResetPasswordRequest $request
     * @return array
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $result = $this->userService->resetPassword($request->get('email'));

        return ['success' => $result];
    }

    /**
     * @param ChangePasswordRequest $request
     * @return array
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $result = $this->userService->changePassword($request->get('token'), $request->get('password'));

        return ['success' => $result];
    }

    /**
     * @param UpdateUserRequest $request
     * @param int $id
     * @return array
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $result = $this->userService->update($id, $request->toArray());

        return ['success' => $result];
    }

    public function index()
    {
        $result = $this->userService->index();

        return ['users' => $result];
    }

    public function show(Request $request, User $user)
    {
        if ($request->user()->cannot('view', $user)) {
            return response()->json(null, 403);
        }

        return new UserResource($user);
    }

    public function delete(Request $request, User $user)
    {
        if ($request->user()->cannot('update', $user)) {
            return response()->json(null, 403);
        }

        $result = $this->userService->delete($user);

        return ['success' => $result];
    }
}
