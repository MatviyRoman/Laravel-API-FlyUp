<?php

namespace App\Http\Resources\Pages\Admin;

use App\Http\Resources\Language;
use App\Page as PageModel;
use Illuminate\Http\Resources\Json\Resource;

class Page extends Resource
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
		    'image' => $this->image,
		    'additional_images' => $this->additional_images,
		    'title' => $this->title,
		    'keywords' => $this->keywords,
		    'description' => $this->description,
		    'url' => $this->url,
		    'languages' => Language::collection(PageModel::find($this->id)->languages),
	    ];
    }
}
