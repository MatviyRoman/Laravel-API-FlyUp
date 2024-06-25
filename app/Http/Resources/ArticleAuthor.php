<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Article;
use App\ArticleAuthor as ArticleAuthorModel;

class ArticleAuthor extends Resource
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
		    'is_active' => $this->is_active,
		    'order' => $this->order,
		    'name' => $this->name,
		    'articles_number' => Article::where('article_author_id', $this->id)->count(),
		    'languages' => Language::collection(ArticleAuthorModel::where('id', $this->id)->with('languages')->first()->languages)
	    ];
    }
}
