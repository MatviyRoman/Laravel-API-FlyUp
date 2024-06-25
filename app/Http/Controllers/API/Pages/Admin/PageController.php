<?php

namespace App\Http\Controllers\API\Pages\Admin;


use App\Http\Resources\Pages\Admin\PageAll;
use App\Http\Resources\Pages\Admin\PageCollection;
use App\InterfaceTranslate;
use App\Language;
use App\Http\Controllers\MainController;
use App\Page;
use App\Seo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use DB;

class PageController extends Controller
{

	/**
	 * @SWG\Get(
	 *     path="/api/page",
	 *     tags={"Page"},
	 *     summary="Display a listing of pages",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="number",
	 *          description="number per page",
	 *          type="integer",
	 *          in="query"
	 *     ),
	 *     @SWG\Parameter(
	 *          name="page",
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
	 *          name="title",
	 *          description="title",
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
		if ($request->number && $request->number != 0)
			$number = $request->number;
		else
			$number = env('DEFAULT_NUMBER_PER_PAGE', 20);

		$pages = DB::table('pages')
			->join('seos', 'pages.id', '=', 'seos.page_id')
			->where('seos.language_id', env('DEFAULT_LANG_ID', 1));

		if ($request->has('title'))
			$pages->where('seos.title', 'LIKE', '%' . $request->title . '%');

		if ($request->has('method') && $request->has('field'))
			$pages->orderBy($request->field, $request->method);

		return new PageCollection(
			$pages->select('pages.id',
				'pages.name',
				'pages.image',
				'pages.additional_images',
				'seos.title',
				'seos.keywords',
				'seos.description',
				'seos.url'
			)->paginate($number)
		);
	}

	/**
	 * @SWG\Post(
	 *     path="/api/page",
	 *     tags={"Page"},
	 *     summary="Create page",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *     in="body",
	 *     name="body",
	 *     description="page parameters",
	 *     required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="name", type="string"),
	 *              @SWG\Property(property="url", type="string"),
	 *              @SWG\Property(property="title", type="string"),
	 *              @SWG\Property(property="seo_title", type="string"),
     *              @SWG\Property(property="alt", type="string"),
	 *              @SWG\Property(property="keywords", type="string"),
	 *              @SWG\Property(property="description", type="string"),
	 *              @SWG\Property(property="image", type="string"),
	 *              @SWG\Property(property="additional_images", type="string"),
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
		if (Seo::where('url', MainController::getUrl($request->url))->count()) {
			$validator = Validator::make(array_merge($request->all(), ['name' => MainController::getUrl($request->name ? $request->name : $request->title)]), [
				'name' => 'required|unique:pages|string|max:255',
				'url' => 'required|unique:seos|string|max:255',
				'title' => 'required|unique:seos|string|max:255',
				'seo_title' => 'required|unique:seos|string|max:255',
				'keywords' => 'required|string',
				'description' => 'required|string',
				'image' => 'required|string|max:255',
				'additional_images' => 'nullable|string',
			]);
		} else {
			$validator = Validator::make(array_merge($request->all(), ['name' => MainController::getUrl($request->name ? $request->name : $request->title)]), [
				'name' => 'required|unique:pages|string|max:255',
				'url' => 'nullable|unique:seos|string|max:255',
				'title' => 'required|unique:seos|string|max:255',
				'seo_title' => 'required|unique:seos|string|max:255',
				'keywords' => 'required|string',
				'description' => 'required|string',
				'image' => 'required|string|max:255',
                'additional_images' => 'nullable|string',
			]);
		}

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$page = Page::create([
			'name' => MainController::getUrl($request->name ? $request->name : $request->title),
			'image' => $request->image,
			'additional_images' => $request->additional_images,
		]);

		if (Seo::create(array_merge($request->all(),
			[
				'url' => MainController::getUrl($request->url),
				'language_id' => env('DEFAULT_LANG_ID', 1),
				'page_id' => $page->id
			]
		))
		)
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/page/{id}/edit",
	 *     tags={"Page"},
	 *     summary="Show the form for editing page",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="page id",
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
	public function edit(Request $request, $id)
	{
		$parameters = array_merge($request->all(), ['id' => $id]);
		$validator = Validator::make($parameters, [
			'id' => 'required|numeric|exists:pages,id',
			'language_id' => 'required|numeric|exists:languages,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		if (Seo::where('page_id', $id)
			->where('language_id', $request->language_id)
			->count()
		) {
			return new PageAll(
				Seo::where('page_id', $id)
					->where('language_id', $request->language_id)
					->with('page')
					->first()
			);
		} else {
			$page = Page::find($id);
			$data = [
				'id' => $id,
				'name' => $page->name,
				'image' => $page->image,
				'additional_images' => $page->additional_images,
				'title' => '',
				'seo_title' => '',
				'keywords' => '',
				'description' => '',
				'url' => ''
			];
			return [
				'data' => $data,
				'language' => Language::where('id', $request->language_id)->select('id', 'name', 'flag')->first(),
				'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get(),
			];
		}
	}

	/**
	 * @SWG\Put(
	 *     path="/api/page/{id}",
	 *     tags={"Page"},
	 *     summary="Update page in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="page id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="page parameters",
	 *          required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="language_id", type="integer"),
	 *              @SWG\Property(property="name", type="string"),
	 *              @SWG\Property(property="url", type="string"),
	 *              @SWG\Property(property="title", type="string"),
	 *              @SWG\Property(property="seo_title", type="string"),
     *              @SWG\Property(property="alt", type="string"),
	 *              @SWG\Property(property="keywords", type="string"),
	 *              @SWG\Property(property="description", type="string"),
	 *              @SWG\Property(property="image", type="string"),
	 *              @SWG\Property(property="additional_images", type="string"),
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
	public function update(Request $request, $id)
	{
		$validator = Validator::make(array_merge($request->all(), ['id' => $id]), [
			'id' => 'required|numeric|exists:pages,id',
			'language_id' => 'required|numeric|exists:languages,id',
			'name' => 'required|string|max:255|unique:pages,name,' . $id,
			'url' => 'nullable|string|max:255',
			'title' => 'required|string|max:255',
			'seo_title' => 'required|string|max:255',
			'keywords' => 'required|string',
			'description' => 'required|string',
			'image' => 'required|string|max:255',
            'additional_images' => 'nullable|string',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		if (Seo::where('url', MainController::getUrl($request->url))->where('page_id', '!=', $id)->count()) {
			$validator = Validator::make($request->all(), [
				'url' => 'unique:seos,url',
			]);

			if ($validator->fails())
				return response()->json(['errors' => $validator->errors()], 400);
		}

		$page = Page::find($id);

		if (!empty($request->image) && $page->image != $request->image) {
			$page->image = $request->image;
			$page->save();
			unset($request['image']);
		}
		if (!empty($request->additional_images) && $page->additional_images != $request->additional_images) {
			$page->additional_images = $request->additional_images;
			$page->save();
			unset($request['additional_images']);
		}
		if (!empty($request->name) && $page->name != $request->name) {
			$page->name = $request->name;
			$page->save();
			unset($request['name']);
		}

		if (Seo::updateOrCreate(
			['language_id' => $request->language_id, 'page_id' => $id],
			array_merge(
				$request->all(),
				['url' => MainController::getUrl($request->url)]
			)
		)
		)
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Put(
	 *     path="/api/page/{id}/field",
	 *     tags={"Page"},
	 *     summary="Update page field in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="page id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="page field (name, image) and his new value",
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
	public function updateField(Request $request, $id)
	{
		if (Page::find($id)->update([$request->field => $request->value]))
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/page/search",
	 *     tags={"Page"},
	 *     summary="Search page",
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
	public function search(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'value' => 'required|string|max:255',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$result = Seo::where('language_id', env('DEFAULT_LANG_ID', 1))
			->where('title', 'LIKE', '%' . $request->value . '%')
			->select('page_id as id', 'title')
			->orderBy('id', 'desc')
			->take(10)
			->get();

		if (!$result->count())
			$result = InterfaceTranslate::getTranslate(52, env('DEFAULT_LANG_ID', 1));

		return response($result, 200);
	}

	/**
	 * @SWG\Delete(
	 *     path="/api/page/{id}",
	 *     tags={"Page"},
	 *     summary="Remove page from storage",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="page id",
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
		return Page::destroy($id);
	}
}
