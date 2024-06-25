<?php

namespace App\Http\Resources;

use App\ServiceTranslate;
use Illuminate\Http\Resources\Json\Resource;
use App\Language;

class Feedback extends Resource
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
		    'email' => $this->email,
            'type' => $this->type,
            'phone' => $this->phone,
		    'is_viewed' => $this->is_viewed,
		    'language' => Language::select('id', 'name', 'flag')->find($this->language_id),
		    'service' => ServiceTranslate::where('service_id', $this->service_id)->where('language_id', env('DEFAULT_LANG_ID', 1))->select('title')->first(),
		    'created_at' => $this->created_at->format('d.m.Y H:i:s')
	    ];
    }
}
