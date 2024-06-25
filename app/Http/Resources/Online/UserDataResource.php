<?php

namespace App\Http\Resources\Online;

use App\Http\Resources\Admin\Common\UserRoleResource;
use Illuminate\Http\Resources\Json\Resource;

class UserDataResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'language_id' => $this->language_id,
            'gender' => $this->gender,
            'address' => $this->address,
            'e_address' => $this->e_address,
            'company_name' => $this->company_name,
            'image' => $this->image,
            'zip' => $this->zip,
            'ytunnus' => $this->ytunnus,
            'contact_person_phone' => $this->contact_person_phone,
            'contact_person_email' => $this->contact_person_email,
            'contact_person_name' => $this->contact_person_name,
            'status' => $this->status,

            'is_admin' => $this->role_id == 2,
            'role_id' => $this->role_id,

            'dob' => $this->dob,
            'type' => $this->type,
            'data' => $this->data,
            'files' => $this->files,
            'users' => $this->users,
            'branches' => $this->branches,

            'roles' => UserRoleResource::collection($this->roles),
        ];

        if ($this->abilities) {
            $data['abilities'] = $this->abilities;
        }

        return $data;
    }
}
