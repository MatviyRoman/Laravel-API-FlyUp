<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleTranslate extends Model
{
	protected $fillable = [
		'article_id',
		'language_id',
		'title',
		'seo_title',
		'keywords',
		'description',
		'url',
		'text',
		'subtext',
        'alt'
	];

	public function language()
	{
		return $this->belongsTo(Language::class, 'language_id', 'id')->select('id', 'name', 'flag');
	}

	public function article()
	{
		return $this->belongsTo(Article::class, 'article_id', 'id');
	}
}
