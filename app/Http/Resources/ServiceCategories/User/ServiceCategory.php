<?php

namespace App\Http\Resources\ServiceCategories\User;

use Illuminate\Http\Resources\Json\Resource;

class ServiceCategory extends Resource
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
            'name' => $this->title,
			'image' => $this->image ? utf8_encode(file_get_contents($this->image)) : '',
			'seo_image' => $this->seo_image,
            'subtext' => $this->subtext,
			'url' => $this->url
		];
	}
}
