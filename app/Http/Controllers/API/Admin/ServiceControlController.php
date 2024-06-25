<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseCronController;
use App\Http\Requests\Admin\StoreServiceControlRequest;
use App\Http\Resources\Admin\ServiceControl\ServiceControlGridResource;
use App\Http\Resources\Admin\ServiceControl\ServiceControlResource;
use App\Repositories\Admin\ServiceControlRepository;

class ServiceControlController extends BaseCronController
{
    function __construct() {
        parent::__construct(new ServiceControlRepository(ServiceControlGridResource::class), ServiceControlResource::class);
    }

    public function create(StoreServiceControlRequest $request)
    {
        $requestData = $request->validated();

        return new $this->resource($this->repository->create($requestData));
    }

    public function update(int $id, StoreServiceControlRequest $request)
    {
        $requestData = $request->validated();

        return new $this->resource($this->repository->update($id, $requestData));
    }
}
