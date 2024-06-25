<?php

namespace App\Http\Controllers\API\TextTranslations\Admin;

use App\Http\Resources\TextTranslations\Admin\TextEntityAll;
use App\Http\Resources\TextTranslations\Admin\TextEntityCollection;
use App\InterfaceTranslate;
use App\TextEntity;
use App\TextTranslate;
use App\InterfaceGroup;
use App\Language;
use App\Seo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Validator;
use DB;
use App\Http\Controllers\MainController;

class TextController extends Controller
{

	/**
	 * @SWG\Get(
	 *     path="/api/text",
	 *     tags={"Text"},
	 *     summary="Display a listing of text entities",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="number",
	 *          description="number per page",
	 *          type="integer",
	 *          in="query"
	 *     ),
	 *     @SWG\Parameter(
	 *          name="Text",
	 *          description="page number",
	 *          type="integer",
	 *          in="query"
	 *     ),
	 *     @SWG\Parameter(
	 *          name="method",
	 *          description="sort method",
	 *          type="string",
	 *          in="query",
	 *          enum={"asc", "desc"}
	 *     ),
	 *     @SWG\Parameter(
	 *          name="field",
	 *          description="sort field",
	 *          type="string",
	 *          in="query"
	 *     ),
	 *     @SWG\Parameter(
	 *          name="page_id",
	 *          description="page id",
	 *          type="integer",
	 *          in="query"
	 *     ),
	 *     @SWG\Parameter(
	 *          name="name",
	 *          description="name",
	 *          type="string",
	 *          in="query"
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=401, description="Unauthenticated"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 *     security={
	 *       {"Bearer": {}}
	 *     }
	 * )
	 */
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
        $validator = Validator::make($request->all(), [
            'language_id' => 'required|numeric|exists:languages,id',
            'interface_group_id' => 'required|numeric|exists:interface_groups,id',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        $interface = DB::table('text_entities')
            ->join('text_translates', 'text_entities.id', '=', 'text_translates.text_entity_id')
            ->where('text_translates.language_id', $request->language_id)
            ->where('text_entities.interface_group_id', $request->interface_group_id)
            ->select('text_entities.id',
                'text_entities.name',
                'text_entities.title',
                'text_translates.value')
            ->orderBy('id', 'desc')->get();


        return [
            'data' => $interface,
            'interface_group' => InterfaceGroup::where('id', $request->interface_group_id)->select('id', 'name', 'title')->first(),
            'language' => Language::where('id', $request->language_id)->select('id', 'name', 'flag')->first(),
            'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get()
        ];
	}

	/**
	 * @SWG\Post(
	 *     path="/api/text",
	 *     tags={"Text"},
	 *     summary="Create text entity",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *     in="body",
	 *     name="body",
	 *     description="Text entity parameters",
	 *     required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="page_id", type="integer"),
	 *              @SWG\Property(property="name", type="string"),
	 *              @SWG\Property(property="value", type="string"),
	 *          )
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=401, description="Unauthenticated"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 *     security={
	 *       {"Bearer": {}}
	 *     }
	 * )
	 */

	public function store(Request $request)
	{
        $validator = Validator::make($request->all(), [
            'interface_group_id' => 'required|numeric|exists:interface_groups,id',
//			'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'value' => 'required|string',
            'language_id' => 'required|numeric|exists:languages,id',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

		Cache::clear();

        $interfaceEntity = TextEntity::create([
            'name' => MainController::getUrl($request->title),
            'title' => $request->title,
            'interface_group_id' => $request->interface_group_id
        ]);

        $languages = Language::select('id')->get();
        $translations = [];
        foreach ($languages as $language) {
            array_push($translations, [
                'language_id' => $language->id,
                'text_entity_id' => $interfaceEntity->id,
                'value' => $language->id == $request->language_id ? $request->value : ''
            ]);
        }

        if (TextTranslate::insert($translations))
            return response('Successful operation', 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/text/{id}/edit",
	 *     tags={"Text"},
	 *     summary="Show the form for editing text entity",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Text entity id",
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          name="language_id",
	 *          description="Language id",
	 *          type="integer",
	 *          in="query"
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=401, description="Unauthenticated"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 *     security={
	 *       {"Bearer": {}}
	 *     }
	 * )
	 */
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
//	public function edit(Request $request, $id)
//	{
//		$parameters = array_merge($request->all(), ['id' => $id]);
//		$validator = Validator::make($parameters, [
//			'id' => 'required|numeric|exists:text_entities,id',
//			'language_id' => 'required|numeric|exists:languages,id',
//		]);
//
//		if ($validator->fails())
//			return response()->json(['errors' => $validator->errors()], 400);
//
//		if (TextTranslate::where('text_entity_id', $id)
//			->where('language_id', $request->language_id)
//			->count()
//		) {
//			return new TextEntityAll(
//				TextTranslate::where('text_entity_id', $id)
//					->where('language_id', $request->language_id)
//					->with('text_entity')
//					->first()
//			);
//		} else {
//			$textEntity = TextEntity::find($id);
//			$data = [
//				'id' => $id,
//				'name' => $textEntity->name,
//				'value' => '',
//			];
//			return [
//				'data' => $data,
//				'language' => Language::where('id', $request->language_id)->select('id', 'name', 'flag')->first(),
//				'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get(),
//				'page' => Seo::where('page_id', $textEntity->page_id)
//					->where('language_id', env('DEFAULT_LANG_ID', 1))
//					->select('page_id as id', 'title')
//					->first(),
//				'pages' => Seo::where('language_id', env('DEFAULT_LANG_ID', 1))
//					->select('page_id as id', 'title')
//					->get(),
//			];
//		}
//	}

	/**
	 * @SWG\Put(
	 *     path="/api/text/{id}",
	 *     tags={"Text"},
	 *     summary="Update text entity in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Text entity id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="Text entity parameters",
	 *          required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="language_id", type="integer"),
	 *              @SWG\Property(property="page_id", type="integer"),
	 *              @SWG\Property(property="name", type="string"),
	 *              @SWG\Property(property="value", type="string"),
	 *          ),
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=401, description="Unauthenticated"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 *     security={
	 *       {"Bearer": {}}
	 *     }
	 * )
	 */
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
//	public function update(Request $request, $id)
//	{
//		$parameters = array_merge($request->all(), ['id' => $id]);
//		$validator = Validator::make($parameters, [
//			'id' => 'required|numeric|exists:text_entities,id',
//			'page_id' => 'numeric|exists:pages,id',
//			'name' => 'required|string|max:255|unique:text_entities,name,' . $id,
//			'value' => 'required|string',
//		]);
//
//		if ($validator->fails())
//			return response()->json(['errors' => $validator->errors()], 400);
//
//		$textEntity = TextEntity::find($id);
//
//		if (!empty($request->name) && $textEntity->name != $request->name) {
//			$textEntity->name = $request->name;
//			$textEntity->save();
//			unset($request['name']);
//		}
//		if (!empty($request->page_id) && $textEntity->page_id != $request->page_id) {
//			$textEntity->page_id = $request->page_id;
//			$textEntity->save();
//			unset($request['page_id']);
//		}
//
//		if (TextTranslate::updateOrCreate(['language_id' => $request->language_id, 'text_entity_id' => $id], $request->all()))
//			return response('Successful operation', 200);
//	}

	/**
	 * @SWG\Put(
	 *     path="/api/text/{id}/field",
	 *     tags={"Text"},
	 *     summary="Update text entity field in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Text entity id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="Text entity field (name) and his new value",
	 *          required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="field", type="string"),
	 *              @SWG\Property(property="value", type="string")
	 *          )
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=401, description="Unauthenticated"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 *     security={
	 *       {"Bearer": {}}
	 *     }
	 * )
	 */
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
    public function updateTexts(Request $request)
    {
        //die(var_dump($request->language_id));
        $validator = Validator::make(['language_id' => $request->language_id, 'translations' => $request->translations], [
            'language_id' => 'required|numeric|exists:languages,id',
            'translations' => 'required',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

	    Cache::clear();

        foreach($request->translations as $translation) {
            TextTranslate::updateOrCreate(
                ['text_entity_id' => $translation['text_entity_id'], 'language_id' => $request->language_id],
                ['value' => $translation['value']]
            );
        }
    }
	/**
	 * @SWG\Get(
	 *     path="/api/text/search",
	 *     tags={"Text"},
	 *     summary="Search text entity",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="value",
	 *          type="string",
	 *          in="query"
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=401, description="Unauthenticated"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 *     security={
	 *       {"Bearer": {}}
	 *     }
	 * )
	 */
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
//	public function search(Request $request)
//	{
//		$validator = Validator::make($request->all(), [
//			'value' => 'required|string|max:255',
//		]);
//
//		if ($validator->fails())
//			return response()->json(['errors' => $validator->errors()], 400);
//
//		$result = TextTranslate::where('language_id', env('DEFAULT_LANG_ID', 1))
//			->where('value', 'LIKE', '%' . $request->value . '%')
//			->select('text_entity_id as id', 'value')
//			->orderBy('id', 'desc')
//			->take(10)
//			->get();
//
//		if (!$result->count())
//			$result = InterfaceTranslate::getTranslate(52, env('DEFAULT_LANG_ID', 1));
//
//		return response($result, 200);
//	}

	/**
	 * @SWG\Delete(
	 *     path="/api/text/{id}",
	 *     tags={"Text"},
	 *     summary="Remove text entity from storage",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Text entity id",
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=401, description="Unauthenticated"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 *     security={
	 *       {"Bearer": {}}
	 *     }
	 * )
	 */
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		return TextEntity::destroy($id);
	}
}
