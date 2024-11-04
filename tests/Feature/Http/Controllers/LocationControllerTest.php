<?php

use App\Http\Requests\StoreLocationRequest;
use App\Interfaces\LocationRepositoryInterface;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

it('successfully creates a location', function () {

    $requestData = [
        'name' => 'Test Location',
        'lat' => 32.123,
        'long' => 32.123,
        'marker_color' => '#213321',
    ];

    $user = User::factory()->create();
    Auth::shouldReceive('id')->andReturn($user->id);

    $request = Mockery::mock(StoreLocationRequest::class);
    $request->name = $requestData['name'];
    $request->lat = $requestData['lat'];
    $request->long = $requestData['long'];
    $request->marker_color = $requestData['marker_color'];

    $mockLocationRepository = Mockery::mock(LocationRepositoryInterface::class);
    $mockLocationRepository->shouldReceive('store')->andReturnUsing(function ($details) {
        $location = (object)$details;
        $location->id = 1;
        return $location;
    });

    DB::shouldReceive('beginTransaction')->once();
    DB::shouldReceive('commit')->once();
    DB::shouldReceive('rollback')->never();

    $controller = new LocationController($mockLocationRepository);

    $response = $controller->store($request);

    $responseData = json_decode($response->getContent());

    expect($response->status())->toBe(201)
        ->and($responseData->message)->toBe('Location Create Successful')
        ->and($responseData->data->name)->toBe('Test Location')
        ->and($responseData->data->lat)->toBe(32.123)
        ->and($responseData->data->long)->toBe(32.123)
        ->and($responseData->data->marker_color)->toBe('#213321');
});

it('successfully updates a location', function () {

    $requestData = [
        'name' => 'Updated Location',
        'lat' => 40.123,
        'long' => 40.123,
        'marker_color' => 'green',
    ];

    $user = User::factory()->create();
    Auth::shouldReceive('id')->andReturn($user->id);

    $request = Mockery::mock(\App\Http\Requests\UpdateLocationRequest::class);
    $request->name = $requestData['name'];
    $request->lat = $requestData['lat'];
    $request->long = $requestData['long'];
    $request->marker_color = $requestData['marker_color'];

    $mockLocationRepository = Mockery::mock(LocationRepositoryInterface::class);
    $mockLocationRepository->shouldReceive('update')->with($requestData, 1)->once();

    DB::shouldReceive('beginTransaction')->once();
    DB::shouldReceive('commit')->once();
    DB::shouldReceive('rollback')->never();

    $controller = new LocationController($mockLocationRepository);

    $response = $controller->update($request, 1);

    $responseData = json_decode($response->getContent());
    expect($response->status())->toBe(201);
});

it('rolls back transaction on update failure', function () {

    $requestData = [
        'name' => 'Updated Location',
        'lat' => 40.123,
        'long' => 40.123,
        'marker_color' => 'green',
    ];

    $request = Mockery::mock(\App\Http\Requests\UpdateLocationRequest::class);
    $request->name = $requestData['name'];
    $request->lat = $requestData['lat'];
    $request->long = $requestData['long'];
    $request->marker_color = $requestData['marker_color'];

    $mockLocationRepository = Mockery::mock(LocationRepositoryInterface::class);
    $mockLocationRepository->shouldReceive('update')->with($requestData, 1)->andThrow(new \Exception('Update failed'));

    DB::shouldReceive('beginTransaction')->once();
    DB::shouldReceive('rollback')->once();

    $controller = new LocationController($mockLocationRepository);

    $response = $controller->update($request, 1);

    expect($response)->toBe(null);
});

it('calculates route distance with exists locations', function () {

    $requestData = [
        'lat' => 82.1651,
        'long' => 35.8741,
    ];

    $request = new \App\Http\Requests\MakeRouteRequest();
    $request->merge($requestData);

    $mockLocationRepository = Mockery::mock(LocationRepositoryInterface::class);

    $mockLocationRepository
        ->shouldReceive('makeRoute')
        ->once()
        ->with(Mockery::on(function($data) {
            return is_array($data);
        }))
        ->andReturn([
            'success' => true,
        ]);

    $controller = new LocationController($mockLocationRepository);

    $response = $controller->makeRoute($request);

    $responseData = json_decode($response->getContent());

    expect($response->status())->toBe(200)
        ->and($responseData->success)->toBe(true);
});

it('returns validation error when long parameter is missing', function () {

    // Eksik "long" alanı içeren veri
    $requestData = [
        'lat' => 82.1651,
    ];

    // Mock oluşturmak yerine doğrudan HTTP isteği gönderin
    $request = new \App\Http\Requests\MakeRouteRequest();
    $request->merge($requestData);

    $mockLocationRepository = Mockery::mock(LocationRepositoryInterface::class);

    $mockLocationRepository
        ->shouldReceive('makeRoute')
        ->once()
        ->with(Mockery::on(function($data) {
            return is_array($data);
        }))
        ->andReturn([
            'success' => false,
        ]);

    $controller = new LocationController($mockLocationRepository);

    $response = $controller->makeRoute($request);

    $responseData = json_decode($response->getContent());
//    dd($responseData);

    expect($response->status())->toBe(200)
        ->and($responseData->success)->toBe(true)->not();
});



