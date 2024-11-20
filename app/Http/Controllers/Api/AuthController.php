<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = $request->authenticate();
        return ApiResponse::success('logged in', [
            'token'=>$user->createToken('api')->plainTextToken,
            'user' => new UserResource($user),
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create($request->all());
        return ApiResponse::success('logged in', [
            'token'=> $user->createToken('api')->plainTextToken,
            'user' => new UserResource($user),
        ]);
    }

    public function logout()
    {
        Auth::user(request()->header('guard'))->tokens()->delete();
        return ApiResponse::success('logged out');
    }
}
