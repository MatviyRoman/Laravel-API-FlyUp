<?php

namespace App\Http\Resources\Services\User;

use App\ServiceCategoryTranslate;
use Illuminate\Http\Resources\Json\Resource;

class Service extends Resource
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
			'image' => $this->image,
			'views' => $this->views,
			'likes' => $this->likes,
            'icon' => $this->icon ? utf8_encode(file_get_contents($this->icon)) : '',
			'name' => $this->title,
            'price' => $this->price,
            'price2' => $this->price2,
            'price3' => $this->price3,
            'is_delivery_required' => $this->is_delivery_required,
            'is_no_price' => $this->is_no_price,
			'subtext' => $this->subtext,
			'url' => $this->url,
			'created_at' => date('d/m/Y', strtotime($this->created_at)),
            'service_category' => ServiceCategoryTranslate::where('service_category_id', $this->service_category_id)
                ->where('language_id', $this->language_id)
                ->first() ?
                ServiceCategoryTranslate::where('service_category_id', $this->service_category_id)
                    ->where('language_id', $this->language_id)
                    ->select('service_category_id as id', 'title', 'url')
                    ->first() :
                ServiceCategoryTranslate::where('service_category_id', $this->service_category_id)
                    ->where('language_id', env('DEFAULT_LANG_ID', 1))
                    ->select('service_category_id as id', 'title', 'url')
                    ->first(),
		];
	}
}
