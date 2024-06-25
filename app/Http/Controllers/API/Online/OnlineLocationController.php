<?php

namespace App\Http\Controllers\API\Online;

use App\Http\Controllers\Controller;
use App\Http\Requests\Online\GerServiceLocationsRequest;
use App\Repositories\Admin\AdminLocationRepository;
use App\Repositories\LocationRepository;
use App\Repositories\Online\OnlineLocationRepository;
use App\Repositories\OrderRepository;

/**
 * Class OnlineLocationController
 * @package App\Http\Controllers\API\Admin
 */
class OnlineLocationController extends Controller
{
    /**
     * @var AdminLocationRepository
     */
    private $repository;

    /**
     * @var LocationRepository
     */
    private $commonLocationRepository;

    /**
     * @var OrderRepository
     */
    private $commonOrderRepository;

    function __construct(
        OnlineLocationRepository $repository,
        LocationRepository $commonLocationRepository,
        OrderRepository $commonOrderRepository
    ) {
        $this->repository = $repository;
        $this->commonLocationRepository = $commonLocationRepository;
        $this->commonOrderRepository = $commonOrderRepository;
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->repository->getAll();
    }

    /**
     * Show available service locations
     *
     * @param GerServiceLocationsRequest $request
     * @return array
     */
    public function serviceLocations(GerServiceLocationsRequest $request)
    {
        $requestData = $request->validated();

        $locationsIds = $this->commonOrderRepository->getServiceLocations((int)$requestData['service_id']);
        $serviceLocations = $this->commonLocationRepository->getByIds($locationsIds);

        return $serviceLocations;
    }
}
