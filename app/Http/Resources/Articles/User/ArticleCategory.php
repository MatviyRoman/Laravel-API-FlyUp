<?php

namespace App\Http\Resources\Articles\User;

use App\Article;
use Illuminate\Http\Resources\Json\Resource;

class ArticleCategory extends Resource
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
		    'icon' => $this->icon ? utf8_encode(file_get_contents($this->icon)) : '',
		    'name' => $this->title,
		    'url' => $this->url,
		    'articles_number' => Article::where('article_category_id', $this->id)->count()
	    ];
    }
}
