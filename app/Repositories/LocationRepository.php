<?php

namespace App\Repositories;

use App\Interfaces\LocationRepositoryInterface;
use App\Models\Location;
use KMLaravel\GeographicalCalculator\Classes\GeographicalCalculator;

class LocationRepository implements LocationRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getAll()
    {
        return Location::all();
    }

    public function getDetail($id)
    {
        // TODO: Implement getDetail() method.
        return Location::find($id);
    }

    public function store(array $data)
    {
        // TODO: Implement store() method.
        return Location::create($data);
    }

    public function update(array $data, $id)
    {
        // TODO: Implement update() method.
        return Location::whereId($id)->update($data);
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
        return Location::destroy($id);
    }

    public function makeRoute(array $ids)
    {
        $first = Location::find($ids[0]);
        $second = Location::find($ids[1]);

        $geo= new GeographicalCalculator();
        $geo->initCoordinates($first->lat,$second->lat,$first->long,$second->long,['units' => ['km','m','mile']]);
        return $geo->getDistance();
    }
}
