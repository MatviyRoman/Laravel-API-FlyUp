<?php

namespace App\Http\Resources\Pages\User;

use App\Seo;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PageAll extends Resource
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
			'seo_image' => $this->image,
			'additional_images' => $this->additional_images,
			'views' => $this->views,
			'title' => $this->title,
			'seo_title' => $this->seo_title,
			'keywords' => $this->keywords,
			'description' => $this->description,
			'url' => $this->url,
		];
	}


	/**
	 * Get additional data that should be returned with the resource array.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return array
	 */
	public function with($request)
	{
		return [
			'languages' => Seo::where('page_id', $this->id)
				->join('pages', 'seos.page_id', '=', 'pages.id')
				->join('languages', 'seos.language_id', '=', 'languages.id')
				->orderBy('languages.id')
				->select('languages.id', 'languages.name', 'languages.flag', 'seos.url')
				->get(),
			'interface_groups' => Cache::remember('interface_groups_' . $this->id . '_' . $this->language_id, 360, function () {
				$groups = DB::table('interface_groups')
					->where('interface_groups.page_id', $this->id)
					->select('id', 'name')
					->get();
				if (!$groups->count()) {
					return [];
				} else {
					foreach ($groups as $key => $group) {
						$interface_groups[$group->name]['interface'] = DB::table('interface_entities')
							->where('interface_entities.interface_group_id', $group->id)
							->join('interface_translates', 'interface_entities.id', '=', 'interface_translates.interface_entity_id')
							->where('interface_translates.language_id', $this->language_id)
							->select('interface_entities.name', 'interface_translates.value')
							->pluck('value', 'name');
						$interface_groups[$group->name]['text'] = DB::table('text_entities')
							->where('text_entities.interface_group_id', $group->id)
							->join('text_translates', 'text_entities.id', '=', 'text_translates.text_entity_id')
							->where('text_translates.language_id', $this->language_id)
							->select('text_entities.name', 'text_translates.value')
							->pluck('value', 'name');
					}
					return $interface_groups;
				}
			}),
		];
	}
}
