<?php

namespace App\Repositories;

use App\Discount;
use App\Http\Resources\Admin\DiscountResource;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use VklComponents\VklTable\VklTableBuilder;

class DiscountRepository
{
    private $model = Discount::class;

    /**
     * @param array $requestData // grid request data with service_id
     * @return mixed
     */
    public function getGrid(array $requestData)
    {
        $builder = $this->model::query();

        $table = new VklTableBuilder($builder, $requestData, DiscountResource::class);

        $table->setSearchableColumns(['code', 'percent', 'value']);

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

    /**
     * @param string $code
     * @return mixed
     */
    public function getByCodeForUser(string $code)
    {
        $now = Carbon::now();

        $entity = $this->model::where('code', $code)
            ->where('is_for_admin', 0)
            ->where(function ($query) use ($now) {
                $query->where('start', '<=', $now->toDateTimeString())
                    ->orWhereNull('start');
            })
            ->where(function ($query) use ($now) {
                $query->where('end', '>', $now->toDateTimeString())
                    ->orWhereNull('end');
            })
            ->first();

        if (!$entity) {
            throw new HttpResponseException(response('Discount code not found', 400));
        }

        return $entity;
    }
}