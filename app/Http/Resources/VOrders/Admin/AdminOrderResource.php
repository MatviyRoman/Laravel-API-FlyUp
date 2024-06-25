<?php

namespace App\Http\Resources\VOrders\Admin;

use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use VklComponents\VklTable\VklTableHelper;

class AdminOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $serviceData = null;

        if ($this->service) {
            $serviceData['id'] = $this->service_id;
            $serviceData['image'] = $this->service->image;

            $serviceTranslation = $this->service->translation->first();

            if ($serviceTranslation) {
                $serviceData['title'] = $serviceTranslation->title;
                $serviceData['url'] = $serviceTranslation->url;
            }
        }

        $serviceUnitData = $this->serviceUnit ? [
            'id' => $this->service_unit_id,
            'name' => $this->serviceUnit->name,
            'notes' => $this->serviceUnit->notes,
            'location' => $this->serviceUnit->location->name,
        ] : null;

        $userData = $this->user_id ? [
            'user_id' => $this->user->id,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
        ] : [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];

        $adminData = $this->admin_id ? [
            'user_id' => $this->admin->id,
            'first_name' => $this->admin->first_name,
            'last_name' => $this->admin->last_name,
            'email' => $this->admin->email,
            'phone' => $this->admin->phone,
        ] : null;

        return [
            'id' => $this->id,
            'service' => $serviceData,
            'service_unit' => $serviceUnitData,
            'status' => $this->calculateStatus(),
            'language' => $this->language,

            'type' => $this->type,
            'data' => $this->data,

            'start' => $this->start,
            'end' => $this->end,

            'user_data' => $userData,
            'admin_data' => $adminData,

            'origin_price' => $this->origin_price,
            'price' => $this->price,
            'discount_code' => $this->discount_code,
            'created_at' => VklTableHelper::convertBaseDateTimeToAppDateTime($this->created_at)
        ];
    }
}
