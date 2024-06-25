<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Language;

class InterfaceTranslate extends Model
{
	protected $fillable = [
		'interface_entity_id',
		'language_id',
		'value'
	];

	protected $interface_entity_id;

	protected $language_id;

	protected $value;

	public function language()
	{
		return $this->belongsTo(Language::class, 'language_id', 'id');
	}

	public function interface_entity()
	{
		return $this->belongsTo(InterfaceEntity::class, 'interface_entity_id', 'id');
	}

	public static function getTranslate($id, $language_id = 1)
	{
		$result = InterfaceTranslate::where('language_id', $language_id)->where('interface_entity_id', $id)->first() ?
			InterfaceTranslate::where('language_id', $language_id)->where('interface_entity_id', $id)->select('value')->first() :
			InterfaceTranslate::where('language_id', env('DEFAULT_LANG_ID', 1))->where('interface_entity_id', $id)->select('value')->first();
		return $result['value'];
	}

	public static function getAll($lang)
	{
		return InterfaceTranslate::where('language_id', $lang)->select('interface_entity_id', 'language_id', 'value')->get();
	}
}
