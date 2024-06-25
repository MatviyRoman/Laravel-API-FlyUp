<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Language;
use App\Article;

class ArticleAuthorAll extends Resource
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
		    'id' => $this->articleAuthor->id,
		    'name' => $this->name,
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
			'articles_number' => Article::where('article_author_id', $this->articleAuthor->id)->count(),
			'language' => Language::where('id', $this->language_id)->select('id', 'name', 'flag')->first(),
			'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get()
		];
	}
}
