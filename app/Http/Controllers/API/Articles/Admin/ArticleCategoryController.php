<?php

namespace App\Http\Controllers\API\Articles\Admin;

use DB;
use App\ArticleCategory;
use App\ArticleCategoryTranslate;
use App\Http\Resources\ArticleCategoryAll;
use App\Http\Resources\ArticleCategoryCollection;
use App\Language;
use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class ArticleCategoryController extends Controller
{
	/**
	 * @SWG\Get(
	 *     path="/api/articleCategory",
	 *     tags={"Article Category"},
	 *     summary="Display a listing of article categories",
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
	 *          in="query",
	 *          enum={"id", "title", "is_active", "order"}
	 *     ),
	 *     @SWG\Parameter(
	 *          name="use_case",
	 *          description="use case",
	 *          type="string",
	 *          in="query",
	 *          enum={"for category", "for article", "main categories"}
	 *     ),
	 *     @SWG\Parameter(
	 *          name="article_category_id",
	 *          description="category id",
	 *          type="integer",
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

		$articleCategories = DB::table('article_categories')
			->join('article_category_translates', 'article_categories.id', '=', 'article_category_translates.article_category_id')
			->where('article_category_translates.language_id', env('DEFAULT_LANG_ID', 1));

		if ($request->has('use_case')) {
			switch ($request->use_case) {
				case 'for category':
					$articleCategories
						->where('article_categories.article_category_id', null)
						->where('article_categories.has_articles', false);
					break;
				case 'for article':
					$articleCategories->where('article_categories.is_last', true);
					break;
				case 'main categories':
					$articleCategories->where('article_categories.article_category_id', null);
					break;
			}
		}

		if ($request->has('article_category_id'))
			$articleCategories->where('article_categories.article_category_id', $request->article_category_id);

		if ($request->has('title'))
			$articleCategories->where('article_category_translates.title', 'LIKE', '%' . $request->title . '%');

		if ($request->has('method') && $request->has('field')) {
			$articleCategories->orderBy($request->field, $request->method);
		} else {
			$articleCategories->orderBy('order');
		}

		return new ArticleCategoryCollection(
			$articleCategories->select('article_categories.id', 'article_categories.order', 'article_categories.icon', 'article_categories.is_active', 'article_category_translates.title', 'article_category_translates.url', 'article_categories.article_category_id')
				->paginate($number)
		);
	}

	/**
	 * @SWG\Post(
	 *     path="/api/articleCategory",
	 *     tags={"Article Category"},
	 *     summary="Create article category",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *     in="body",
	 *     name="body",
	 *     description="Article category parameters",
	 *     required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="article_category_id", type="integer"),
	 *              @SWG\Property(property="image", type="string"),
	 *              @SWG\Property(property="seo_image", type="string"),
	 *              @SWG\Property(property="icon", type="string"),
	 *              @SWG\Property(property="url", type="string"),
	 *              @SWG\Property(property="title", type="string"),
	 *              @SWG\Property(property="seo_title", type="string"),
	 *              @SWG\Property(property="keywords", type="string"),
	 *              @SWG\Property(property="description", type="string"),
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
		$validator = Validator::make(array_merge($request->all(), ['url' => MainController::getUrl($request->url ? $request->url : $request->title)]), [
			'article_category_id' => 'numeric|exists:article_categories,id',
			'image' => 'string|max:255',
			'seo_image' => 'required|string|max:255',
			'icon' => 'required|string|max:255',
			'url' => 'nullable|string|unique:article_category_translates|max:255',
			'title' => 'required|string|unique:article_category_translates|max:255',
			'seo_title' => 'required|string|unique:article_category_translates|max:255',
			'keywords' => 'required|string',
			'description' => 'required|string',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		if ($request->has('article_category_id')) {
			$parentCategory = ArticleCategory::find($request->article_category_id);
			if ($parentCategory->article_category_id == null && $parentCategory->has_articles == 0) {
				$parentCategory->is_last = 0;
				$parentCategory->save();
			} else {
				return response('Bad parent category', 400);
			}
		}

		$order = ArticleCategory::max('order');
		$order = empty($order) ? 0 : $order;
		$articleCategory = ArticleCategory::create([
			'order' => ++$order,
			'image' => $request->image,
			'seo_image' => $request->seo_image,
			'icon' => $request->icon,
			'article_category_id' => $request->article_category_id,
		]);

		if (ArticleCategoryTranslate::create(array_merge($request->all(),
			[
				'url' => MainController::getUrl($request->url ? $request->url : $request->title),
				'language_id' => env('DEFAULT_LANG_ID', 1),
				'article_category_id' => $articleCategory->id
			]
		))
		)
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/articleCategory/{id}/edit",
	 *     tags={"Article Category"},
	 *     summary="Show the form for editing article category",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article category id",
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
			'id' => 'required|numeric|exists:article_categories,id',
			'language_id' => 'required|numeric|exists:languages,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);


		if (ArticleCategoryTranslate::where('article_category_id', $id)
			->where('language_id', $request->language_id)
			->count()
		) {
			return new ArticleCategoryAll(
				ArticleCategoryTranslate::where('article_category_id', $id)
					->where('language_id', $request->language_id)
					->with('articleCategory')
					->first()
			);
		} else {
			$articleCategory = ArticleCategory::find($id);
			$data = [
				'id' => $id,
				'url' => '',
				'title' => '',
				'seo_title' => '',
				'keywords' => '',
				'description' => '',
				'image' => $articleCategory->image,
				'seo_image' => $articleCategory->seo_image,
				'icon' => $articleCategory->icon,
				'article_category_id' => $articleCategory->article_category_id,
			];
			return [
				'data' => $data,
				'language' => Language::where('id', $request->language_id)->select('id', 'name', 'flag')->first(),
				'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get(),
				'article_category' => ArticleCategoryTranslate::where('article_category_id', $articleCategory->article_category_id)
					->where('language_id', env('DEFAULT_LANG_ID', 1))
					->select('article_category_id as id', 'title')
					->first(),
				'article_categories' => DB::table('article_categories')
					->where('article_categories.article_category_id', null)
					->where('article_categories.has_articles', false)
					->join('article_category_translates', 'article_categories.id', '=', 'article_category_translates.article_category_id')
					->where('article_category_translates.language_id', env('DEFAULT_LANG_ID', 1))
					->select('article_categories.id', 'article_category_translates.title')
					->get(),
			];
		}
	}

	/**
	 * @SWG\Put(
	 *     path="/api/articleCategory/{id}",
	 *     tags={"Article Category"},
	 *     summary="Update article category in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article category id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="Article category translate parameters",
	 *          required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="language_id", type="integer"),
	 *              @SWG\Property(property="article_category_id", type="integer"),
	 *              @SWG\Property(property="image", type="string"),
	 *              @SWG\Property(property="seo_image", type="string"),
	 *              @SWG\Property(property="icon", type="string"),
	 *              @SWG\Property(property="url", type="string"),
	 *              @SWG\Property(property="title", type="string"),
	 *              @SWG\Property(property="seo_title", type="string"),
	 *              @SWG\Property(property="description", type="string"),
	 *              @SWG\Property(property="keywords", type="string"),
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
		$parameters = array_merge($request->all(), ['id' => $id]);
		$validator = Validator::make($parameters, [
			'id' => 'required|numeric|exists:article_categories,id',
			'article_category_id' => 'numeric',
			'url' => 'nullable|string|max:255',
			'title' => 'required|string|max:255',
			'seo_title' => 'required|string|max:255',
			'keywords' => 'required|string',
			'description' => 'required|string',
			'image' => 'string|max:255',
			'seo_image' => 'required|string|max:255',
			'icon' => 'required|string|max:255',
			'language_id' => 'required|numeric|exists:languages,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		if (ArticleCategoryTranslate::where('url', MainController::getUrl($request->url ? $request->url : $request->title))
			->where('article_category_id', '!=', $id)->count()
		) {
			$validator = Validator::make(['url' => MainController::getUrl($request->url ? $request->url : $request->title)], [
				'url' => 'unique:article_category_translates,url',
			]);

			if ($validator->fails())
				return response()->json(['errors' => $validator->errors()], 400);
		}

		$articleCategory = ArticleCategory::find($id);

		if ($request->has('article_category_id') && $articleCategory->article_category_id != $request->article_category_id) {
			if ($request->article_category_id == 0) {
				$request->article_category_id = null;
				if (ArticleCategory::where('article_category_id', $articleCategory->article_category_id)->count() == 1) {
					ArticleCategory::find($articleCategory->article_category_id)->update(['is_last' => 1]);
				}
			} else {
				$parentCategory = ArticleCategory::find($request->article_category_id);
				if ($parentCategory->article_category_id == null) {
					$parentCategory->is_last = 0;
					$parentCategory->save();
				} else {
					return response('Bad parent category', 400);
				}
			}
			$articleCategory->article_category_id = $request->article_category_id;
			$articleCategory->save();
			unset($request['article_category_id']);
		}
		if (!empty($request->image) && $articleCategory->image != $request->image) {
			$articleCategory->image = $request->image;
			$articleCategory->save();
			unset($request['image']);
		}
		if (!empty($request->seo_image) && $articleCategory->seo_image != $request->seo_image) {
			$articleCategory->seo_image = $request->seo_image;
			$articleCategory->save();
			unset($request['image']);
		}
		if (!empty($request->icon) && $articleCategory->icon != $request->icon) {
			$articleCategory->icon = $request->icon;
			$articleCategory->save();
			unset($request['icon']);
		}
		if (ArticleCategoryTranslate::updateOrCreate(
			['language_id' => $request->language_id, 'article_category_id' => $id],
			array_merge(
				$request->all(),
				['url' => MainController::getUrl($request->url ? $request->url : $request->title)]
			)
		)
		)
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Put(
	 *     path="/api/articleCategory/{id}/field",
	 *     tags={"Article Category"},
	 *     summary="Update article category field in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article category id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="Article category field (is_active, order) and his new value",
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
		if (ArticleCategory::find($id)->update([$request->field => $request->value]))
			return response('Successful operation', 200);
	}


	/**
	 * @SWG\Get(
	 *     path="/api/articleCategory/search",
	 *     tags={"Article Category"},
	 *     summary="Search article category",
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
		if ($request->number && $request->number != 0)
			$number = $request->number;
		else
			$number = env('DEFAULT_NUMBER_PER_PAGE', 20);

		$ArticleCategories = DB::table('article_categories')
			->join('article_category_translates', 'article_categories.id', '=', 'article_category_translates.article_category_id')
			->where('article_category_translates.language_id', env('DEFAULT_LANG_ID', 1))
			->where('article_category_translates.title', 'LIKE', $request->value . '%');

		if ($request->has('method') && $request->has('field')) {
			$ArticleCategories->orderBy($request->field, $request->method);
		} else {
			$ArticleCategories->orderBy('order');
		}

		return new ArticleCategoryCollection(
			$ArticleCategories->select('article_categories.id', 'article_categories.order', 'article_categories.icon', 'article_categories.is_active', 'article_category_translates.title', 'article_category_translates.url')
				->paginate($number)
		);
	}

	/**
	 * @SWG\Delete(
	 *     path="/api/articleCategory/{id}",
	 *     tags={"Article Category"},
	 *     summary="Remove article category from storage",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article category id",
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
		$articleCategory = ArticleCategory::find($id);
		if ($articleCategory->article_category_id != null && ArticleCategory::where('article_category_id', $articleCategory->article_category_id)->count() == 1) {
			ArticleCategory::find($articleCategory->article_category_id)->update(['is_last' => 1]);
		}
		return ArticleCategory::destroy($id);
	}
}
