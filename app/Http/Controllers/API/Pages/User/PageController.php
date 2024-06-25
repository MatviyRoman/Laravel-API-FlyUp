<?php

namespace App\Http\Controllers\API\Pages\User;

use App\Http\Controllers\MainController;
use App\Http\Resources\Pages\User\PageAll;
use App\Http\Resources\Pages\User\PageCollection;
use App\Language;
use App\Page;
use App\Seo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
	/**
	 * @SWG\Get(
	 *     path="/api/user/page",
	 *     tags={"User Page"},
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
	 *          name="language_id",
	 *          description="language id",
	 *          type="integer",
	 *          required=true,
	 *          in="query",
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
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
			'language_id' => 'required|numeric|exists:languages,id'
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		App::setLocale(Language::find($request->language_id)->name);

		if ($request->number && $request->number != 0)
			$number = $request->number;
		else
			$number = env('DEFAULT_NUMBER_PER_PAGE', 20);

		$pages = DB::table('pages')
			->join('seos', 'pages.id', '=', 'seos.page_id')
			->where('seos.language_id', $request->language_id)
			->select('pages.id', 'pages.name', 'seos.url');

		if($request->has('method') && $request->has('field'))
			$pages->orderBy($request->field, $request->method);

		return new PageCollection($pages->paginate($number));
	}

	/**
	 * @SWG\Get(
	 *     path="/api/user/page/{language}/{use_case}/{url}",
	 *     tags={"User Page"},
	 *     summary="Show the page",
	 *     produces= {"application/json"},
	 *     consumes={"application/x-www-form-urlencoded"},
	 *     @SWG\Parameter(
	 *          name="language",
	 *          description="language name",
	 *          type="string",
	 *          required=true,
	 *          in="path",
	 *     ),
	 *     @SWG\Parameter(
	 *          name="use_case",
	 *          type="string",
	 *          in="path",
	 *          enum={"view", "content", "view_content"}
	 *     ),
	 *     @SWG\Parameter(
	 *          name="url",
	 *          type="string",
	 *          in="path",
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 * )
	 */

	public function show($language, $use_case, $url = null)
	{
		$validator = Validator::make(['language' => $language, 'url' => $url], [
			'language' => 'required|string|exists:languages,name',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$language_id = Language::where('name', $language)->first()->id;
		if (!Seo::where('language_id', $language_id)->where('url', $url)->count())
			abort(404);

		$page = DB::table('pages')
			->join('seos', 'pages.id', '=', 'seos.page_id')
			->where('seos.language_id', $language_id)
			->where('seos.url', $url)
			->select('pages.id', 'pages.image', 'pages.additional_images', 'pages.views', 'pages.name', 'seos.url', 'seos.title', 'seos.seo_title', 'seos.keywords', 'seos.description', 'seos.language_id')
			->first();

		if($page) {
			switch ($use_case) {
				case 'view':
					MainController::sessionIncrement(Page::find($page->id));
					return response('view', 200);
				case 'content':
					return new PageAll($page);
			}
			MainController::sessionIncrement(Page::find($page->id));
			return new PageAll($page);
		} else {
			abort(404);
		}
	}
}
