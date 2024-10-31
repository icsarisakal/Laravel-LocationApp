<?php
use Illuminate\Support\Facades\Route;

it('ensures rate limiting works on login route', function () {

    Route::post('auth/login', function () {
        return response()->json(['message' => 'Logged in']);
    })->middleware('throttle:global');

    for ($i = 0; $i < 150; $i++) {
        $response = $this->postJson('auth/login', ['email' => 'ibrahimcansarisakal@gmail.com', 'password' => 'password']);
        $response->assertStatus(200); // Başarılı yanıt
    }

    $response = $this->postJson('auth/login', ['email' => 'ibrahimcansarisakal@gmail.com', 'password' => 'password']);
    $response->assertStatus(429); // Too Many Requests
    $response->assertJson(['message' => 'Too Many Attempts.']);
});
