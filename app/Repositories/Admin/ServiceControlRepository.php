<?php

namespace App\Repositories\Admin;

use App\Repositories\BaseCronRepository;
use App\ServiceControl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use VklComponents\VklTable\VklTableBuilder;

class ServiceControlRepository extends BaseCronRepository
{
    function __construct(?string $gridResource = null) {
        parent::__construct(ServiceControl::class, $gridResource);
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

        $table->setCustomFilterForColumn('service_id', function ($query, $filterValues) use ($requestData) {
            $query->whereIn('service_control_services.service_id', $filterValues);
        });

        return $table->resolve();
    }

    /**
     * @param array|null $requestData
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public function getGridQuery(array $requestData)
    {
        return $this->modelClass::query()
            ->join('service_control_services', 'service_control_services.service_control_id', 'service_controls.id')
            ->groupBy('service_controls.id')
            ->select('service_controls.*');
    }

    /**
     * Create admin with media and translations.
     *
     * @param array $requestData
     * @return Model
     */
    public function create(array $requestData): Model
    {
        $model = parent::create($requestData);

        $this->syncServices($model, $requestData);

        return $model;
    }

    /**
     * Update admin with media and translations.
     *
     * @param int $id
     * @param array $requestData
     * @return Model
     * @throws \Throwable
     */
    public function update(int $id, array $requestData): Model
    {
        $model = parent::update($id, $requestData);

        $this->syncServices($model, $requestData);

        return $model;
    }

    public function syncServices($model, $requestData)
    {
        if (array_key_exists('service_ids', $requestData)) {
            $model->services()->sync($requestData['service_ids']);
        }
    }
}