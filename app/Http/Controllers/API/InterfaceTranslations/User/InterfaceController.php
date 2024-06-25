<?php

namespace App\Http\Controllers\API\InterfaceTranslations\User;

use App\Language;
use App\Seo;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Support\Facades\Cache;
use Validator;
use DB;

class InterfaceController extends Controller
{

	/**
	 * @SWG\Get(
	 *     path="/api/user/interface/page/{language}/{url}",
	 *     tags={"User Interface"},
	 *     summary="Display a listing of page interface entities and languages",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="language",
	 *          description="language name",
	 *          type="string",
	 *          required=true,
	 *          in="path",
	 *     ),
	 *     @SWG\Parameter(
	 *          name="url",
	 *          description="page url",
	 *          type="string",
	 *          in="path"
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=401, description="Unauthenticated"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 * )
	 */
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($language, $url = null)
	{
		if ($url == '0')
			$validator = Validator::make(['language' => $language], [
				'language' => 'required|string|exists:languages,name',
			]);
		else
			$validator = Validator::make(['language' => $language, 'url' => $url], [
				'language' => 'required|string|exists:languages,name',
				'url' => 'nullable|string|exists:seos,url',
			]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$language_id = Language::where('name', $language)->first()->id;
		$page = $url == '0' ? null : Seo::where('url', $url)->where('language_id', $language_id)->select('page_id')->first()->page_id;

		$interface_groups = Cache::remember('interface_groups_index_' . $page . '_' . $language_id, 360, function () use ($page, $language_id) {
			$groups = DB::table('interface_groups')
				->where('interface_groups.page_id', $page)
				->select('id', 'name')
				->get();
			$interface_groups['languages'] = Language::orderBy('order')
				->select('id', 'name', 'flag')
				->get();
			if ($page == null)
				$interface_groups['all_views'] = Setting::find(1)
					->all_views;
			if ($groups->count())
				foreach ($groups as $key => $group) {
					$interface_groups[$group->name] = DB::table('interface_entities')
						->where('interface_entities.interface_group_id', $group->id)
						->join('interface_translates', 'interface_entities.id', '=', 'interface_translates.interface_entity_id')
						->where('interface_translates.language_id', $language_id)
						->select('interface_entities.name', 'interface_translates.value')
						->pluck('value', 'name');
				}
			return $interface_groups;
		});

		return response($interface_groups, 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/user/interface/{language_id}/{name}",
	 *     tags={"User Interface"},
	 *     summary="Show the interface entity translation",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="language_id",
	 *          description="language id",
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          name="name",
	 *          description="interface entity name",
	 *          type="string",
	 *          in="path"
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 * )
	 */

	public function show($language_id, $name)
	{
		$validator = Validator::make(['language_id' => $language_id, 'name' => $name], [
			'language_id' => 'required|numeric|exists:languages,id',
			'name' => 'required|exists:interface_entities,name',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$result = DB::table('interface_entities')
			->where('interface_entities.name', $name)
			->join('interface_translates', 'interface_entities.id', '=', 'interface_translates.interface_entity_id')
			->where('interface_translates.language_id', $language_id)
			->select('value')
			->first();

		if ($result)
			return response($result->value, 200);
		else
			abort(404);
	}
}
