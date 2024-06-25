<?php

namespace App\Http\Controllers\API\Clients\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Language;
use DB;
use App\Http\Resources\ClientCollection;
use Illuminate\Support\Facades\App;

class ClientController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/api/user/client",
     *     tags={"User Client"},
     *     summary="Display a listing of clients",
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

        $clients = DB::table('clients')
            ->where('clients.is_active', 1)
            ->join('client_translates', 'clients.id', '=', 'client_translates.client_id')
            ->where('client_translates.language_id', $request->language_id);
        if($request->has('method') && $request->has('field'))
            $clients->orderBy($request->field, $request->method);
        else
            $clients->orderBy('order');

        return $clients->select('clients.id', 'clients.order', 'clients.is_active', 'clients.icon', 'client_translates.name', 'client_translates.alt')->get();
    }
}
