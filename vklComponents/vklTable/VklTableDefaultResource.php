<?php

namespace VklComponents\VklTable;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The default resource class for data output in vklComponents/vklTable/VklTableBuilder.php.
 * @package vklComponents\vklTable
 */
class VklTableDefaultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        // Convert an eloquent model to an array
        if (get_parent_class($this->resource) == 'App\Models\BaseModel' || get_parent_class($this->resource) == 'App\Models\Model') {
            return $this->resource->toArray();
        }

        return (array)$this->resource;
    }
}