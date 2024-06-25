<?php

namespace App\Http\Resources\Services\Admin;

use App\Language;
use App\ServiceCategoryTranslate;
use Illuminate\Http\Resources\Json\Resource;
use App\ArticleAuthorTranslate;
use Illuminate\Support\Facades\DB;

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
		    'id' => $this->article->id,
		    'url' => $this->url,
		    'title' => $this->title,
		    'seo_title' => $this->seo_title,
		    'keywords' => $this->keywords,
		    'description' => $this->description,
            'alt' => $this->alt,
		    'text' => $this->text,
		    'subtext' => $this->subtext,
		    'is_delivery_required' => $this->article->is_delivery_required,
		    'is_no_price' => $this->article->is_no_price,
		    'price' => $this->article->price,
		    'price2' => $this->article->price2,
		    'price3' => $this->article->price3,
		    'image' => $this->article->image,
		    'seo_image' => $this->article->seo_image,
		    'icon' => $this->article->icon,
		    'docs' => $this->article->docs,
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
            'service_category' => ServiceCategoryTranslate::where('service_category_id', $this->article->service_category_id)
                ->where('language_id', env('DEFAULT_LANG_ID', 1))
                ->select('service_category_id as id', 'title')
                ->first(),
            'service_categories' => DB::table('service_categories')
                ->join('service_category_translates', 'service_categories.id', '=', 'service_category_translates.service_category_id')
                ->where('service_category_translates.language_id', env('DEFAULT_LANG_ID', 1))
                ->select('service_categories.id', 'service_category_translates.title')
                ->get(),
		];
	}
}
