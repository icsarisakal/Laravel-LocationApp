<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Interfaces\LocationRepositoryInterface;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    private LocationRepositoryInterface $locationRepositoryInterface;

    public function __construct(LocationRepositoryInterface $locationRepositoryInterface)
    {
        $this->locationRepositoryInterface = $locationRepositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->locationRepositoryInterface->getAll();

        return ApiResponseClass::sendResponse(LocationResource::collection($data),'All Locations Fetched.',200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request)
    {

        $details =[
            'name' => $request->name,
            'lat' => $request->lat,
            'long' => $request->long,
            'marker_color' => $request->marker_color,
            'created_user_id'=>auth()->id(),
        ];
        DB::beginTransaction();
        try{
            $product = $this->locationRepositoryInterface->store($details);

            DB::commit();
            return ApiResponseClass::sendResponse(new LocationResource($product),'Location Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex,$ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $detail = $this->locationRepositoryInterface->getDetail($id);
        if (!empty($detail)){
            return ApiResponseClass::sendResponse(new LocationResource($detail),'Location Found.',200);
        } else{
            return ApiResponseClass::sendResponse(null,'There is no location.',200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, $id)
    {
        $updateDetails =[
            'name' => $request->name,
            'lat' => $request->lat,
            'long' => $request->long,
            'marker_color' => $request->marker_color
        ];
        DB::beginTransaction();
        try{
            $this->locationRepositoryInterface->update($updateDetails,$id);

            DB::commit();
            return ApiResponseClass::sendResponse('Location Update Successful','',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->locationRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Location Delete Successful','',204);
    }

    public function makeRoute(Request $request)
    {
        if (count($request->ids)!=2){
            return ApiResponseClass::sendResponse(null,'Locations count must be 2',400);
        }
        $distance=$this->locationRepositoryInterface->makeRoute($request->ids);
        $rslt=[
            "related_locations"=>$request->ids,
            "result"=>$distance
        ];
        return ApiResponseClass::sendResponse($rslt,'Distance Calculated',200);
    }
}
