<?php

namespace App\Repositories\Admin;

use App\Http\Resources\Services\Admin\AdminServiceUnitResource;
use App\ServiceUnit;
use Illuminate\Http\Exceptions\HttpResponseException;
use VklComponents\VklTable\VklTableBuilder;

class AdminServiceUnitRepository
{
    private $model = ServiceUnit::class;

    /**
     * @param array $requestData // grid request data with service_id
     * @return mixed
     */
    public function getGrid(array $requestData)
    {
        $builder = $this->model::query()->with(['location', 'service.translation']);

        if (array_key_exists('service_id', $requestData) && $requestData['service_id']) {
            $builder->where('service_id', $requestData['service_id']);
        }

        $table = new VklTableBuilder($builder, $requestData, AdminServiceUnitResource::class);

        $table->setSearchableColumns(['name', 'notes']);

        return $table->resolve();
    }

    /**
     * @param array $serviceUnitsIds
     * @return mixed
     */
    public function getByIds(array $serviceUnitsIds)
    {
        $entities = $this->model::find($serviceUnitsIds);

        return $entities;
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