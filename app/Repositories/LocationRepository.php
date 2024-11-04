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
        return Location::find($id);
    }

    public function store(array $data)
    {
        return Location::create($data);
    }

    public function update(array $data, $id)
    {
        return Location::whereId($id)->update($data);
    }

    public function delete($id)
    {
        return Location::destroy($id);
    }

    public function makeRoute(array $data)
    {
        $route = [];
        $dbLocations = Location::all()->toArray();
        $currentPoint = $data;

        while (!empty($dbLocations)) {
            foreach ($dbLocations as $key => &$location) {
                $geo = new GeographicalCalculator();
                $geo->initCoordinates($currentPoint["lat"], $location["lat"], $currentPoint["long"], $location["long"], ['units' => ['km', 'm', 'mile']]);
                $location["distance"] = $geo->getDistance();
            }

            usort($dbLocations, function ($a, $b) {
                return $a['distance']['km'] <=> $b['distance']['km'];
            });

            $closestLocation = array_shift($dbLocations);
            $route[] = $closestLocation;

            $currentPoint = $closestLocation;
        }

        return $route;
    }
}
