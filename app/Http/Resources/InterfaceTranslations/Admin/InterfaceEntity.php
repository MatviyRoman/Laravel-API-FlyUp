<?php

namespace App\Http\Resources\InterfaceTranslations\Admin;

use App\Http\Resources\Language;
use App\Seo;
use App\InterfaceEntity as InterfaceEntityModel;
use Illuminate\Http\Resources\Json\Resource;

class InterfaceEntity extends Resource
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
		    'name' => $this->name,
		    'title' => $this->title,
		    'value' => $this->value,
//		    'page' => Seo::where('page_id', $this->page_id)
//			    ->where('language_id', env('DEFAULT_LANG_ID', 1))
//			    ->select('page_id as id', 'title')
//			    ->first(),
//		    'languages' => Language::collection(InterfaceEntityModel::find($this->id)->languages),
	    ];
    }
}
