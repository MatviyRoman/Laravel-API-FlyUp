<?php

namespace App\Http\Controllers\API\Articles\User;

use App\Http\Resources\Articles\User\ArticleCategoryAll;
use App\Http\Resources\Articles\User\ArticleCategoryCollection;
use App\InterfaceTranslate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArticleCategoryController extends Controller
{
	/**
	 * @SWG\Get(
	 *     path="/api/user/article/category",
	 *     tags={"User Article"},
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
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'language_id' => 'required|numeric|exists:languages,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$categories = DB::table('article_categories')
			->where('article_categories.is_active', 1)
			->where('article_categories.article_category_id', null)
			->join('article_category_translates', 'article_categories.id', '=', 'article_category_translates.article_category_id')
			->where('article_category_translates.language_id', $request->language_id)
			->orderBy('article_categories.order')
			->select('article_categories.id', 'article_category_translates.title as name', 'article_categories.icon', 'article_category_translates.url')
			->get();

		foreach ($categories as $key => $category) {
			$result['article'][$key] = (array) $category;
			$subcategories = DB::table('article_categories')
				->where('article_categories.is_active', 1)
				->where('article_categories.article_category_id', $category->id)
				->join('article_category_translates', 'article_categories.id', '=', 'article_category_translates.article_category_id')
				->where('article_category_translates.language_id', $request->language_id)
				->orderBy('article_categories.order')
				->select('article_categories.id', 'article_category_translates.title as name', 'article_category_translates.url')
				->get();

			$result['article'][$key]['icon'] = file_exists($result['article'][$key]['icon']) ? utf8_encode(file_get_contents($result['article'][$key]['icon'])) : '';

			if ($subcategories->count()) {
				$result['article'][$key]['subcategories'] = $subcategories;
				unset($result['article'][$key]['id']);
				unset($result['article'][$key]['url']);
			}
		}

		$result['service'][0]['name'] = InterfaceTranslate::getTranslate(3, $request->language_id);
		$result['service'][0]['icon'] = '';

		return response($result, 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/user/article/{language_id}/{category_url}/",
	 *     tags={"User Article"},
	 *     summary="Show the article category",
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
			'category_url' => 'required|string|exists:article_category_translates,url',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$articleCategory = DB::table('article_categories')
			->where('article_categories.is_active', 1)
			->join('article_category_translates', 'article_categories.id', '=', 'article_category_translates.article_category_id')
			->where('article_category_translates.language_id', $language_id)
			->where('article_category_translates.url', $category_url)
			->select('article_categories.id', 'title', 'seo_title', 'keywords', 'description', 'image', 'seo_image')
			->first();

		if($articleCategory)
			return new ArticleCategoryAll($articleCategory);
		else
			abort(404);
	}
}
