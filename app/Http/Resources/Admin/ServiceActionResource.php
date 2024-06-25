<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\Resource;

class ServiceActionResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $service = $this->service->translation->first();

        return [
            'id' => $this->id,
            'service_control' => [
                'id' => $this->service_control_id,
                'name' => $this->serviceControl->name,
                'type' => $this->serviceControl->type,
            ],
            'service' => [
                'id' => $this->service_id,
                'title' => $service->title,
            ],
            'result' => $this->result,
            'sum' => $this->sum,
            'notes' => $this->notes,
            'data' => $this->data,
            'created_at' => date('d/m/Y', strtotime($this->created_at)),
        ];
    }
}
