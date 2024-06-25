<?php

namespace App\Http\Resources\ServiceCategories\Admin;

use App\Language;
use Illuminate\Http\Resources\Json\Resource;

class ServiceCategoryAll extends Resource
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
		    'id' => $this->serviceCategory->id,
		    'url' => $this->url,
		    'title' => $this->title,
		    'seo_title' => $this->seo_title,
		    'keywords' => $this->keywords,
		    'description' => $this->description,
            'alt' => $this->alt,
		    'text' => $this->text,
            'subtext' => $this->subtext,
		    'image' => $this->serviceCategory->image,
		    'seo_image' => $this->serviceCategory->seo_image,
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
			'language' => Language::where('id', $this->language_id)
				->select('id', 'name', 'flag')
				->first(),
			'languages' => Language::orderBy('order')
				->select('id', 'name', 'flag')
				->get(),
		];
	}
}
