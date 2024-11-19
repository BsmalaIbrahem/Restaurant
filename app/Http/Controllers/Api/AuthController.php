<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $guard = $request->header('guard', 'user');
        if(!Auth::guard($guard)->attempt(['email' => $request['email'], 'password' => $request['password']]))
        {
            return ApiResponse::failed('password is Wrong');
        }

        return ApiResponse::success('logged in', [
            'token'=>$request->user($guard)->createToken('api')->plainTextToken,
            'user' => auth()->guard($guard)->user(),
        ]);
    }

    public function register()
    {
        //
    }

    public function logout()
    {
        Auth::user(request()->header('guard'))->tokens()->delete();
        return ApiResponse::success('logged out');
    }
}
