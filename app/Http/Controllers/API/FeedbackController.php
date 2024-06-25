<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp;
use Validator;
use App;
use App\Feedback;
use App\Language;
use App\InterfaceTranslate;
use App\Http\Resources\FeedbackCollection;
use App\Http\Resources\FeedbackAll as FeedbackResource;

class FeedbackController extends Controller
{
	/**
	 * @SWG\Get(
	 *     path="/api/feedback",
	 *     tags={"Feedback"},
	 *     summary="Display a listing of feedback",
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
			$number = env('DEFAULT_SERVICES_PER_PAGE', 20);
		return new FeedbackCollection(
			Feedback::select('id', 'name', 'email', 'phone', 'type', 'language_id', 'service_id', 'is_viewed', 'created_at')
				->paginate($number)
		);
	}


	/**
	 * @SWG\Post(
	 *     path="/api/feedback",
	 *     tags={"Feedback"},
	 *     summary="Create feedback",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *     in="body",
	 *     name="body",
	 *     description="Feedback paramaters",
	 *     required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="options", type="object",
	 *                      @SWG\Property(property="language_id", type="integer"),
	 *                      @SWG\Property(property="page_id", type="integer"),
	 *                      @SWG\Property(property="recaptcha", type="string"),
	 *              ),
	 *              @SWG\Property(property="fields", type="object",
	 *                      @SWG\Property(property="name", type="string"),
	 *                      @SWG\Property(property="email", type="string"),
	 *                      @SWG\Property(property="phone", type="string"),
	 *                      @SWG\Property(property="message", type="string")
	 *              )
	 *          )
	 *     ),
	 *     @SWG\Response(response=200, description="Successful operation"),
	 *     @SWG\Response(response=400, description="Bad request"),
	 *     @SWG\Response(response=404, description="Resource Not Found"),
	 * )
	 */

	public function store(Request $request)
	{
		$validator = Validator::make($request->options, [
			'language_id' => 'required|numeric|exists:languages,id',
			'recaptcha' => 'required|string'
		]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$client = new GuzzleHttp\Client(['base_uri' => 'https://www.google.com']);

		$response = $client->request('POST', '/recaptcha/api/siteverify', [
			'form_params' => [
				'secret' => env('RECAPTCHA_SECRET', '6Ldv6mkUAAAAAOc2HmTI44YghP97GGLyMJ7Is1F9'),
				'response' => $request->options['recaptcha']
			]
		]);

		if (!json_decode($response->getBody())->success)
			return response()->json(['errors' => ['recaptcha' => json_decode($response->getBody())]], 400);

		App::setLocale(Language::find($request->options['language_id'])->name);

		$validator = Validator::make($request->fields, [
			'name' => 'required|string|max:255',
			'email' => 'string|email|max:255',
			'type' => 'required|string',
			'message' => 'required|string',
			'phone' => 'min:9|max:15'
		]);
//            ->setAttributeNames([
//                'name' => InterfaceTranslate::getTranslate(7, $request->options['language_id']),
//                'email' => InterfaceTranslate::getTranslate(8, $request->options['language_id']),
//                'phone' => InterfaceTranslate::getTranslate(9, $request->options['language_id']),
//                'message' => InterfaceTranslate::getTranslate(10, $request->options['language_id']),
//            ]);

		if ($validator->fails())
			return response()->json(['errors' => $validator->errors()], 400);

		$serviceId = null;
		if (array_key_exists('service_id', $request->options)) $serviceId = $request->options['service_id'];

		Feedback::create(array_merge($request->fields, ['language_id' => $request->options['language_id'], 'service_id' => $serviceId]));

		$result['greeting']['header'] = InterfaceTranslate::getTranslate(16, $request->options['language_id']);
		$result['greeting']['message'] = InterfaceTranslate::getTranslate(17, $request->options['language_id']);

		return response()->json($result, 200);

	}

	/**
	 * @SWG\Get(
	 *     path="/api/feedback/{id}/edit",
	 *     tags={"Feedback"},
	 *     summary="Show the form for editing feedback",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Feedback id",
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
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */

	public function edit($id)
	{
		$result = Feedback::findOrFail($id);
		$result->update(['is_viewed' => 1]);
		return new FeedbackResource($result);
	}

	/**
	 * @SWG\Put(
	 *     path="/api/feedback/{id}",
	 *     tags={"Feedback"},
	 *     summary="Update feedback in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Feedback id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *     in="body",
	 *     name="body",
	 *     description="Feedback paramaters",
	 *     required=true,
	 *          @SWG\Schema(
	 *              @SWG\Property(property="name", type="string"),
	 *              @SWG\Property(property="email", type="string"),
	 *              @SWG\Property(property="phone", type="string"),
	 *              @SWG\Property(property="message", type="string"),
	 *              @SWG\Property(property="comment", type="string"),
	 *              @SWG\Property(property="language_id", type="integer"),
	 *              @SWG\Property(property="page_id", type="integer")
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

	public function update(Request $request, $id)
	{
		App::setLocale(env('DEFAULT_LANG', 'ru'));

		$validator = Validator::make($request->fields, [
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255',
			'phone' => 'required|min:9|max:15',
			'message' => 'required|string'
		])->setAttributeNames([
			'name' => InterfaceTranslate::getTranslate(7, $request->options['language_id']),
			'email' => InterfaceTranslate::getTranslate(8, $request->options['language_id']),
			'phone' => InterfaceTranslate::getTranslate(9, $request->options['language_id']),
			'message' => InterfaceTranslate::getTranslate(10, $request->options['language_id']),
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 400);
		}

		if (Feedback::find($id)->update($request->all()))
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Put(
	 *     path="/api/feedback/{id}/field",
	 *     tags={"Feedback"},
	 *     summary="Update feedback field in storage",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Feedback id",
	 *          required=true,
	 *          type="integer",
	 *          in="path"
	 *     ),
	 *     @SWG\Parameter(
	 *     in="body",
	 *     name="body",
	 *     description="Feedback field and his new value",
	 *     required=true,
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
		if (Feedback::find($id)->update([$request->field => $request->value]))
			return response('Successful operation', 200);
	}

	/**
	 * @SWG\Get(
	 *     path="/api/feedback/count",
	 *     tags={"Feedback"},
	 *     summary="Get field value count",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @SWG\Parameter(
	 *          name="field",
	 *          type="string",
	 *          in="query"
	 *     ),
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
	 * Get the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */

	public function count(Request $request)
	{
		if ($request->field != null && $request->value != null) {
			return response(Feedback::where($request->field, $request->value)->count(), 200);
		} else {
			return response()->json(['error' => "'field', 'value' is required"], 400);
		}

	}

	/**
	 * @SWG\Delete(
	 *     path="/api/feedback/{id}",
	 *     tags={"Feedback"},
	 *     summary="Remove feedback from storage",
	 *     produces= {"application/json"},
	 *     consumes= {"application/json"},
	 *     @SWG\Parameter(
	 *          name="id",
	 *          description="Feedback id",
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
		return Feedback::destroy($id);
	}
}
