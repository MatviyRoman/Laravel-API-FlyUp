<?php

namespace App\Http\Controllers\API\Contacts\User;

use App\Service;
use App\Http\Controllers\MainController;
use App\Http\Resources\Contacts\User\ContactAll;
use App\Http\Resources\Contacts\User\ContactCollection;
use App\InterfaceTranslate;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ContactsController extends Controller
{
	/**
	 * @SWG\Get(
	 *     path="/api/user/contact",
	 *     tags={"User Contact"},
	 *     summary="Display a listing of contacts",
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

		$contacts = DB::table('contacts')
			->where('contacts.is_active', 1)
			->join('contact_translates', 'contacts.id', '=', 'contact_translates.contact_id')
			->where('contact_translates.language_id', $request->language_id)
			->select('contacts.id', 'contacts.image', 'contacts.phone', 'contacts.email', 'contacts.skype',
                 'contact_translates.position', 'contact_translates.name', 'contact_translates.alt');

		if ($request->has('method') && $request->has('field'))
            $contacts->orderBy($request->field, $request->method);
		else
            $contacts->orderBy('order');

        $contacts = collect($contacts->get())->toArray();



        for($i = 0; $i < count($contacts); $i++) {
            $contacts[$i]->languages = DB::table('languages')
                ->join('contact_language', 'contact_language.language_id', '=', 'languages.id')
                ->where('contact_language.contact_id', $contacts[$i]->id)
                ->select('languages.id', 'languages.name', 'languages.flag')->get();

            $contacts[$i]->messengers = DB::table('messengers')
                ->join('contact_messenger', 'contact_messenger.messenger_id', '=', 'messengers.id')
                ->where('contact_messenger.contact_id', $contacts[$i]->id)
                ->select('messengers.id', 'messengers.name', 'messengers.flag')->get();
        }

//        var_dump($contacts);

//        var_dump($contacts->get());


		return $contacts;

//		return new ContactCollection($contacts);
	}


}
