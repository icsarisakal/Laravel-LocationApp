<?php

use App\Http\Requests\StoreLocationRequest;
use App\Http\Resources\LocationResource;
use App\Interfaces\LocationRepositoryInterface;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mockery;
use App\Models\User;
use App\Classes\ApiResponseClass;

it('successfully creates a location', function () {
    // Örnek veri
    $requestData = [
        'name' => 'Test Location',
        'lat' => 32.123,
        'long' => 32.123,
        'marker_color' => 'blue',
    ];

    // Fabrika kullanarak bir kullanıcı oluşturun
    $user = User::factory()->create();
    Auth::shouldReceive('id')->andReturn($user->id);

    // Mock StoreLocationRequest
    $request = Mockery::mock(StoreLocationRequest::class);
    $request->name = $requestData['name'];
    $request->lat = $requestData['lat'];
    $request->long = $requestData['long'];
    $request->marker_color = $requestData['marker_color'];

    // Mock locationRepositoryInterface
    $mockLocationRepository = Mockery::mock(LocationRepositoryInterface::class);
    $mockLocationRepository->shouldReceive('store')->andReturnUsing(function ($details) {
        $details['id'] = 1; // Örnek bir id döndürür
        return (object)$details;
    });

    // Controller'ı başlat
    $controller = new LocationController($mockLocationRepository);

    // Testi çalıştır
    DB::shouldReceive('beginTransaction');
    DB::shouldReceive('commit');
    $response = $controller->store($request);

    // API cevabını doğrula
    expect($response->status())->toBe(201)
        ->and($response->getData()->message)->toBe('Location Create Successful')
        ->and($response->getData()->data)->toBeInstanceOf(LocationResource::class);
});
