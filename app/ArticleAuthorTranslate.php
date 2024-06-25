<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleAuthorTranslate extends Model
{
	protected $fillable = [
		'article_author_id',
		'language_id',
		'name',
	];

	public function language()
	{
		return $this->belongsTo(Language::class, 'language_id', 'id');
	}

	public function articleAuthor()
	{
		return $this->belongsTo(ArticleAuthor::class, 'article_author_id', 'id');
	}
}
