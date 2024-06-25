<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/api/language",
     *     tags={"Language"},
     *     summary="Display a listing of languages",
     *     produces= {"application/json"},
     *     consumes= {"application/json"},
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

        $languages = DB::table('languages');

        if ($request->has('method') && $request->has('field'))
            $languages->orderBy($request->field, $request->method);
        else
            $languages->orderBy('order');

        return $languages->get();
    }
}
