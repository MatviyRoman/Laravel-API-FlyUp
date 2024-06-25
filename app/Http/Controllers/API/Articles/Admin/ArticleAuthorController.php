<?php

namespace App\Http\Controllers\API\Articles\Admin;

use DB;
use App\ArticleAuthor;
use App\ArticleAuthorTranslate;
use App\Http\Resources\ArticleAuthorAll;
use App\Http\Resources\ArticleAuthorCollection;
use App\InterfaceTranslate;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class ArticleAuthorController extends Controller
{
	/**
	 * @SWG\Get(
	 *     path="/api/articleAuthor",
	 *     tags={"Article Author"},
	 *     summary="Display a listing of article authors",
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
	 *          enum={"id", "name", "is_active", "order"}
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
		if ($request->number && $request->number != 0)
			$number = $request->number;
		else
			$number = env('DEFAULT_NUMBER_PER_PAGE', 20);

		$articleAuthors = DB::table('article_authors')
			->join('article_author_translates', 'article_authors.id', '=', 'article_author_translates.article_author_id')
			->where('article_author_translates.language_id', env('DEFAULT_LANG_ID', 1));

		if ($request->has('name'))
			$articleAuthors->where('article_author_translates.name', 'LIKE', '%' . $request->name . '%');
		if ($request->has('method') && $request->has('field')) {
			$articleAuthors->orderBy($request->field, $request->method);
		} else {
			$articleAuthors->orderBy('order');
		}

		return new ArticleAuthorCollection(
			$articleAuthors->select('article_authors.id', 'article_authors.order', 'article_authors.is_active', 'article_author_translates.name')
				->paginate($number)
		);
	}

	/**
	 * @SWG\Post(
	 *     path="/api/articleAuthor",
	 *     tags={"Article Author"},
	 *     summary="Create article author",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *     in="body",
	 *     name="body",
	 *     description="Article author parameters",
	 *     required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="name", type="string")
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
			'name' => 'required|string|max:255'
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$order = ArticleAuthor::max('order');
		$order = empty($order) ? 0 : $order;
		$articleAuthor = ArticleAuthor::create(['order' => ++$order]);

		if (ArticleAuthorTranslate::create(array_merge($request->all(),
			[
				'language_id' => env('DEFAULT_LANG_ID', 1),
				'article_author_id' => $articleAuthor->id
			]
		))
		)
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/articleAuthor/{id}/edit",
	 *     tags={"Article Author"},
	 *     summary="Show the form for editing article author",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article author id",
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
			'id' => 'required|numeric|exists:article_authors,id',
			'language_id' => 'required|numeric|exists:languages,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		if (ArticleAuthorTranslate::where('article_author_id', $id)
			->where('language_id', $request->language_id)
			->count()
		) {
			return new ArticleAuthorAll(
				ArticleAuthorTranslate::where('article_author_id', $id)
					->where('language_id', $request->language_id)
					->with('articleAuthor')
					->first()
			);
		} else {
			$data = [
				'id' => $id,
				'name' => ''
			];
			return [
				'data' => $data,
				'language' => Language::where('id', $request->language_id)->select('id', 'name', 'flag')->first(),
				'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get()
			];
		}

	}

	/**
	 * @SWG\Put(
	 *     path="/api/articleAuthor/{id}",
	 *     tags={"Article Author"},
	 *     summary="Update article author in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article author id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="Article author translate parameters",
	 *          required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="language_id", type="integer"),
	 *              @SWG\Property(property="name", type="string"),
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
			'id' => 'required|numeric|exists:article_authors,id',
			'name' => 'required|string|max:255',
			'language_id' => 'required|numeric|exists:languages,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		if (ArticleAuthorTranslate::where('name', $request->name)->where('article_author_id', '!=', $id)->count()
		) {
			$validator = Validator::make($request->all(), [
				'name' => 'unique:article_author_translates,name',
			]);

			if ($validator->fails())
				return response()->json(['errors' => $validator->errors()], 400);
		}

		if (ArticleAuthorTranslate::updateOrCreate(
			['language_id' => $request->language_id, 'article_author_id' => $id],
			$request->all()
		)
		)
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Put(
	 *     path="/api/articleAuthor/{id}/field",
	 *     tags={"Article Author"},
	 *     summary="Update article author field in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article author id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="Article author field (is_active, order) and his new value",
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
		if (ArticleAuthor::find($id)->update([$request->field => $request->value]))
			return response('Successful operation', 200);
	}


	/**
	 * @SWG\Get(
	 *     path="/api/articleAuthor/search",
	 *     tags={"Article Author"},
	 *     summary="Search article author",
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

		$articleAuthors = DB::table('article_authors')
			->join('article_author_translates', 'article_authors.id', '=', 'article_author_translates.article_author_id')
			->where('article_author_translates.language_id', env('DEFAULT_LANG_ID', 1))
			->where('article_author_translates.name', 'LIKE', $request->value . '%');

		if ($request->has('method') && $request->has('field')) {
			$articleAuthors->orderBy($request->field, $request->method);
		} else {
			$articleAuthors->orderBy('order');
		}

		return new ArticleAuthorCollection(
			$articleAuthors->select('article_authors.id', 'article_authors.order', 'article_authors.is_active', 'article_author_translates.name')
				->paginate($number)
		);
	}

	/**
	 * @SWG\Delete(
	 *     path="/api/articleAuthor/{id}",
	 *     tags={"Article Author"},
	 *     summary="Remove article author from storage",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Article author id",
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
		return ArticleAuthor::destroy($id);
	}
}