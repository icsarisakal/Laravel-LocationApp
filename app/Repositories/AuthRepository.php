<?php

namespace App\Repositories;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Interfaces\AuthRepostoryInterface;
use App\Interfaces\LocationRepositoryInterface;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthRepository implements AuthRepostoryInterface
{

    public function login(LoginRequest $data)
    {
        if(Auth::attempt(['email' => $data->email, 'password' => $data->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('LocationApp')->plainTextToken;
            $success['name'] =  $user->name;
            $success['id'] =  $user->id;

            return $success;
        }
        else{
            return null;
        }
    }

    public function register(RegisterRequest $data)
    {
        $input['password'] = bcrypt($data['password']);
        $input['name'] = $data['name'];
        $input['email'] = $data['email'];
        $user = User::create($input);
        $success['token'] =  $user->createToken('LocationApp')->plainTextToken;
        $success['name'] =  $user->name;
        return $success;
    }
}
