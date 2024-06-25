<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateLocationRequest;
use App\Http\Requests\Admin\UpdateLocationRequest;
use App\Http\Requests\BaseGridRequest;
use App\Http\Resources\LocationResource;
use App\Repositories\Admin\AdminLocationRepository;

/**
 * Class AdminServiceUnitController
 * @package App\Http\Controllers\API\Orders\Admin
 */
class AdminLocationController extends Controller
{
    /**
     * @var AdminLocationRepository
     */
    private $repository;

    function __construct(
        AdminLocationRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @param BaseGridRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getGrid(BaseGridRequest $request)
    {
        $requestData = $request->validated();

        return $this->repository->getGrid($requestData);
    }

    /**
     * @param CreateLocationRequest $request
     * @return LocationResource
     */
    public function create(CreateLocationRequest $request)
    {
        $requestData = $request->validated();

        return new LocationResource($this->repository->create($requestData));
    }

    /**
     * @param UpdateLocationRequest $request
     * @return mixed
     */
    public function update(UpdateLocationRequest $request)
    {
        $requestData = $request->validated();

        return $this->repository->update($requestData);
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function delete(int $id)
    {
        $this->repository->delete($id);

        return response(['status' => 'success'], 200);
    }
}
