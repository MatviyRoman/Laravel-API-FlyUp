<?php

namespace App\Http\Resources\Services;

use Illuminate\Http\Resources\Json\Resource;

class ServiceComponentResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'name' => $this->name,
            'notes' => $this->notes,
            'image' => $this->image,
            'reg_number' => $this->reg_number,
            'price' => $this->price,
            'work_start' => $this->work_start ? $this->work_start->format('Y-m-d H:i') : null,
            'work_end' => $this->work_end ? $this->work_end->format('Y-m-d H:i') : null,
            'repair' => $this->repair,
            'inspection' => $this->inspection,
            'count' => $this->count,
            'created' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
