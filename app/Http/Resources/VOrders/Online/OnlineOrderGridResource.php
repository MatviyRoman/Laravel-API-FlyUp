<?php

namespace App\Http\Resources\VOrders\Online;

use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use VklComponents\VklTable\VklTableHelper;

class OnlineOrderGridResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $service = $this->service->translation->first();

        return [
            'id' => $this->id,
            'service' => [
                'id' => $this->service_id,
                'title' => $service->title,
                'url' => $service->url,
            ],
            'location' => $this->serviceUnit->location->name,
            'status' => $this->calculateStatus(),
            'start' => $this->start,
            'end' => $this->end,
            'price' => $this->price,
            'data' => $this->data,
            'type' => $this->type,
            'created_at' => VklTableHelper::convertBaseDateTimeToAppDateTime($this->created_at),
        ];
    }
}
