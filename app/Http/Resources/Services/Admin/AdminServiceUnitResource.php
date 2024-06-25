<?php

namespace App\Http\Resources\Services\Admin;

use Illuminate\Http\Resources\Json\Resource;

class AdminServiceUnitResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $translation = $this->service->translation->first();

        return [
            'id' => $this->id,
            'location' => $this->location_id ? [
                'id' => $this->location->id,
                'name' => $this->location->name,
            ] : null,
            'service' => $this->service_id ? [
                'id' => $this->service->id,
                'name' => $translation ? $translation->title : null,
                'url' => $translation ? $translation->url : null,
            ] : null,
            'name' => $this->name,
            'notes' => $this->notes,
            'image' => $this->image,
            'number' => $this->number,
            'price' => $this->price,
            'work_start' => $this->work_start ? $this->work_start->format('d/m/Y H:i') : null,
            'work_end' => $this->work_end ? $this->work_end->format('d/m/Y H:i') : null,
            'repair' => $this->repair,
            'inspection' => $this->inspection,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}
