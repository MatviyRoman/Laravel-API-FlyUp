<?php

namespace App\Http\Controllers\API\Services\Admin;

use App\Service;
use App\ServiceCategoryTranslate;
use App\ServiceTranslate;
use App\Http\Resources\Services\Admin\ServiceAll;
use App\Http\Resources\Services\Admin\ServiceCollection;
use App\InterfaceTranslate;
use App\Language;
use App\Http\Controllers\MainController;
use App\ServiceUnit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Validator;
use App\ArticleAuthorTranslate;
use DB;

class ServiceController extends Controller
{

	/**
	 * @SWG\Get(
	 *     path="/api/service",
	 *     tags={"Service"},
	 *     summary="Display a listing of services",
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
	 *          description="title for search",
	 *          type="string",
	 *          in="query"
	 *     ),
     *     @SWG\Parameter(
     *          name="service_category_id",
     *          description="category id",
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

		$services = DB::table('services')
			->join('service_translates', 'services.id', '=', 'service_translates.service_id')
			->where('service_translates.language_id', env('DEFAULT_LANG_ID', 1));

		if ($request->has('title'))
			$services->where('service_translates.title', 'LIKE', '%' . $request->title . '%');

        if ($request->has('article_author_id'))
            $services->where('services.article_author_id', $request->article_author_id);

        if ($request->has('service_category_id'))
            $services->where('services.service_category_id', $request->service_category_id);

		if ($request->has('method') && $request->has('field'))
			$services->orderBy($request->field, $request->method);
		else
			$services->orderBy('order');

		return new ServiceCollection(
			$services->select('services.id',
				'services.is_active',
				'services.order',
				'services.service_category_id',
				'services.image',
				'services.price',
				'services.price2',
				'services.price3',
				'services.is_delivery_required',
				'services.is_no_price',
				'services.icon',
				'services.docs',
				'services.likes',
				'services.views',
				'service_translates.title',
				'service_translates.url',
                'services.article_author_id'
			)->paginate($number)
		);
	}

	/**
	 * @SWG\Post(
	 *     path="/api/service",
	 *     tags={"Service"},
	 *     summary="Create service",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *     in="body",
	 *     name="body",
	 *     description="Service parameters",
	 *     required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="image", type="string"),
	 *              @SWG\Property(property="service_category_id", type="integer"),
	 *              @SWG\Property(property="seo_image", type="string"),
	 *              @SWG\Property(property="icon", type="string"),
	 *              @SWG\Property(property="docs", type="string"),
	 *              @SWG\Property(property="price", type="string"),
	 *              @SWG\Property(property="price2", type="string"),
	 *              @SWG\Property(property="price3", type="string"),
	 *              @SWG\Property(property="is_no_price", type="boolean"),
	 *              @SWG\Property(property="is_delivery_required", type="boolean"),
	 *              @SWG\Property(property="title", type="string"),
	 *              @SWG\Property(property="url", type="string"),
	 *              @SWG\Property(property="seo_title", type="string"),
	 *              @SWG\Property(property="alt", type="string"),
	 *              @SWG\Property(property="keywords", type="string"),
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
			'image' => 'required|string',
			'price' => 'nullable|numeric|min:0',
			'price2' => 'nullable|numeric|min:0',
			'price3' => 'nullable|numeric|min:0',
			'is_delivery_required' => 'boolean',
			'is_no_price' => 'boolean',
            'service_category_id' => 'required|numeric|exists:service_categories,id',
			'seo_image' => 'required|string|max:255',
			'icon' => 'required|string|max:255',
			'docs' => 'required|string',
			'title' => 'required|unique:service_translates|string|max:255',
			'seo_title' => 'required|unique:service_translates|string|max:255',
            'alt' => 'required|string|max:255',
			'url' => 'required|unique:service_translates|string|max:255',
			'keywords' => 'required|string',
			'description' => 'required|string',
			'text' => 'required|string',
			'subtext' => 'required|string',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$order = Service::max('order');
		$order = empty($order) ? 0 : $order;
		$service = Service::create([
			'order' => ++$order,
			'seo_image' => $request->seo_image,
			'image' => $request->image,
			'price' => $request->price,
			'price2' => $request->price2,
			'price3' => $request->price3,
			'is_delivery_required' => $request->is_delivery_required,
			'is_no_price' => $request->is_no_price,
			'icon' => $request->icon,
			'docs' => $request->docs,
            'service_category_id' => $request->service_category_id
		]);

		if (ServiceTranslate::create(array_merge($request->all(),
			[
				'url' => MainController::getUrl($request->url ? $request->url : $request->title),
				'language_id' => env('DEFAULT_LANG_ID', 1),
				'service_id' => $service->id
			]
		))
		)
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/service/{id}/edit",
	 *     tags={"Service"},
	 *     summary="Show the form for editing service",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Service id",
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
			'id' => 'required|numeric|exists:services,id',
			'language_id' => 'required|numeric|exists:languages,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		if (ServiceTranslate::where('service_id', $id)
			->where('language_id', $request->language_id)
			->count()
		) {
			return new ServiceAll(
				ServiceTranslate::where('service_id', $id)
					->where('language_id', $request->language_id)
					->with('article')
					->first()
			);
		} else {
			$service = Service::find($id);
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
				'seo_image' => $service->seo_image,
				'image' => $service->image,
				'price' => $service->price,
				'price2' => $service->price2,
				'price3' => $service->price3,
				'is_delivery_required' => $service->is_delivery_required,
				'is_no_price' => $service->is_no_price,
				'icon' => $service->icon,
				'docs' => $service->docs
			];
			return [
				'data' => $data,
                'service_category' => ServiceCategoryTranslate::where('service_category_id', $service->service_category_id)
                    ->where('language_id', env('DEFAULT_LANG_ID', 1))
                    ->select('service_category_id as id', 'title')
                    ->first(),
                'service_categories' => DB::table('service_categories')
                    //->where('service_categories.is_last', true)
                    ->join('service_category_translates', 'service_categories.id', '=', 'service_category_translates.service_category_id')
                    ->where('service_category_translates.language_id', env('DEFAULT_LANG_ID', 1))
                    ->select('service_categories.id', 'service_category_translates.title')
                    ->get(),
				'language' => Language::where('id', $request->language_id)->select('id', 'name', 'flag')->first(),
				'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get(),
			];
		}
	}

	/**
	 * @SWG\Put(
	 *     path="/api/service/{id}",
	 *     tags={"Service"},
	 *     summary="Update service in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Service id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="Service parameters",
	 *          required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="language_id", type="integer"),
	 *              @SWG\Property(property="service_category_id", type="integer"),
	 *              @SWG\Property(property="image", type="string"),
	 *              @SWG\Property(property="price", type="string"),
	 *              @SWG\Property(property="price2", type="string"),
	 *              @SWG\Property(property="price3", type="string"),
	 *              @SWG\Property(property="is_no_price", type="boolean"),
	 *              @SWG\Property(property="is_delivery_required", type="boolean"),
	 *              @SWG\Property(property="alt", type="string"),
	 *              @SWG\Property(property="seo_image", type="string"),
	 *              @SWG\Property(property="icon", type="string"),
	 *              @SWG\Property(property="docs", type="string"),
	 *              @SWG\Property(property="title", type="string"),
	 *              @SWG\Property(property="url", type="string"),
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
		$validator = Validator::make(array_merge($request->all(), ['id' => $id]), [
			'id' => 'required|numeric|exists:services,id',
			'language_id' => 'required|numeric|exists:languages,id',
			'image' => 'nullable|string',
			'seo_image' => 'nullable|string|max:255',
			'price' => 'nullable|numeric|min:0',
			'price2' => 'nullable|numeric|min:0',
			'price3' => 'nullable|numeric|min:0',
			'is_delivery_required' => 'nullable|boolean',
			'is_no_price' => 'nullable|boolean',
			'icon' => 'nullable|string|max:255',
			'docs' => 'nullable|string',
			'title' => 'required|string|max:255',
			'url' => 'nullable|string|max:255',
			'seo_title' => 'required|string|max:255',
			'description' => 'required|string',
            'alt' => 'required|string|max:255',
			'keywords' => 'required|string',
			'text' => 'required|string',
			'subtext' => 'required|string',
            'service_category_id' => 'required|numeric|exists:service_categories,id',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		if (ServiceTranslate::where('url', MainController::getUrl($request->url ? $request->url : $request->title))
			->where('service_id', '!=', $id)->count()
		) {
			$validator = Validator::make(['url' => MainController::getUrl($request->url ? $request->url : $request->title)], [
				'url' => 'unique:service_translates,url',
			]);

			if ($validator->fails())
				return response()->json(['errors' => $validator->errors()], 400);
		}

		$service = Service::find($id);

        $service->update($request->all());

		if (ServiceTranslate::updateOrCreate(
			['language_id' => $request->language_id, 'service_id' => $id],
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
	 *     path="/api/service/{id}/field",
	 *     tags={"Service"},
	 *     summary="Update service field in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Service id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *          in="body",
	 *          name="body",
	 *          description="Service field (service_category_id, is_active, order, image, icon, docs, price, price2, price3, is_delivery_required, is_no_price) and his new value",
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
		if (Service::find($id)->update([$request->field => $request->value]))
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/service/search",
	 *     tags={"Service"},
	 *     summary="Search service",
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

		$result = ServiceTranslate::where('language_id', env('DEFAULT_LANG_ID', 1))
			->where('title', 'LIKE', '%' . $request->value . '%')
			->select('service_id as id', 'title')
			->orderBy('id', 'desc')
			->take(10)
			->get();

		if (!$result->count())
			$result = InterfaceTranslate::getTranslate(52, env('DEFAULT_LANG_ID', 1));

		return response($result, 200);
	}

	/**
	 * @SWG\Delete(
	 *     path="/api/service/{id}",
	 *     tags={"Service"},
	 *     summary="Remove service from storage",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Service id",
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
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
	public function destroy($id)
	{
        $withRelations = request()->has('with_relations') && (bool) request('with_relations');

	    $service = Service::findOrFail($id);

        $serviceUnits = ServiceUnit::where('service_id', $service->id);

        if ($serviceUnits->count()) {
            throw_unless($withRelations, UnprocessableEntityHttpException::class, 'Service has service units.');
        }

        $serviceUnits->withTrashed()->forceDelete();

		$service->delete();

        return response(['status' => 'success'], 200);
	}
}
