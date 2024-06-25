<?php
/**
 * Created by PhpStorm.
 * User: doon
 * Date: 02.07.18
 * Time: 18:58
 */

namespace App\Http\Resources\Contacts\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use DB;

class ContactCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function with($request)
    {
        return [
            'meta' => [
                'current_lang' => DB::table('languages')->find(env('DEFAULT_LANG_ID', 1))->name
            ],
        ];
    }

}