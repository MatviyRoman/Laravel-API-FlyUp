<?php

namespace App\Http\Resources\Admin\ServiceControl;

use Illuminate\Http\Resources\Json\Resource;

class ServiceControlResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'data' => $this->data,
            'services' => ServiceControlItemResource::collection($this->services),
        ];
    }
}
