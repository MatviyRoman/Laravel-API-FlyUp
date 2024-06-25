<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
	protected $hidden = ['pivot'];

	public function interfaceTranslations()
	{
		return $this->hasMany(InterfaceTranslate::class, 'language_id', 'id');
	}

	public function seos()
	{
		return $this->hasMany(Seo::class, 'language_id', 'id');
	}
}
