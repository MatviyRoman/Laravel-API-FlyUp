<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use VklComponents\VklTable\VklTableBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class BaseCronRepository
{
    /** @var Model|string */
    protected $modelClass;

    /** @var string */
    protected $gridResource;

    function __construct(string $modelClass, ?string $gridResource = null)
    {
        $this->modelClass = $modelClass;
        $this->gridResource = $gridResource ? $gridResource : JsonResource::class;
    }

    /**
     * @param array $requestData
     * @return mixed
     */
    public function getGrid(array $requestData)
    {
        return $this->buildGrid($this->getGridQuery($requestData), $requestData);
    }

    /**
     * Apply the query and get the grid data.
     *
     * @param Builder|\Illuminate\Database\Query\Builder $query
     * @param array $requestData - data from request Components/VklTable/TableRequest.php or from a child class
     * @return mixed
     */
    public function buildGrid($query, array $requestData)
    {
        $table = new VklTableBuilder($query, $requestData, $this->gridResource);

        return $table->resolve();
    }

    /**
     * @param array|null $requestData
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public function getGridQuery(array $requestData)
    {
        return $this->modelClass::query();
    }

    /**
     * @param array $requestData
     * @return Model
     */
    public function create(array $requestData): Model
    {
        return $this->modelClass::create($requestData);
    }

    /**
     * @param int $id
     * @param array $requestData
     * @return Model
     * @throws Throwable
     */
    public function update(int $id, array $requestData): Model
    {
        $model = $this->getById($id);

        $model->update($requestData);

        return $model;
    }

    /**
     * @param int $id
     * @return bool|null
     * @throws Throwable
     */
    public function delete(int $id): ?bool
    {
        $model = $this->getById($id);

        return $model->delete();
    }

    /**
     * @param int $id
     * @return Model|mixed
     * @throws Throwable
     */
    public function getById(int $id): Model
    {
        $model = $this->modelClass::find($id);

        throw_unless($model,NotFoundHttpException::class);

        return $model;
    }
}
