<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginationCollection extends ResourceCollection
{
    /**
     * @var array
     */
    private $pagination;

    /**
     * Api JsonResource class to transform data
     * @var string|null
     */
    private $resourceClass;

    public function __construct($resource, ?string $resourceClass = null)
    {
        $this->resourceClass = $resourceClass;

        $this->pagination = [
            'total' => $resource->total(),
            'count' => $resource->count(),
            'per_page' => $resource->perPage(),
            'current_page' => $resource->currentPage(),
            'total_pages' => $resource->lastPage()
        ];

        $resource = $resource->getCollection();

        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'data' => $this->resourceClass ? $this->resourceClass::collection($this->collection) : $this->collection,
            'pagination' => $this->pagination
        ];
    }
}