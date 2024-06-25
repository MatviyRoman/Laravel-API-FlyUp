<?php

namespace App\Http\Resources\Contacts\Admin;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\Language;
use App\Contact as ContactModal;

class Contact extends Resource
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
		    'is_active' => $this->is_active,
		    'order' => $this->order,
            'name' => $this->name,
            'skype' => $this->skype,
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => $this->image,
            'languages' => Language::collection(ContactModal::find($this->id)->languages),
	    ];
    }
}
