<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseCronController;
use App\Http\Requests\Admin\StoreServiceActionRequest;
use App\Http\Resources\Admin\ServiceActionResource;
use App\Repositories\Admin\ServiceActionRepository;


class ServiceActionController extends BaseCronController
{
    function __construct() {
        parent::__construct(new ServiceActionRepository(ServiceActionResource::class), ServiceActionResource::class);
    }

    public function create(StoreServiceActionRequest $request)
    {
        $requestData = $request->validated();

        return new $this->resource($this->repository->create($requestData));
    }

    public function update(int $id, StoreServiceActionRequest $request)
    {
        $requestData = $request->validated();

        return new $this->resource($this->repository->update($id, $requestData));
    }
}
