<?php

namespace App\Http\Resources\Pages\Admin;

use App\Language;
use Illuminate\Http\Resources\Json\Resource;

class PageAll extends Resource
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
			'name' => $this->page->name,
			'image' => $this->page->image,
			'additional_images' => $this->page->additional_images,
			'title' => $this->title,
			'seo_title' => $this->seo_title,
			'keywords' => $this->keywords,
			'description' => $this->description,
			'url' => $this->url,
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
