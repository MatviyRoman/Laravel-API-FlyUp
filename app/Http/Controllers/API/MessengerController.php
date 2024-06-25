<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MessengerController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/api/messenger",
     *     tags={"Messenger"},
     *     summary="Display a listing of messengers",
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

        $messengers = DB::table('messengers');

        if ($request->has('method') && $request->has('field'))
            $messengers->orderBy($request->field, $request->method);
        else
            $messengers->orderBy('order');

        return $messengers->get();
    }
}
