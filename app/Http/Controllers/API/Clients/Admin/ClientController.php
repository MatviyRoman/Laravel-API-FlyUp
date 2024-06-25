<?php

namespace App\Http\Controllers\API\Clients\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Client;
use App\ClientTranslate;
use App\Language;
use DB;
use App\Http\Resources\ClientAll;
use App\Http\Resources\ClientCollection;

class ClientController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/api/clients",
     *     tags={"Clients"},
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
        if($request->number && $request->number != 0)
            $number = $request->number;
        else
            $number = env('DEFAULT_NUMBER_PER_PAGE', 20);

        $clients = DB::table('clients')
            ->join('client_translates', 'clients.id', '=', 'client_translates.client_id')
            ->where('client_translates.language_id', env('DEFAULT_LANG_ID', 1));

        if ($request->has('method') && $request->has('field')) {
            $clients->orderBy($request->field, $request->method);
        } else {
            $clients->orderBy('order');
        }

        return new ClientCollection(
            $clients->select('clients.id', 'clients.order', 'clients.is_active', 'clients.icon', 'client_translates.name')
                ->paginate($number)
         );
    }

    /**
     * @SWG\Post(
     *     path="/api/clients",
     *     tags={"Clients"},
     *     summary="Create client",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Clients parameters",
     *     required=true,
     *          @SWG\Schema(
     *              @SWG\Property(property="icon", type="string"),
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="alt", type="string")
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
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'alt' => 'required|string|max:255'
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        $order = Client::max('order');
        $order = empty($order) ? 0 : $order;
        $client = Client::create(['order' => ++$order, 'icon' => $request->icon]);
        if (ClientTranslate::create(array_merge($request->all(),
            [
                'language_id' => env('DEFAULT_LANG_ID', 1),
                'client_id' => $client->id
            ]
        )))
            return response('Successful operation', 200);
    }

    /**
     * @SWG\Get(
     *     path="/api/clients/{id}/edit",
     *     tags={"Clients"},
     *     summary="Show the form for editing client",
     *     produces= {"application/json"},
     *     consumes= {"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="Client id",
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
        if($request->has(['language_id'])) {
            if(Language::find($request->language_id) && Client::find($id)) {
                if(ClientTranslate::where('client_id', $id)
                    ->where('language_id', $request->language_id)
                    ->count()) {
                    return new ClientAll(
                        ClientTranslate::where('client_id', $id)
                            ->where('language_id', $request->language_id)
                            ->with('client')
                            ->first()
                    );
                } else {
                    $client = Client::find($id);
                    $data = [
                        'id' => $id,
                        'icon' => $client->icon,
                        'alt' => '',
                        'name' => ''
                    ];
                    return [
                        'data' => $data,
                        'language' => Language::where('id', $request->language_id)->select('id', 'name', 'flag')->first(),
                        'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get()
                    ];
                }
            } else {
                return response('Language or client does not exist', 400);
            }
        } else {
            return response('Need language_id', 400);
        }
    }

    /**
     * @SWG\Put(
     *     path="/api/clients/{id}",
     *     tags={"Clients"},
     *     summary="Update client in storage",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="Client id",
     *          required=true,
     *          type="integer",
     *          in="path"
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          description="Client parameters",
     *          required=true,
     *          @SWG\Schema(
     *              @SWG\Property(property="language_id", type="integer"),
     *              @SWG\Property(property="icon", type="string"),
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="alt", type="string")
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'alt' => 'required|string|max:255',
            'language_id' => 'required|numeric|exists:languages,id',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        $client = Client::find($id);

        if (Language::find($request->language_id) && $client) {
            if (!empty($request->icon) && $client->icon != $request->icon) {
                $client->icon = $request->icon;
                $client->save();
            }
            if (ClientTranslate::updateOrCreate(
                ['language_id' => $request->language_id, 'client_id' => $id],
                $request->all()
            ))
                return response('Successful operation', 200);
        } else {
            return response('Language or client does not exist', 400);
        }
    }

    /**
     * @SWG\Get(
     *     path="/api/clients/search",
     *     tags={"Clients"},
     *     summary="Search client",
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
        if($request->number && $request->number != 0)
            $number = $request->number;
        else
            $number = env('DEFAULT_NUMBER_PER_PAGE', 20);

        $clients = DB::table('clients')
            ->join('client_translates', 'clients.id', '=', 'client_translates.client_id')
            ->where('client_translates.language_id', env('DEFAULT_LANG_ID', 1))
            ->where('client_translates.name', 'LIKE', $request->value.'%');

        if ($request->has('method') && $request->has('field')) {
            $clients->orderBy($request->field, $request->method);
        } else {
            $clients->orderBy('order');
        }

        return new ClientCollection(
            $clients->select('clients.id', 'clients.order', 'clients.is_active', 'clients.icon', 'client_translates.name', 'client_translates.alt')
                ->paginate($number)
        );
    }

    /**
     * @SWG\Put(
     *     path="/api/clients/{id}/field",
     *     tags={"Clients"},
     *     summary="Update client field in storage",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="Client id",
     *          required=true,
     *          type="integer",
     *          in="path"
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          description="Client field (icon) and his new value",
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
        if (Client::find($id)->update([$request->field => $request->value]))
            return response('Successful operation', 200);
    }


    /**
     * @SWG\Delete(
     *     path="/api/clients/{id}",
     *     tags={"Clients"},
     *     summary="Remove client from storage",
     *     produces= {"application/json"},
     *     consumes= {"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="Client id",
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
        return Client::destroy($id);
    }
}
