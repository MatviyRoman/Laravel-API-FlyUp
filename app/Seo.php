<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
	protected $fillable = [
		'page_id',
		'language_id',
		'title',
		'seo_title',
		'description',
		'keywords',
		'url',
	];

	public function page()
	{
		return $this->belongsTo(Page::class, 'page_id', 'id');
	}

	public function language()
	{
		return $this->belongsTo(Language::class, 'language_id', 'id');
	}
}
