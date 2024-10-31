<?php

namespace App\Http\Controllers\API;

use App\Classes\ApiResponseClass;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Interfaces\AuthRepostoryInterface;

class AuthController
{
    private AuthRepostoryInterface $authRepositoryInterface;

    public function __construct(AuthRepostoryInterface $authRepositoryInterface)
    {
        $this->authRepositoryInterface = $authRepositoryInterface;
    }

    public function login(LoginRequest $request)
    {
        $data = $this->authRepositoryInterface->login($request);
        return ApiResponseClass::sendResponse($data,'Login Successful.',200);
    }
    public function register(RegisterRequest $request)
    {
        $data = $this->authRepositoryInterface->register($request);
        return ApiResponseClass::sendResponse(AuthResource::collection($data),'Registration Successful.',201);
    }

}
