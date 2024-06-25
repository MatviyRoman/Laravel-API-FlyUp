<?php

namespace App\Http\Resources\Articles\User;

use App\ArticleCategoryTranslate;
use Illuminate\Http\Resources\Json\Resource;

class ArticleCategoryAll extends Resource
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
		    'seo_image' => $this->seo_image,
		    'title' => $this->title,
		    'seo_title' => $this->seo_title,
		    'keywords' => $this->keywords,
		    'description' => $this->description
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
			'languages' => ArticleCategoryTranslate::where('article_category_id', $this->id)
				->join('languages', 'article_category_translates.language_id', '=', 'languages.id')
				->select('languages.id', 'languages.name', 'languages.flag', 'article_category_translates.url')
				->get()
		];
	}
}
