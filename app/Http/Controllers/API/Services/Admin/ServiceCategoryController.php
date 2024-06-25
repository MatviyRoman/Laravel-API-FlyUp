<?php

namespace App\Http\Controllers\API\Services\Admin;

use DB;
use App\ServiceCategory;
use App\ServiceCategoryTranslate;
use App\Http\Resources\ServiceCategories\Admin\ServiceCategoryAll;
use App\Http\Resources\ServiceCategories\Admin\ServiceCategoryCollection;
use App\Language;
use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class ServiceCategoryController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/api/serviceCategory",
     *     tags={"Service Category"},
     *     summary="Display a listing of service categories",
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

        $articleCategories = DB::table('service_categories')
            ->join('service_category_translates', 'service_categories.id', '=', 'service_category_translates.service_category_id')
            ->where('service_category_translates.language_id', env('DEFAULT_LANG_ID', 1));

        if ($request->has('title'))
            $articleCategories->where('service_category_translates.title', 'LIKE', '%' . $request->title . '%');

        if ($request->has('method') && $request->has('field')) {
            $articleCategories->orderBy($request->field, $request->method);
        } else {
            $articleCategories->orderBy('order');
        }

        return new ServiceCategoryCollection(
            $articleCategories->select('service_categories.id', 'service_categories.order', 'service_categories.image', 'service_categories.is_active',
                'service_category_translates.title', 'service_category_translates.url')
                ->paginate($number)
        );
    }

    /**
     * @SWG\Post(
     *     path="/api/serviceCategory",
     *     tags={"Service Category"},
     *     summary="Create service category",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Service category parameters",
     *     required=true,
     *          @SWG\Schema(
     *              @SWG\Property(property="image", type="string"),
     *              @SWG\Property(property="alt", type="string"),
     *              @SWG\Property(property="seo_image", type="string"),
     *              @SWG\Property(property="url", type="string"),
     *              @SWG\Property(property="title", type="string"),
     *              @SWG\Property(property="text", type="string"),
     *              @SWG\Property(property="subtext", type="string"),
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
            'image' => 'nullable|string|max:255',
            'alt' => 'nullable|string|max:255',
            'seo_image' => 'nullable|string|max:255',
            'subtext' => 'nullable|string',
            'url' => 'nullable|string|unique:service_category_translates|max:255',
            'title' => 'required|string|unique:service_category_translates|max:255',
            'seo_title' => 'required|string|unique:service_category_translates|max:255',
            'keywords' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        $order = ServiceCategory::max('order');
        $order = empty($order) ? 0 : $order;
        $serviceCategory = ServiceCategory::create([
            'order' => ++$order,
            'image' => $request->image,
            'seo_image' => $request->seo_image
        ]);

        if (ServiceCategoryTranslate::create(array_merge($request->all(),
            [
                'url' => MainController::getUrl($request->url ? $request->url : $request->title),
                'language_id' => env('DEFAULT_LANG_ID', 1),
                'service_category_id' => $serviceCategory->id
            ]
        ))
        )
            return response('Successful operation', 200);
    }

    /**
     * @SWG\Get(
     *     path="/api/serviceCategory/{id}/edit",
     *     tags={"Service Category"},
     *     summary="Show the form for editing service category",
     *     produces= {"application/json"},
     *     consumes= {"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="Service category id",
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
            'id' => 'required|numeric|exists:service_categories,id',
            'language_id' => 'required|numeric|exists:languages,id',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        if (ServiceCategoryTranslate::where('service_category_id', $id)
            ->where('language_id', $request->language_id)
            ->count()
        ) {
            return new ServiceCategoryAll(
                ServiceCategoryTranslate::where('service_category_id', $id)
                    ->where('language_id', $request->language_id)
                    ->with('serviceCategory')
                    ->first()
            );
        } else {
            $serviceCategory = ServiceCategory::find($id);
            $data = [
                'id' => $id,
                'url' => '',
                'title' => '',
                'text' => '',
                'subtext' => '',
                'seo_title' => '',
                'keywords' => '',
                'description' => '',
                'image' => $serviceCategory->image,
                'seo_image' => $serviceCategory->seo_image,
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
     *     path="/api/serviceCategory/{id}",
     *     tags={"Service Category"},
     *     summary="Update service category in storage",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="Service category id",
     *          required=true,
     *          type="integer",
     *          in="path"
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          description="Service category translate parameters",
     *          required=true,
     *          @SWG\Schema(
     *              @SWG\Property(property="language_id", type="integer"),
     *              @SWG\Property(property="image", type="string"),
     *              @SWG\Property(property="alt", type="string"),
     *              @SWG\Property(property="seo_image", type="string"),
     *              @SWG\Property(property="url", type="string"),
     *              @SWG\Property(property="title", type="string"),
     *              @SWG\Property(property="text", type="string"),
     *              @SWG\Property(property="subtext", type="string"),
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
            'id' => 'required|numeric|exists:service_categories,id',
            'url' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'seo_title' => 'required|string|max:255',
            'keywords' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'seo_image' => 'nullable|string|max:255',
            'language_id' => 'required|numeric|exists:languages,id',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        if (ServiceCategoryTranslate::where('url', MainController::getUrl($request->url ? $request->url : $request->title))
            ->where('service_category_id', '!=', $id)->count()
        ) {
            $validator = Validator::make(['url' => MainController::getUrl($request->url ? $request->url : $request->title)], [
                'url' => 'unique:service_category_translates,url',
            ]);

            if ($validator->fails())
                return response()->json(['errors' => $validator->errors()], 400);
        }

        $serviceCategory = ServiceCategory::find($id);

        if (!empty($request->image) && $serviceCategory->image != $request->image) {
            $serviceCategory->image = $request->image;
            $serviceCategory->save();
            unset($request['image']);
        }
        if (!empty($request->seo_image) && $serviceCategory->seo_image != $request->seo_image) {
            $serviceCategory->seo_image = $request->seo_image;
            $serviceCategory->save();
            unset($request['seo_image']);
        }

        if (ServiceCategoryTranslate::updateOrCreate(
            ['language_id' => $request->language_id, 'service_category_id' => $id],
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
     *     path="/api/serviceCategory/{id}/field",
     *     tags={"Service Category"},
     *     summary="Update service category field in storage",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="Service category id",
     *          required=true,
     *          type="integer",
     *          in="path"
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          description="Service category field (is_active, order) and his new value",
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
        if (ServiceCategory::find($id)->update([$request->field => $request->value]))
            return response('Successful operation', 200);
    }


    /**
     * @SWG\Get(
     *     path="/api/serviceCategory/search",
     *     tags={"Service Category"},
     *     summary="Search service category",
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

        $ArticleCategories = DB::table('service_categories')
            ->join('service_category_translates', 'service_categories.id', '=', 'service_category_translates.service_category_id')
            ->where('service_category_translates.language_id', env('DEFAULT_LANG_ID', 1))
            ->where('service_category_translates.title', 'LIKE', $request->value . '%');

        if ($request->has('method') && $request->has('field')) {
            $ArticleCategories->orderBy($request->field, $request->method);
        } else {
            $ArticleCategories->orderBy('order');
        }

        return new ServiceCategoryCollection(
            $ArticleCategories->select('service_categories.id', 'service_categories.order', 'service_categories.is_active', 'service_category_translates.title', 'service_category_translates.url')
                ->paginate($number)
        );
    }

    /**
     * @SWG\Delete(
     *     path="/api/serviceCategory/{id}",
     *     tags={"Service Category"},
     *     summary="Remove service category from storage",
     *     produces= {"application/json"},
     *     consumes= {"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="Service category id",
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
        $serviceCategory = ServiceCategory::find($id);
//        if ($serviceCategory->service_category_id != null && ServiceCategory::where('service_category_id', $serviceCategory->service_category_id)->count() == 1) {
//            ServiceCategory::find($serviceCategory->service_category_id)->update(['is_last' => 1]);
//        }
        return ServiceCategory::destroy($id);
    }
}