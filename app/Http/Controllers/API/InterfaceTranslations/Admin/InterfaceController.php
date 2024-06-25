<?php

namespace App\Http\Controllers\API\InterfaceTranslations\Admin;


use App\Http\Resources\InterfaceTranslations\Admin\InterfaceEntityAll;
use App\Http\Resources\InterfaceTranslations\Admin\InterfaceEntityCollection;
use App\InterfaceEntity;
use App\InterfaceTranslate;
use App\Language;
use App\InterfaceGroup;
use App\Seo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Validator;
use DB;
use App\Http\Controllers\MainController;

class InterfaceController extends Controller
{

	/**
	 * @SWG\Get(
	 *     path="/api/interface",
	 *     tags={"Interface"},
	 *     summary="Display a listing of interface entities",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="number",
	 *          description="number per page",
	 *          type="integer",
	 *          in="query"
	 *     ),
	 *     @SWG\Parameter(
	 *          name="Interface",
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

		$interface = DB::table('interface_entities')
			->join('interface_translates', 'interface_entities.id', '=', 'interface_translates.interface_entity_id')
            ->where('interface_translates.language_id', $request->language_id)
            ->where('interface_entities.interface_group_id', $request->interface_group_id)
            ->select('interface_entities.id',
                'interface_entities.name',
                'interface_entities.title',
                'interface_translates.value')
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
	 *     path="/api/interface",
	 *     tags={"Interface"},
	 *     summary="Create interface entity",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *     in="body",
	 *     name="body",
	 *     description="interface entity parameters",
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

		$interfaceEntity = InterfaceEntity::create([
			'name' => MainController::getUrl($request->title),
			'title' => $request->title,
			'interface_group_id' => $request->interface_group_id
		]);

        $languages = Language::select('id')->get();
        $translations = [];
        foreach ($languages as $language) {
            array_push($translations, [
                'language_id' => $language->id,
                'interface_entity_id' => $interfaceEntity->id,
                'value' => $language->id == $request->language_id ? $request->value : ''
            ]);
        }

		if (InterfaceTranslate::insert($translations))
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/interface/{id}/edit",
	 *     tags={"Interface"},
	 *     summary="Show the form for editing interface entity",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="interface entity id",
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
//			'id' => 'required|numeric|exists:interface_entities,id',
//			'language_id' => 'required|numeric|exists:languages,id',
//		]);
//
//		if ($validator->fails())
//			return response()->json(['errors' => $validator->errors()], 400);
//
//		if (InterfaceTranslate::where('interface_entity_id', $id)
//			->where('language_id', $request->language_id)
//			->count()
//		) {
//			return new InterfaceEntityAll(
//				InterfaceTranslate::where('interface_entity_id', $id)
//					->where('language_id', $request->language_id)
//					->with('interface_entity')
//					->first()
//			);
//		} else {
//			$interfaceEntity = InterfaceEntity::find($id);
//			$data = [
//				'id' => $id,
//				'name' => $interfaceEntity->name,
//				'value' => '',
//			];
//			return [
//				'data' => $data,
//				'language' => Language::where('id', $request->language_id)->select('id', 'name', 'flag')->first(),
//				'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get(),
//				'page' => Seo::where('page_id', $interfaceEntity->page_id)
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
	 *     path="/api/interface/{id}",
	 *     tags={"Interface"},
	 *     summary="Update interface entity in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="interface entity id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="interface entity parameters",
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
//			'id' => 'required|numeric|exists:interface_entities,id',
//			'page_id' => 'numeric|exists:pages,id',
//			'name' => 'required|string|max:255|unique:interface_entities,name,' . $id,
//			'value' => 'required|string',
//		]);
//
//		if ($validator->fails())
//			return response()->json(['errors' => $validator->errors()], 400);
//
//		$interfaceEntity = InterfaceEntity::find($id);
//
//		if (!empty($request->name) && $interfaceEntity->name != $request->name) {
//			$interfaceEntity->name = $request->name;
//			$interfaceEntity->save();
//			unset($request['name']);
//		}
//		if (!empty($request->page_id) && $interfaceEntity->page_id != $request->page_id) {
//			$interfaceEntity->page_id = $request->page_id;
//			$interfaceEntity->save();
//			unset($request['page_id']);
//		}
//
//		if (InterfaceTranslate::updateOrCreate(['language_id' => $request->language_id, 'interface_entity_id' => $id], $request->all()))
//			return response('Successful operation', 200);
//	}

	/**
	 * @SWG\Put(
	 *     path="/api/interface/update",
	 *     tags={"Interface"},
	 *     summary="List of new interface translations parameters",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="List of interface translations parameters",
	 *          required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="language_id", type="integer"),
	 *              @SWG\Property(property="translations", type="array",
	 *                  @SWG\Items(
	 *                      type="object",
	 *                      @SWG\Property(property="interface_entity_id", type="integer"),
	 *                      @SWG\Property(property="value", type="string"),
	 *          )
	 *              )
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
	public function updateFields(Request $request)
	{
		$validator = Validator::make(['language_id' => $request->language_id, 'translations' => $request->translations], [
			'language_id' => 'required|numeric|exists:languages,id',
			'translations' => 'required',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		Cache::clear();

		foreach($request->translations as $translation) {
			InterfaceTranslate::updateOrCreate(
				['interface_entity_id' => $translation['interface_entity_id'], 'language_id' => $request->language_id],
				['value' => $translation['value']]
			);
        }
	}

	/**
	 * @SWG\Get(
	 *     path="/api/interface/search",
	 *     tags={"Interface"},
	 *     summary="Search interface entity",
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
//		$result = InterfaceTranslate::where('language_id', env('DEFAULT_LANG_ID', 1))
//			->where('value', 'LIKE', '%' . $request->value . '%')
//			->select('interface_entity_id as id', 'value')
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
	 *     path="/api/interface/{id}",
	 *     tags={"Interface"},
	 *     summary="Remove interface entity from storage",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="interface entity id",
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
		return InterfaceEntity::destroy($id);
	}
}
