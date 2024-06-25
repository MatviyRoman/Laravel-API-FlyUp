<?php

namespace App\Http\Resources\InterfaceTranslations\User;

use Illuminate\Http\Resources\Json\ResourceCollection;

class InterfaceEntityCollection extends ResourceCollection
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
