<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateDiscountRequest;
use App\Http\Requests\Admin\UpdateDiscountRequest;
use App\Http\Requests\BaseGridRequest;
use App\Http\Resources\Admin\DiscountResource;
use App\Repositories\DiscountRepository;

/**
 * Class AdminDiscountController
 * @package App\Http\Controllers\API\Admin
 */
class AdminDiscountController extends Controller
{
    /**
     * @var DiscountRepository
     */
    private $repository;

    function __construct(
        DiscountRepository $repository
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
     * @param CreateDiscountRequest $request
     * @return DiscountResource
     */
    public function create(CreateDiscountRequest $request)
    {
        $requestData = $request->validated();

        return new DiscountResource($this->repository->create($requestData));
    }

    /**
     * @param UpdateDiscountRequest $request
     * @return mixed
     */
    public function update(UpdateDiscountRequest $request)
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
