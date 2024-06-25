<?php

namespace App\Http\Controllers\API\Articles\Admin;

use App\Article;
use App\ArticleAuthorTranslate;
use App\ArticleCategory;
use App\ArticleCategoryTranslate;
use App\ArticleTranslate;
use App\Http\Controllers\API\PageController;
use App\Http\Resources\ArticleAll;
use App\Http\Resources\ArticleCollection;
use App\InterfaceTranslate;
use App\Language;
use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Validator;

class ArticleController extends Controller
{

	/**
	 * @SWG\Get(
	 *     path="/api/article",
	 *     tags={"Article"},
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
	 *          name="article_category_id",
	 *          description="category id",
	 *          type="integer",
	 *          in="query"
	 *     ),
	 *     @SWG\Parameter(
	 *          name="article_author_id",
	 *          description="author id",
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

		$articles = DB::table('articles')
			->join('article_translates', 'articles.id', '=', 'article_translates.article_id')
			->where('article_translates.language_id', env('DEFAULT_LANG_ID', 1));

		if ($request->has('article_category_id'))
			$articles->where('articles.article_category_id', $request->article_category_id);

		if ($request->has('article_author_id'))
			$articles->where('articles.article_author_id', $request->article_author_id);

		if ($request->has('title'))
			$articles->where('article_translates.title', 'LIKE', '%' . $request->title . '%');

		if ($request->has('method') && $request->has('field'))
			$articles->orderBy($request->field, $request->method);
		else
			$articles->orderBy('order');

		return new ArticleCollection(
			$articles->select('articles.id', 'articles.is_active', 'articles.order', 'articles.image', 'articles.likes', 'articles.views',
				'articles.article_category_id', 'articles.article_author_id', 'article_translates.title', 'article_translates.url')
				->paginate($number)
		);
	}

	/**
	 * @SWG\Post(
	 *     path="/api/article",
	 *     tags={"Article"},
	 *     summary="Create article",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *     in="body",
	 *     name="body",
	 *     description="Article parameters",
	 *     required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="article_category_id", type="integer"),
	 *              @SWG\Property(property="article_author_id", type="integer"),
	 *              @SWG\Property(property="image", type="string"),
	 *              @SWG\Property(property="seo_image", type="string"),
	 *              @SWG\Property(property="url", type="string"),
	 *              @SWG\Property(property="title", type="string"),
	 *              @SWG\Property(property="seo_title", type="string"),
	 *              @SWG\Property(property="keywords", type="string"),
     *              @SWG\Property(property="alt", type="string"),
	 *              @SWG\Property(property="description", type="string"),
	 *              @SWG\Property(property="text", type="string"),
	 *              @SWG\Property(property="subtext", type="string")
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
			'article_category_id' => 'required|numeric|exists:article_categories,id',
			'article_author_id' => 'required|numeric|exists:article_authors,id',
			'image' => 'required|string|max:255',
			'seo_image' => 'required|string|max:255',
            'alt' => 'required|string|max:255',
			'title' => 'required|unique:article_translates|string|max:255',
			'url' => 'nullable|unique:article_translates|string|max:255',
			'seo_title' => 'required|unique:article_translates|string|max:255',
			'keywords' => 'required|string',
			'description' => 'required|string',
			'text' => 'required|string',
			'subtext' => 'required|string',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$articleCategory = ArticleCategory::find($request->article_category_id);
		if (!$articleCategory->is_last)
			return response('Bad parent category', 400);
		$articleCategory->has_articles = 1;
		$articleCategory->save();

		$order = Article::max('order');
		$order = empty($order) ? 0 : $order;
		$article = Article::create([
			'order' => ++$order,
			'image' => $request->image,
			'seo_image' => $request->seo_image,
			'article_category_id' => $request->article_category_id,
			'article_author_id' => $request->article_author_id
		]);

		if (ArticleTranslate::create(array_merge($request->all(),
			[
				'url' => MainController::getUrl($request->url ? $request->url : $request->title),
				'language_id' => env('DEFAULT_LANG_ID', 1),
				'article_id' => $article->id
			]
		))
		)
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/article/{id}/edit",
	 *     tags={"Article"},
	 *     summary="Show the form for editing article",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article id",
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
			'id' => 'required|numeric|exists:articles,id',
			'language_id' => 'required|numeric|exists:languages,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		if (ArticleTranslate::where('article_id', $id)
			->where('language_id', $request->language_id)
			->count()
		) {
			return new ArticleAll(
				ArticleTranslate::where('article_id', $id)
					->where('language_id', $request->language_id)
					->with('article')
					->first()
			);
		} else {
			$article = Article::find($id);
			$data = [
				'id' => $id,
				'url' => '',
				'title' => '',
				'seo_title' => '',
				'keywords' => '',
				'description' => '',
				'text' => '',
				'subtext' => '',
                'alt' => '',
				'image' => $article->image,
				'seo_image' => $article->seo_image
			];
			return [
				'data' => $data,
				'language' => Language::where('id', $request->language_id)->select('id', 'name', 'flag')->first(),
				'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get(),
				'article_category' => ArticleCategoryTranslate::where('article_category_id', $article->article_category_id)
					->where('language_id', env('DEFAULT_LANG_ID', 1))
					->select('article_category_id as id', 'title')
					->first(),
				'article_categories' => DB::table('article_categories')
					->where('article_categories.is_last', true)
					->join('article_category_translates', 'article_categories.id', '=', 'article_category_translates.article_category_id')
					->where('article_category_translates.language_id', env('DEFAULT_LANG_ID', 1))
					->select('article_categories.id', 'article_category_translates.title')
					->get(),
				'article_author' => ArticleAuthorTranslate::where('article_author_id', $article->article_author_id)
					->where('language_id', env('DEFAULT_LANG_ID', 1))
					->select('article_author_id as id', 'name')
					->first(),
				'article_authors' => ArticleAuthorTranslate::where('language_id', env('DEFAULT_LANG_ID', 1))
					->select('article_author_id as id', 'name')
					->get(),
			];
		}
	}

	/**
	 * @SWG\Put(
	 *     path="/api/article/{id}",
	 *     tags={"Article"},
	 *     summary="Update article in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article id",
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
	 *              @SWG\Property(property="article_author_id", type="integer"),
	 *              @SWG\Property(property="image", type="string"),
	 *              @SWG\Property(property="seo_image", type="string"),
	 *              @SWG\Property(property="url", type="string"),
	 *              @SWG\Property(property="title", type="string"),
	 *              @SWG\Property(property="alt", type="string"),
	 *              @SWG\Property(property="seo_title", type="string"),
	 *              @SWG\Property(property="description", type="string"),
	 *              @SWG\Property(property="keywords", type="string"),
	 *              @SWG\Property(property="text", type="string"),
	 *              @SWG\Property(property="subtext", type="string"),
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
			'id' => 'required|numeric|exists:articles,id',
			'image' => 'string|max:255',
			'seo_image' => 'string|max:255',
			'url' => 'nullable|string|max:255',
			'title' => 'required|string|max:255',
			'seo_title' => 'required|string|max:255',
			'keywords' => 'required|string',
			'description' => 'required|string',
            'alt' => 'required|string|max:255',
			'text' => 'required|string',
			'subtext' => 'required|string',
			'language_id' => 'required|numeric|exists:languages,id',
			'article_category_id' => 'required|numeric|exists:article_categories,id',
			'article_author_id' => 'required|numeric|exists:article_authors,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		if (!ArticleCategory::find($request->article_category_id)->is_last)
			return response('Bad parent category', 400);

		if (ArticleTranslate::where('url', MainController::getUrl($request->url ? $request->url : $request->title))
			->where('article_id', '!=', $id)->count()
		) {
			$validator = Validator::make(['url' => MainController::getUrl($request->url ? $request->url : $request->title)], [
				'url' => 'unique:article_translates,url',
			]);

			if ($validator->fails())
				return response()->json(['errors' => $validator->errors()], 400);
		}

		$article = Article::find($id);

		if (!empty($request->image) && $article->image != $request->image) {
			$article->image = $request->image;
			$article->save();
			unset($request['image']);
		}
		if (!empty($request->seo_image) && $article->seo_image != $request->seo_image) {
			$article->seo_image = $request->seo_image;
			$article->save();
			unset($request['seo_image']);
		}
		if (!empty($request->article_category_id) && $article->article_category_id != $request->article_category_id) {
			if (Article::where('article_category_id', $article->article_category_id)->count() == 1)
				ArticleCategory::find($article->article_category_id)->update(['has_articles' => 0]);
			$article->article_category_id = $request->article_category_id;
			$article->save();
			ArticleCategory::find($article->article_category_id)->update(['has_articles' => 1]);
			unset($request['article_category_id']);
		}
		if (!empty($request->article_author_id) && $article->article_author_id != $request->article_author_id) {
			$article->article_author_id = $request->article_author_id;
			$article->save();
			unset($request['article_author_id']);
		}
		if (ArticleTranslate::updateOrCreate(
			['language_id' => $request->language_id, 'article_id' => $id],
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
	 *     path="/api/article/{id}/field",
	 *     tags={"Article"},
	 *     summary="Update article field in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="Article field (article_category_id, is_active, order, image) and his new value",
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
		if (Article::find($id)->update([$request->field => $request->value]))
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/article/search",
	 *     tags={"Article"},
	 *     summary="Search article",
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

		$result = ArticleTranslate::where('language_id', env('DEFAULT_LANG_ID', 1))
			->where('title', 'LIKE', '%' . $request->value . '%')
			->select('article_id as id', 'title')
			->orderBy('id', 'desc')
			->take(10)
			->get();

		if (!$result->count())
			$result = InterfaceTranslate::getTranslate(52, env('DEFAULT_LANG_ID', 1));

		return response($result, 200);
	}

	/**
	 * @SWG\Delete(
	 *     path="/api/article/{id}",
	 *     tags={"Article"},
	 *     summary="Remove article from storage",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article id",
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
		$validator = Validator::make(['id' => $id], [
			'id' => 'required|numeric|exists:articles,id',
		]);
		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$article = Article::find($id);
		if (Article::where('article_category_id', $article->article_category_id)->count() == 1)
			ArticleCategory::find($article->article_category_id)->update(['has_articles' => 0]);

		return Article::destroy($id);
	}
}
