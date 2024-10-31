<?php

use App\Http\Controllers\LocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::prefix("auth")->controller(AuthController::class)->group(function (){
    Route::post('register','register')->middleware(['throttle:global']);
    Route::post('login','login')->middleware(['throttle:global']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum','throttle:global']);

Route::middleware(['auth:sanctum','throttle:global'])->group( function () {
    Route::apiResource('locations',LocationController::class);
    Route::post("locations/make-route",[LocationController::class,"makeRoute"]);
});

