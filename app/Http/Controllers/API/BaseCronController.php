<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseGridRequest;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class BaseCronController extends Controller
{
    public $repository;

    /** @var JsonResource|string */
    protected $resource;

    function __construct($repository, ?string $resource = null) {
        $this->repository = $repository;
        $this->resource = $resource ? $resource : JsonResource::class;
    }

    /**
     * @param BaseGridRequest $request
     * @return mixed
     */
    public function getGrid(BaseGridRequest $request)
    {
        $requestData = $request->validated();

        return $this->repository->getGrid($requestData);
    }

    /**
     * @param int $id
     * @return JsonResource
     */
    public function show(int $id): JsonResource
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

        return response(['status' => 'success']);
    }

    /**
     * @param int $modelId
     * @param int $fileId
     * @return ResponseFactory|Response
     */
    public function deleteMedia(int $modelId, int $fileId)
    {
        $this->repository->deleteMedia($modelId, $fileId);

        return response(['status' => 'success']);
    }
}
