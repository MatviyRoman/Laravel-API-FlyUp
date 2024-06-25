<?php
/**
 * Created by PhpStorm.
 * User: doon
 * Date: 02.07.18
 * Time: 18:59
 */

namespace App\Http\Resources\Contacts\Admin;

use App\Messenger;
use Illuminate\Http\Resources\Json\Resource;
use App\Language;
use App\ContactMessenger;
use App\ContactLanguage;

class ContactAll extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->contact_id,
            'name' => $this->name,
            'position' => $this->position,
            'alt' => $this->alt,
            'skype' => $this->contact->skype,
            'phone' => $this->contact->phone,
            'email' => $this->contact->email,
            'image' => $this->contact->image,
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
//            'vacancies_number' => Vacancy::where('vacancy_id', $this->vacancy->id)->count(),
            'messengers' => Messenger::orderBy('order')->select('id', 'name', 'flag')->get(),
            'selected_messengers' => ContactMessenger::where('contact_id', $this->contact_id)->select('messenger_id')->get(),
            'selected_languages' => ContactLanguage::where('contact_id', $this->contact_id)->select('language_id')->get(),
            'language' => Language::where('id', $this->language_id)->select('id', 'name', 'flag')->first(),
            'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get()
        ];
    }
}