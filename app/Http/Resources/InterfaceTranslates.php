<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class InterfaceTranslates extends Resource
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
		    'interface_entity_id' => $this->interface_entity_id,
		    'value' => $this->value,
	    ];
    }
}
