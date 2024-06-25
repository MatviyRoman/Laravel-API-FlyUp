<?php

namespace App\Http\Resources\Admin\ServiceControl;

use Illuminate\Http\Resources\Json\Resource;

class ServiceControlItemResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $item = $this->translation->first();

        return [
            'id' => $this->id,
            'title' => $item->title,
        ];
    }
}
