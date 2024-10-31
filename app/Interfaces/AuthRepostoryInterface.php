<?php

namespace App\Interfaces;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

interface AuthRepostoryInterface
{
    public function login(LoginRequest $data);
    public function register(RegisterRequest $data);
}
