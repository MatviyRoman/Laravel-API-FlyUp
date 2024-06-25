<?php

namespace App\Http\Controllers\API\TextTranslations\User;

use App\Http\Resources\TextTranslations\User\TextEntityCollection;
use App\Seo;
use App\Http\Controllers\Controller;
use Validator;
use DB;

class TextController extends Controller
{

	/**
	 * @SWG\Get(
	 *     path="/api/user/text/page/{language_id}/{url}",
	 *     tags={"User Text"},
	 *     summary="Display a listing of page text entities",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="language_id",
	 *          description="language id",
	 *          type="integer",
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
	public function index($language_id, $url = null)
	{
		if($url == '0')
			$validator = Validator::make(['language_id' => $language_id], [
				'language_id' => 'required|numeric|exists:languages,id',
			]);
		else
			$validator = Validator::make(['language_id' => $language_id, 'url' => $url], [
				'language_id' => 'required|numeric|exists:languages,id',
				'url' => 'nullable|string|exists:seos,url',
			]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$page = $url == '0' ? null : Seo::where('url', $url)->where('language_id', $language_id)->select('page_id')->first()->page_id;

		$text = DB::table('text_entities')
			->where('text_entities.page_id', $page)
			->join('text_translates', 'text_entities.id', '=', 'text_translates.text_entity_id')
			->where('text_translates.language_id', $language_id);

		return new TextEntityCollection(
			$text->select('text_entities.id',
				'text_entities.name',
				'text_translates.value'
			)->get()
		);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/user/text/{language_id}/{name}",
	 *     tags={"User Text"},
	 *     summary="Show the text entity translation",
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
	 *          description="text entity name",
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
			'name' => 'required|exists:text_entities,name',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$result = DB::table('text_entities')
			->where('text_entities.name', $name)
			->join('text_translates', 'text_entities.id', '=', 'text_translates.text_entity_id')
			->where('text_translates.language_id', $language_id)
			->select('value')
			->first();

		if($result)
			return response($result->value, 200);
		else
			abort(404);
	}
}
