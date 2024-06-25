<?php

namespace App\Http\Resources\Services\User;

use App\ServiceCategoryTranslate;
use App\ServiceTranslate;
use App\Http\Controllers\MainController;
use Illuminate\Http\Resources\Json\Resource;
use App\ArticleAuthorTranslate;

class ServiceAll extends Resource
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
			'title' => $this->title,
            'price' => $this->price,
            'price2' => $this->price2,
            'price3' => $this->price3,
            'is_no_price' => $this->is_no_price,
            'is_delivery_required' => $this->is_delivery_required,
			'seo_title' => $this->seo_title,
			'keywords' => $this->keywords,
			'description' => $this->description,
			'text' => $this->text,
			'subtext' => $this->subtext,
			'image' => $this->image,
			'seo_image' => $this->seo_image,
            'alt' => $this->alt,
			'likes' => $this->likes,
			'views' => $this->views,
            'icon' => $this->icon ? utf8_encode(file_get_contents($this->icon)) : '',
            'docs' => $this->docs,
			'is_liked' => MainController::isLike('services', $this->id),
			'created_at' => date('d/m/Y', strtotime($this->created_at)),
		];
	}


	/**
	 * Get additional data that should be returned with the resource array.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return array
	 */
	public function with($request)
	{
		return [
			'languages' => ServiceTranslate::where('service_id', $this->id)
				->join('services', 'service_translates.service_id', '=', 'services.id')
				->join('languages', 'service_translates.language_id', '=', 'languages.id')
				->orderBy('languages.id')
				->select('languages.id', 'languages.name', 'languages.flag', 'service_translates.url')
				->get(),
            'category_urls' => ServiceCategoryTranslate::where('service_category_id', $this->service_category_id)
                ->select('language_id', 'url as category_url')
                ->get(),
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
