<?php

namespace App\Repositories\Online;

use App\Location;

class OnlineLocationRepository
{
    private $model = Location::class;

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->model::all();
    }
}