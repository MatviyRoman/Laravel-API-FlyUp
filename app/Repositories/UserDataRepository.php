<?php

namespace App\Repositories;

use App\Http\Resources\Online\UserDataResource;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use VklComponents\VklTable\VklTableBuilder;

class UserDataRepository
{

    /**
     * @param int $userId
     * @param array $updatedUserData
     * @return mixed
     */
    public function updateUserData(int $userId, array $updatedUserData)
    {
        try {
            $user = User::findOrFail($userId);
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(response('User not exists', 401));
        }

        $user->update($updatedUserData);

        return $user;
    }

    /**
     * @param array $requestData // grid request data with service_id
     * @return mixed
     */
    public function getGrid(array $requestData)
    {
        $builder = User::with('roles');

        $table = new VklTableBuilder($builder, $requestData, UserDataResource::class);

        $table->setSearchableColumns(['first_name', 'last_name', 'email', 'phone']);

        return $table->resolve();
    }

    /**
     * @param array $input
     * @param string $verificationCode
     * @return Model
     * @throws \Exception
     */
    public function register(array $input, string $verificationCode): Model
    {
        $input['verification_token'] = Hash::make($verificationCode);
        $input['password'] = null;

        return User::updateOrCreate(['email' => $input['email']], $input);
    }
}