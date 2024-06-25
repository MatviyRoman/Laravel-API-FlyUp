<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
	protected $fillable = [
		'name',
		'views',
		'likes',
		'image',
		'additional_images',
	];

	public function seos()
	{
		return $this->hasMany(Seo::class, 'page_id', 'id')->where('language_id', env('DEFAULT_LANG_ID', 1));
	}

	public function languages()
	{
		return $this->belongsToMany('App\Language', 'seos')->orderBy('order')->select('name', 'flag');
	}

	public function interfaceEntities()
	{
		return $this->belongsToMany(InterfaceEntity::class, 'page_interface_entities')->select('id', 'name');
		/*return $this->hasManyThrough(
			InterfaceTranslate::class,
			PageInterfaceEntity::class,
			'page_id',
			'interface_entity_id',
			'id',
			'id'
		);*/
	}
}
