<?php

namespace App\Http\Controllers\API\Contacts\Admin;

use App\Contact;
use App\ContactTranslate;
use App\Http\Resources\Contacts\Admin\ContactAll;
use App\Http\Resources\Contacts\Admin\ContactCollection;
use App\InterfaceTranslate;
use App\Language;
use App\Messenger;
use App\ContactMessenger;
use App\ContactLanguage;
use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use DB;

class ContactsController extends Controller
{

    /**
     * @SWG\Get(
     *     path="/api/contact",
     *     tags={"Contact"},
     *     summary="Display a listing of contacts",
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
     *          name="name",
     *          description="name for search",
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

        $contacts = DB::table('contacts')
            ->join('contact_translates', 'contacts.id', '=', 'contact_translates.contact_id')
            ->where('contact_translates.language_id', env('DEFAULT_LANG_ID', 1));

        if ($request->has('name'))
            $contacts->where('contact_translates.name', 'LIKE', '%' . $request->name . '%');

        if ($request->has('method') && $request->has('field'))
            $contacts->orderBy($request->field, $request->method);
        else
            $contacts->orderBy('order');

        return new ContactCollection(
            $contacts->select('contacts.id',
                'contacts.is_active',
                'contacts.order',
                'contacts.skype',
                'contacts.email',
                'contacts.phone',
                'contacts.image',
                'contact_translates.name'
            )->paginate($number)
        );
    }

    /**
     * @SWG\Post(
     *     path="/api/contact",
     *     tags={"Contact"},
     *     summary="Create contact",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Contact parameters",
     *     required=true,
     *          @SWG\Schema(
     *              @SWG\Property(property="image", type="string"),
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="position", type="string"),
     *              @SWG\Property(property="alt", type="string"),
     *              @SWG\Property(property="email", type="string"),
     *              @SWG\Property(property="phone", type="string"),
     *              @SWG\Property(property="skype", type="string"),
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
        $validator = Validator::make(array_merge($request->all()), [
            'image' => 'required|string|max:255',
            'name' => 'required|unique:contact_translates|string|max:255',
            'position' => 'required|string|max:255',
            'alt' => 'required|string|max:255',
            'skype' => 'required|string|max:255',
            'email' => 'string|email|max:255',
            'phone' => 'min:9|max:15',
            'messengers' => 'required|array',
            'languagesId' => 'required|array',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        $order = Contact::max('order');
        $order = empty($order) ? 0 : $order;
        $contact = Contact::create([
            'order' => ++$order,
            'image' => $request->image,
            'email' => $request->email,
            'phone' => $request->phone,
            'skype' => $request->skype
        ]);

        if (ContactTranslate::create(array_merge($request->all(),
            [
                'language_id' => env('DEFAULT_LANG_ID', 1),
                'contact_id' => $contact->id
            ]
        ))
        ) {
            $messengerContact = [];
            $languageContact = [];

            foreach ($request->messengers as $messengerId) {
                array_push($messengerContact, ['contact_id' => $contact->id, 'messenger_id' => $messengerId]);
            }
            ContactMessenger::insert($messengerContact);

            foreach ($request->languagesId as $languageId) {
                array_push($languageContact, ['contact_id' => $contact->id, 'language_id' => $languageId]);
            }

            ContactLanguage::insert($languageContact);

            return response('Successful operation', 200);
        }

    }

    /**
     * @SWG\Get(
     *     path="/api/contact/{id}/edit",
     *     tags={"Contact"},
     *     summary="Show the form for editing contact",
     *     produces= {"application/json"},
     *     consumes= {"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="Contact id",
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
            'id' => 'required|numeric|exists:contacts,id',
            'language_id' => 'required|numeric|exists:languages,id',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        if (ContactTranslate::where('contact_id', $id)
            ->where('language_id', $request->language_id)
            ->count()
        ) {
            return new ContactAll(
                ContactTranslate::where('contact_id', $id)
                    ->where('language_id', $request->language_id)
                    ->with('contact')
                    ->first()
            );
        } else {
            $contact = Contact::find($id);
            $data = [
                'id' => $id,
                'name' => '',
                'position' => '',
                'skype' => $contact->skype,
                'email' => $contact->email,
                'image' => $contact->image,
                'phone' => $contact->phone,
                'alt' => '',
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
     *     path="/api/contact/{id}",
     *     tags={"Contact"},
     *     summary="Update contact in storage",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="Contact id",
     *          required=true,
     *          type="integer",
     *          in="path"
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          description="Contact parameters",
     *          required=true,
     *          @SWG\Schema(
     *              @SWG\Property(property="language_id", type="integer"),
     *              @SWG\Property(property="image", type="string"),
     *              @SWG\Property(property="alt", type="string"),
     *              @SWG\Property(property="skype", type="string"),
     *              @SWG\Property(property="email", type="string"),
     *              @SWG\Property(property="phone", type="string"),
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="position", type="string"),
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
            'id' => 'required|numeric|exists:contacts,id',
            'language_id' => 'required|numeric|exists:languages,id',
            'image' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'alt' => 'required|string|max:255',
            'skype' => 'required|string|max:255',
            'email' => 'string|email|max:255',
            'phone' => 'min:9|max:15',
            'messengers' => 'required',
            'languagesId' => 'required',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        $contact = Contact::find($id);

        if (!empty($request->image) && $contact->image != $request->image) {
            $contact->image = $request->image;
            $contact->save();
            unset($request['image']);
        }
        if (!empty($request->skype) && $contact->skype != $request->skype) {
            $contact->skype = $request->skype;
            $contact->save();
            unset($request['skype']);
        }
        if (!empty($request->phone) && $contact->phone != $request->phone) {
            $contact->phone = $request->phone;
            $contact->save();
            unset($request['phone']);
        }
        if (!empty($request->email) && $contact->email != $request->email) {
            $contact->email = $request->email;
            $contact->save();
            unset($request['email']);
        }


        if (ContactTranslate::updateOrCreate(
            ['language_id' => $request->language_id, 'contact_id' => $id],
            $request->all()
        )
        ) {
            $messengerContact = [];
            $languageContact = [];

            foreach ($request->messengers['added'] as $messengerId) {
                array_push($messengerContact, ['contact_id' => $id, 'messenger_id' => $messengerId]);
            }
            ContactMessenger::insert($messengerContact);

            foreach ($request->languagesId['added'] as $languageId) {
                array_push($languageContact, ['contact_id' => $id, 'language_id' => $languageId]);
            }

            ContactLanguage::insert($languageContact);

            DB::table('contact_messenger')->whereIn('messenger_id', $request->messengers['removed'])->where('contact_id', $id)->delete();
            DB::table('contact_language')->whereIn('language_id', $request->languagesId['removed'])->where('contact_id', $id)->delete();

            return response('Successful operation', 200);

        }

    }

    /**
     * @SWG\Put(
     *     path="/api/contact/{id}/field",
     *     tags={"Contact"},
     *     summary="Update contact field in storage",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="Contact id",
     *          required=true,
     *          type="integer",
     *          in="path"
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          description="Contact field (is_active, order, image, skype, email, phone) and his new value",
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
        if (Contact::find($id)->update([$request->field => $request->value]))
            return response('Successful operation', 200);
    }

    /**
     * @SWG\Get(
     *     path="/api/contact/search",
     *     tags={"Contact"},
     *     summary="Search contact",
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

        $result = ContactTranslate::where('language_id', env('DEFAULT_LANG_ID', 1))
            ->where('title', 'LIKE', '%' . $request->value . '%')
            ->select('contact_id as id', 'title')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        if (!$result->count())
            $result = InterfaceTranslate::getTranslate(52, env('DEFAULT_LANG_ID', 1));

        return response($result, 200);
    }

    /**
     * @SWG\Delete(
     *     path="/api/contact/{id}",
     *     tags={"Contact"},
     *     summary="Remove contact from storage",
     *     produces= {"application/json"},
     *     consumes= {"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          description="Contact id",
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
        return Contact::destroy($id);
    }
}
