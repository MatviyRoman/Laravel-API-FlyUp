<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceTranslate extends Model
{
	protected $fillable = [
		'service_id',
		'language_id',
		'title',
		'seo_title',
		'keywords',
		'description',
		'url',
        'alt',
		'text',
		'subtext',
	];

	public function language()
	{
		return $this->belongsTo(Language::class, 'language_id', 'id')->select('id', 'name', 'flag');
	}

	public function article()
	{
		return $this->belongsTo(Service::class, 'service_id', 'id');
	}
}
