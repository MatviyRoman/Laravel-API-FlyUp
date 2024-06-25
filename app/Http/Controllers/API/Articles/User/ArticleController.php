<?php

namespace App\Http\Controllers\API\Articles\User;

use App\Article;
use App\ArticleCategory;
use App\ArticleCategoryTranslate;
use App\Http\Controllers\MainController;
use App\Http\Resources\Articles\User\ArticleAll;
use App\Http\Resources\Articles\User\ArticleCollection;
use App\InterfaceTranslate;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
	/**
	 * @SWG\Get(
	 *     path="/api/user/article",
	 *     tags={"User Article"},
	 *     summary="Display a listing of articles",
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
	 *          name="article_category_url",
	 *          description="category url",
	 *          type="string",
	 *          in="query"
	 *     ),
	 *     @SWG\Parameter(
	 *          name="article_author_id",
	 *          description="author id",
	 *          type="integer",
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
			'language_id' => 'required|numeric|exists:languages,id',
			'article_category_id' => 'nullable|numeric|exists:article_categories,id',
			'article_author_id' => 'nullable|numeric|exists:article_authors,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		App::setLocale(Language::find($request->language_id)->name);

		if ($request->number && $request->number != 0)
			$number = $request->number;
		else
			$number = env('DEFAULT_NUMBER_PER_PAGE', 20);
		/*
				$articles = Article::where('is_active', 1)
					->with(['translation' => function ($query) use ($request){
						$query->where('language_id', $request->language_id);
					}]);
		*/

		$articles = DB::table('articles')
			->where('articles.is_active', 1)
			->join('article_translates', 'articles.id', '=', 'article_translates.article_id')
			->where('article_translates.language_id', $request->language_id)
			->whereIn('articles.article_category_id', ArticleCategory::where('is_active', 1)->pluck('id'))
			->select('articles.id', 'articles.image', 'articles.created_at', 'articles.views', 'articles.likes', 'article_translates.language_id', 'article_translates.url',
				'articles.article_category_id', 'articles.article_author_id', 'article_translates.title', 'article_translates.subtext');

		if ($request->has('article_category_url')) {
			$categoryId = ArticleCategoryTranslate::where('url', $request->article_category_url)->first()->article_category_id;
			$articles->where('articles.article_category_id', $categoryId);
		}

		if ($request->has('article_author_id'))
			$articles->where('articles.article_author_id', $request->article_author_id);

		if ($request->has('method') && $request->has('field'))
			$articles->orderBy($request->field, $request->method);
		else
			$articles->orderBy('order');

		return new ArticleCollection($articles->paginate($number));
	}

	/**
	 * @SWG\Get(
	 *     path="/api/user/article/seo/{language_id}/{category_url}/{url}",
	 *     tags={"User Article"},
	 *     summary="Show the article SEO",
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

	public function showSEO($language_id, $category_url, $url)
	{
		$validator = Validator::make(['language_id' => $language_id, 'url' => $url, 'category_url' => $category_url], [
			'language_id' => 'required|numeric|exists:languages,id',
			'category_url' => 'required|string',
			'url' => 'required|string',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$category = ArticleCategoryTranslate::where('language_id', $language_id)
			->where('url', $category_url)
			->firstOrFail();

		if ($category) {
			if (!ArticleCategory::find($category->article_category_id)->is_active)
				return response('Bad request', 400);
			$article = DB::table('articles')
				->where('articles.is_active', 1)
				->where('articles.article_category_id', $category->article_category_id)
				->join('article_translates', 'articles.id', '=', 'article_translates.article_id')
				->where('article_translates.language_id', $language_id)
				->where('article_translates.url', $url)
				->select('articles.id', 'articles.image', 'articles.seo_image', 'article_translates.seo_title', 'article_translates.keywords', 'article_translates.description')
				->first();
		} else {
			return response('Bad request', 400);
		}

		//die(var_dump($article));

		if ($article) {
			return response((array)$article, 200);
		} else {
			abort(404);
		}
	}

	/**
	 * @SWG\Get(
	 *     path="/api/user/article/{language_id}/{category_url}/{url}/{use_case}",
	 *     tags={"User Article"},
	 *     summary="Show the article",
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
	 *     @SWG\Parameter(
	 *          name="url",
	 *          type="string",
	 *          in="path",
	 *     ),
	 *     @SWG\Parameter(
	 *          name="use_case",
	 *          type="string",
	 *          in="path",
	 *          enum={"view", "content", "view_content"}
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 * )
	 */

	public function show($language_id, $category_url, $url, $use_case = null)
	{
		$validator = Validator::make(['language_id' => $language_id, 'url' => $url, 'category_url' => $category_url], [
			'language_id' => 'required|numeric|exists:languages,id',
			'category_url' => 'required|string',
			'url' => 'required|string',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$category = ArticleCategoryTranslate::where('language_id', $language_id)
			->where('url', $category_url)
			->firstOrFail();

		if (!ArticleCategory::find($category->article_category_id)->is_active)
			return response('Bad request', 400);


		$article = DB::table('articles')
			->where('articles.is_active', 1)
			->where('articles.article_category_id', $category->article_category_id)
			->join('article_translates', 'articles.id', '=', 'article_translates.article_id')
			->where('article_translates.language_id', $language_id)
			->where('article_translates.url', $url)
			->select('articles.id', 'articles.article_author_id', 'articles.image', 'articles.seo_image', 'articles.created_at', 'articles.article_category_id', 'articles.article_author_id', 'articles.views', 'articles.likes',
				'article_translates.language_id', 'article_translates.url', 'article_translates.title', 'article_translates.seo_title', 'article_translates.keywords',
				'article_translates.description', 'article_translates.subtext', 'article_translates.text', 'article_translates.alt')
			->first();

		if ($article) {
			if ($use_case) {
				switch ($use_case) {
					case 'view':
						MainController::sessionIncrement(Article::find($article->id));
						return response('view', 200);
					case 'content':
						return new ArticleAll($article);
				}
			}
			MainController::sessionIncrement(Article::find($article->id));
			return new ArticleAll($article);
		} else {
			abort(404);
		}
	}

	/**
	 * @SWG\Get(
	 *     path="/api/user/article/search",
	 *     tags={"User Article"},
	 *     summary="Search article",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="language_id",
	 *          type="integer",
	 *          in="query",
	 *     ),
	 *     @SWG\Parameter(
	 *          name="value",
	 *          type="string",
	 *          in="query"
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=401, description="Unauthenticated"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
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
			'language_id' => 'required|numeric|exists:languages,id',
			'value' => 'required|string|max:255',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		App::setLocale(Language::find($request->language_id)->name);

		// article is active

		$result = DB::table('articles')
			->where('articles.is_active', 1)
			->join('article_translates', 'articles.id', '=', 'article_translates.article_id')
			->where('article_translates.language_id', $request->language_id)
			->whereIn('articles.article_category_id', ArticleCategory::where('is_active', 1)->pluck('id'))
			->where('article_translates.title', 'LIKE', '%' . $request->value . '%')
			->join('article_category_translates', 'articles.article_category_id', '=', 'article_category_translates.article_category_id')
			->where('article_category_translates.language_id', $request->language_id)
			->orderBy('articles.views', 'desc')
			->select('articles.id', 'article_translates.title', 'article_translates.url', 'article_category_translates.url as category')
			->take(10)
			->get();

		if (!$result->count())
			$result = InterfaceTranslate::getTranslate(52, $request->language_id);

		return response($result, 200);
	}

	/**
	 * @SWG\Post(
	 *     path="/api/user/article/like/{id}",
	 *     tags={"User Article"},
	 *     summary="Like the article",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 * )
	 */
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */

	public function like($id)
	{
		$validator = Validator::make(['id' => $id], ['id' => 'required|numeric|exists:articles,id']);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$model = DB::table('articles')->where('id', $id)->select('id', 'likes')->first();
		$active = true;
		$session = array();
		$session = Session::get('articles.likes');
		if ($session) {
			if (false !== $key = array_search($id, $session)) {
				unset($session[$key]);
				Session::put('articles.likes', $session);
				DB::table('articles')
					->where('id', $id)
					->update(['likes' => --$model->likes]);
				$active = false;
			} else {
				Session::push('articles.likes', $id);
				DB::table('articles')
					->where('id', $id)
					->update(['likes' => ++$model->likes]);
			}
		} else {
			$session[] = $id;
			Session::put('articles.likes', $session);
			DB::table('articles')
				->where('id', $id)
				->update(['likes' => ++$model->likes]);
		}

		return response([
			'active' => $active,
			'amount' => $model->likes
		]);
	}
}
