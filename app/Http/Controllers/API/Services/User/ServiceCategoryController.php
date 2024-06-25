<?php

namespace App\Http\Controllers\API\Services\User;

use App\Http\Resources\ServiceCategories\User\ServiceCategory;
use App\Http\Resources\ServiceCategories\User\ServiceCategoryAll;
use App\Http\Resources\ServiceCategories\User\ServiceCategoryCollection;
use App\InterfaceTranslate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServiceCategoryController extends Controller
{
	/**
	 * @SWG\Get(
	 *     path="/api/user/service/category",
	 *     tags={"User Service"},
	 *     summary="Display a listing of article categories",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
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
	 * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
	public function index(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'language_id' => 'required|numeric|exists:languages,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$categories = DB::table('service_categories')
			->where('service_categories.is_active', 1)
			->join('service_category_translates', 'service_categories.id', '=', 'service_category_translates.service_category_id')
			->where('service_category_translates.language_id', $request->language_id)
			->orderBy('service_categories.order')
			->select('service_categories.id','service_categories.image', 'service_categories.seo_image', 'service_category_translates.title', 'service_category_translates.url', 'service_category_translates.subtext')
			->get();

		return ServiceCategory::collection($categories);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/user/service/{language_id}/{category_url}/",
	 *     tags={"User Service"},
	 *     summary="Show the service category",
	 *     produces= {"application/json"},
	 *     consumes={"application/x-www-form-urlencoded"},
	 *     @SWG\Parameter(
	 *          name="language_id",
	 *          type="integer",
	 *          in="path",
	 *     ),
	 *     @SWG\Parameter(
	 *          name="category_url",
	 *          type="string",
	 *          in="path",
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 * )
	 */

	public function show($language_id, $category_url)
	{
		$validator = Validator::make(['language_id' => $language_id, 'category_url' => $category_url], [
			'language_id' => 'required|numeric|exists:languages,id',
			'category_url' => 'required|string|exists:service_category_translates,url',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$articleCategory = DB::table('service_categories')
			->where('service_categories.is_active', 1)
			->join('service_category_translates', 'service_categories.id', '=', 'service_category_translates.service_category_id')
			->where('service_category_translates.language_id', $language_id)
			->where('service_category_translates.url', $category_url)
			->select('service_categories.id', 'title', 'seo_title', 'keywords', 'description', 'image', 'seo_image', 'text', 'subtext', 'alt')
			->first();

		if($articleCategory)
			return new ServiceCategoryAll($articleCategory);
		else
			abort(404);
	}
}
