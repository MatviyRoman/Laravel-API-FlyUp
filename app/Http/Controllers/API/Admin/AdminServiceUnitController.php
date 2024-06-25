<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateServiceUnitRequest;
use App\Http\Requests\Admin\GerServiceAvailabilityRequest;
use App\Http\Requests\Admin\GridServiceUnitRequest;
use App\Http\Requests\Admin\UpdateServiceUnitRequest;
use App\Http\Resources\Services\Admin\AdminServiceUnitResource;
use App\Repositories\Admin\AdminServiceUnitRepository;
use App\Repositories\OrderRepository;

/**
 * Class AdminServiceUnitController
 * @package App\Http\Controllers\API\Admin
 */
class AdminServiceUnitController extends Controller
{
    /**
     * @var AdminServiceUnitRepository
     */
    private $repository;

    /**
     * @var OrderRepository
     */
    private $commonOrderRepository;

    function __construct(
        AdminServiceUnitRepository $repository,
        OrderRepository $commonOrderRepository
    ) {
        $this->repository = $repository;
        $this->commonOrderRepository = $commonOrderRepository;
    }

    /**
     * @param GridServiceUnitRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getGrid(GridServiceUnitRequest $request)
    {
        $requestData = $request->validated();

        return $this->repository->getGrid($requestData);
    }

    /**
     * Show service unit
     *
     * @param int $id
     * @return AdminServiceUnitResource
     */
    public function show(int $id)
    {
        $order = $this->repository->find($id);

        return new AdminServiceUnitResource($order);
    }

    /**
     * @param CreateServiceUnitRequest $request
     * @return AdminServiceUnitResource
     */
    public function create(CreateServiceUnitRequest $request)
    {
        $requestData = $request->validated();

        return new AdminServiceUnitResource($this->repository->create($requestData));
    }

    /**
     * @param UpdateServiceUnitRequest $request
     * @return mixed
     */
    public function update(UpdateServiceUnitRequest $request)
    {
        $requestData = $request->validated();

        return $this->repository->update($requestData);
    }

    /**
     * @param GerServiceAvailabilityRequest $request
     * @return mixed
     */
    public function getAvailableServiceUnits(GerServiceAvailabilityRequest $request)
    {
        $requestData = $request->validated();

        $availableServiceUnitsIds = $this->commonOrderRepository->getAvailableServiceUnitsIds((int)$requestData['service_id'], (int)$requestData['location_id'], $requestData['start'], $requestData['end']);
        $availableServiceUnits = $this->repository->getByIds($availableServiceUnitsIds);

        return AdminServiceUnitResource::collection($availableServiceUnits);
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
