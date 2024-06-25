<?php

namespace App\Repositories;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Resources\Json\Resource;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use VklComponents\VklTable\VklTableBuilder;

class BaseRepository
{
    protected $model;
    protected $gridResource;

    function __construct($model, $gridResource = null) {
        $this->model = $model;
        $this->gridResource = $gridResource ? $gridResource : Resource::class;
    }

    /**
     * @param array $requestData
     * @return mixed
     */
    public function getGrid(array $requestData)
    {
        $builder = $this->model::query();

        $table = new VklTableBuilder($builder, $requestData, $this->gridResource);

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
        if (!array_key_exists('id', $requestData)) {
            throw new UnprocessableEntityHttpException('Id is required');
        }

        $entity = $this->getById($requestData['id']);

        $entity->update($requestData);

        return $entity;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        $entity = $this->getById($id);

        return $entity->delete();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getById(int $id)
    {
        $entity = $this->model::find($id);

        if (!$entity) {
            throw new HttpResponseException(response('Not found', 404));
        }

        return $entity;
    }
}