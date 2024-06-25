<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TextTranslate extends Model
{
	protected $fillable = [
		'text_entity_id',
		'language_id',
		'value'
	];

	protected $text_entity_id;

	protected $language_id;

	protected $value;

	public function language()
	{
		return $this->belongsTo(Language::class, 'language_id', 'id');
	}

	public function text_entity()
	{
		return $this->belongsTo(TextEntity::class, 'text_entity_id', 'id');
	}
}
