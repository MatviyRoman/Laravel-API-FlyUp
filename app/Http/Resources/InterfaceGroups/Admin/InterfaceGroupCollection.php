<?php

namespace App\Http\Resources\InterfaceGroups\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class InterfaceGroupCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
