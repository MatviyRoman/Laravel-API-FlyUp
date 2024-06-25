<?php

namespace App\Http\Controllers\API\Services\User;

use App\Service;
use App\Http\Controllers\MainController;
use App\Http\Resources\Services\User\ServiceAll;
use App\Http\Resources\Services\User\ServiceCollection;
use App\InterfaceTranslate;
use App\Language;
use App\ServiceCategory;
use App\ServiceCategoryTranslate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
	/**
	 * @SWG\Get(
	 *     path="/api/user/service",
	 *     tags={"User Service"},
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
     *          name="service_category_url",
     *          description="category url",
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

		$services = DB::table('services')
			->where('services.is_active', 1)
			->join('service_translates', 'services.id', '=', 'service_translates.service_id')
			->where('service_translates.language_id', $request->language_id)
            ->whereIn('services.service_category_id', ServiceCategory::where('is_active', 1)->pluck('id'))
			->select('services.id', 'services.image', 'services.price', 'services.price2', 'services.price3', 'services.is_delivery_required', 'services.is_no_price',
                'services.icon', 'services.docs', 'services.created_at', 'services.views', 'services.likes', 'service_translates.language_id', 'service_translates.url',
				'service_translates.title', 'services.service_category_id', 'service_translates.subtext');

        if ($request->has('service_category_url')) {
            $serviceCategoryId = ServiceCategoryTranslate::where('url', $request->service_category_url)->first()->service_category_id;
            $services->where('services.service_category_id', $serviceCategoryId);
        }

		if ($request->has('method') && $request->has('field'))
			$services->orderBy($request->field, $request->method);
		else
			$services->orderBy('order');

		return new ServiceCollection($services->paginate($number));
	}

	/**
	 * @SWG\Get(
	 *     path="/api/user/service/seo/{language_id}/{category_url}/{url}",
	 *     tags={"User Service"},
	 *     summary="Show the service SEO",
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
			'url' => 'required|string|exists:service_translates,url',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

        $category = ServiceCategoryTranslate::where('language_id', $language_id)
            ->where('url', $category_url)
            ->firstOrFail();

        if ($category) {
            if (!ServiceCategory::find($category->service_category_id)->is_active)
                return response('Bad request', 400);
            $service = DB::table('services')
                ->where('services.is_active', 1)
                ->where('services.service_category_id', $category->service_category_id)
                ->join('service_translates', 'services.id', '=', 'service_translates.service_id')
                ->where('service_translates.language_id', $language_id)
                ->where('service_translates.url', $url)
                ->select('services.id', 'services.image', 'services.seo_image', 'service_translates.seo_title', 'service_translates.keywords', 'service_translates.description')
                ->first();
        } else {
            return response('Bad request', 400);
        }

		if ($service) {
			return response((array)$service, 200);
		} else {
			abort(404);
		}
	}

	/**
	 * @SWG\Get(
	 *     path="/api/user/service/{language_id}/{category_url}/{url}/{use_case}",
	 *     tags={"User Service"},
	 *     summary="Show the service",
	 *     produces= {"application/json"},
	 *     consumes={"application/x-www-form-urlencoded"},
	 *     @SWG\Parameter(
	 *          name="language_id",
	 *          type="integer",
	 *          in="path",
	 *     ),
	 *     @SWG\Parameter(
	 *          name="url",
	 *          type="string",
	 *          in="path",
	 *     ),
     *     @SWG\Parameter(
     *          name="category_url",
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
			'url' => 'required|string|exists:service_translates,url',
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

        $category = ServiceCategoryTranslate::where('language_id', $language_id)
            ->where('url', $category_url)
            ->firstOrFail();

        if (!ServiceCategory::find($category->service_category_id)->is_active)
            return response('Bad request', 400);

		$service = DB::table('services')
			->where('services.is_active', 1)
            ->where('services.service_category_id', $category->service_category_id)
			->join('service_translates', 'services.id', '=', 'service_translates.service_id')
			->where('service_translates.language_id', $language_id)
			->where('service_translates.url', $url)
			->select('services.id',
				'services.image',
				'services.seo_image',
				'services.price',
				'services.price2',
				'services.price3',
				'services.is_no_price',
				'services.is_delivery_required',
				'services.icon',
				'services.docs',
				'services.created_at',
				'services.views',
				'services.likes',
				'services.service_category_id',
				'service_translates.language_id',
				'service_translates.title',
				'service_translates.seo_title',
				'service_translates.keywords',
				'service_translates.description',
				'service_translates.subtext',
				'service_translates.text',
                'service_translates.alt')
			->first();

		if ($service) {
			if ($use_case) {
				switch ($use_case) {
					case 'view':
						MainController::sessionIncrement(Service::find($service->id));
						return response('view', 200);
					case 'content':
						return new ServiceAll($service);
				}
			}
			MainController::sessionIncrement(Service::find($service->id));
			return new ServiceAll($service);
		} else {
			abort(404);
		}
	}

	/**
	 * @SWG\Get(
	 *     path="/api/user/service/search",
	 *     tags={"User Service"},
	 *     summary="Search service",
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

		$result = DB::table('services')
			->where('services.is_active', 1)
			->join('service_translates', 'services.id', '=', 'service_translates.service_id')
			->where('service_translates.language_id', $request->language_id)
            ->whereIn('services.service_category_id', ServiceCategory::where('is_active', 1)->pluck('id'))
			->where('service_translates.title', 'LIKE', '%' . $request->value . '%')
            ->join('service_category_translates', 'services.service_category_id', '=', 'service_category_translates.service_category_id')
            ->where('service_category_translates.language_id', $request->language_id)
			->orderBy('services.views', 'desc')
			->select('services.id',
				'service_translates.title',
				'service_translates.url',
                'service_category_translates.url as category')
			->take(10)
			->get();

		if (!$result->count())
			$result = InterfaceTranslate::getTranslate(52, $request->language_id);

		return response($result, 200);
	}

	/**
	 * @SWG\Post(
	 *     path="/api/user/service/like/{id}",
	 *     tags={"User Service"},
	 *     summary="Like the service",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Service id",
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
		$validator = Validator::make(['id' => $id], ['id' => 'required|numeric|exists:services,id']);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$model = DB::table('services')->where('id', $id)->select('id', 'likes')->first();
		$active = true;
		$session = array();
		$session = Session::get('services.likes');
		if ($session) {
			if (false !== $key = array_search($id, $session)) {
				unset($session[$key]);
				Session::put('services.likes', $session);
				DB::table('services')
					->where('id', $id)
					->update(['likes' => --$model->likes]);
				$active = false;
			} else {
				Session::push('services.likes', $id);
				DB::table('services')
					->where('id', $id)
					->update(['likes' => ++$model->likes]);
			}
		} else {
			$session[] = $id;
			Session::put('services.likes', $session);
			DB::table('services')
				->where('id', $id)
				->update(['likes' => ++$model->likes]);
		}

		return response([
			'active' => $active,
			'amount' => $model->likes
		]);
	}
}
