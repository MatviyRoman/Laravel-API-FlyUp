<?php

namespace App\Repositories\Admin;

use App\Repositories\BaseCronRepository;
use App\ServiceAction;
use Illuminate\Database\Eloquent\Builder;
use VklComponents\VklTable\VklTableBuilder;

class ServiceActionRepository extends BaseCronRepository
{
    function __construct(?string $gridResource = null) {
        parent::__construct(ServiceAction::class, $gridResource);
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

        $table->setCustomFilterForColumn('type', function ($query, $filterValues) use ($requestData) {
            $query->whereIn('service_controls.type', $filterValues);
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
            ->join('service_controls', 'service_controls.id', 'service_actions.service_control_id')
            ->groupBy('service_actions.id')
            ->select('service_actions.*');
    }
}