<?php

namespace App\Http\Resources\Pages\Admin;

use Illuminate\Http\Resources\Json\Resource;

class InterfaceGroup extends Resource
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
		    'title' => $this->title
	    ];
    }
}
