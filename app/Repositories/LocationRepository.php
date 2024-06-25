<?php

namespace App\Repositories;

use App\Location;

class LocationRepository
{
    private $model = Location::class;

    /**
     * @param array $locationIds
     * @return mixed
     */
    public function getByIds(array $locationIds)
    {
        $entities = $this->model::find($locationIds);

        return $entities;
    }
}