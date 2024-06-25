<?php

namespace App\Repositories\Admin;

use App\Http\Resources\LocationResource;
use App\Location;
use Illuminate\Http\Exceptions\HttpResponseException;
use VklComponents\VklTable\VklTableBuilder;

class AdminLocationRepository
{
    private $model = Location::class;

    /**
     * @param array $requestData // grid request data with service_id
     * @return mixed
     */
    public function getGrid(array $requestData)
    {
        $builder = $this->model::query();

        $table = new VklTableBuilder($builder, $requestData, LocationResource::class);

        $table->setSearchableColumns(['name']);

        return $table->resolve();
    }

    /**
     * @param array $requestData
     * @return mixed
     */
    public function create(array $requestData)
    {
        $entity = $this->model::create($requestData);

        return $entity;
    }

    /**
     * @param array $requestData
     * @return mixed
     */
    public function update(array $requestData)
    {
        $entity = $this->find($requestData['id']);

        $entity->update($requestData);

        return $entity;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        $entity = $this->find($id);

        return $entity->delete();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        $entity = $this->model::find($id);

        if (!$entity) {
            throw new HttpResponseException(response('Not found', 404));
        }

        return $entity;
    }
}