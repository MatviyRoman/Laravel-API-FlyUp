<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Language;
use App\ServiceTranslate;

class FeedbackAll extends Resource
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
		    'message' => $this->message,
		    'comment' => $this->comment,
            'phone' => $this->phone,
            'type' => $this->type,
		    'file' => $this->file,
		    'language' => Language::select('id', 'name', 'flag')->find($this->language_id),
		    'service' => ServiceTranslate::where('service_id', $this->service_id)->where('language_id', env('DEFAULT_LANG_ID', 1))->select('title')->first(),
		    'created_at' => $this->created_at->format('d.m.Y H:i:s')
	    ];
    }

	/**
	 * Get additional data that should be returned with the resource array.
	 *
	 * @param \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function with($request)
	{
		return [
			'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get()
		];
	}
}
