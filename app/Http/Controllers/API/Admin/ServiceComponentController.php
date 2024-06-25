<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Admin\StoreServiceComponentRequest;
use App\Http\Resources\Services\ServiceComponentResource;
use App\Models\ServiceComponent;
use App\Repositories\BaseRepository;

class ServiceComponentController extends BaseApiController
{
    function __construct() {
        parent::__construct(new BaseRepository(ServiceComponent::class, ServiceComponentResource::class), ServiceComponentResource::class);
    }

    /**
     * @param StoreServiceComponentRequest $request
     * @return ServiceComponentResource
     */
    public function create(StoreServiceComponentRequest $request)
    {
        $requestData = $request->validated();

        return new $this->resource($this->repository->create($requestData));
    }

    /**
     * @param StoreServiceComponentRequest $request
     * @return ServiceComponentResource
     */
    public function update(StoreServiceComponentRequest $request)
    {
        $requestData = $request->validated();

        return new $this->resource($this->repository->update($requestData));
    }
}
