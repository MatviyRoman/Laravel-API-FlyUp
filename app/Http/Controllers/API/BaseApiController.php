<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseGridRequest;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\Resource;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseApiController
 * @package App\Http\Controllers\API
 */
class BaseApiController extends Controller
{
    /** @var $repository BaseRepository */
    protected $repository;

    protected $resource;

    function __construct($repository, ?string $resource = null) {
        $this->repository = $repository;
        $this->resource = $resource ? $resource : Resource::class;
    }

    /**
     * @param int $id
     * @return Resource
     */
    public function show(int $id)
    {
        return new $this->resource($this->repository->getById($id));
    }

    /**
     * @param int $id
     * @return ResponseFactory|Response
     */
    public function delete(int $id)
    {
        $this->repository->delete($id);

        return response(['status' => 'success'], 200);
    }

    /**
     * @param BaseGridRequest $request
     * @return AnonymousResourceCollection
     */
    public function getGrid(BaseGridRequest $request)
    {
        $requestData = $request->validated();

        return $this->repository->getGrid($requestData);
    }
}
