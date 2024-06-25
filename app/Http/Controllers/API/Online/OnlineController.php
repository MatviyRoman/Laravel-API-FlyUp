<?php

namespace App\Http\Controllers\API\Online;

use App\Http\Controllers\Controller;
use App\Http\Requests\Online\UpdateUserDataRequest;
use App\Http\Resources\Online\UserDataResource;
use App\Repositories\Admin\Common\Roles\AbilitiesRepository;
use App\Repositories\UserDataRepository;
use App\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class OnlineController
 * @package App\Http\Controllers\API\Online
 */
class OnlineController extends Controller
{
    /**
     * @var UserDataRepository
     */
    private $userDataRepository;


    function __construct(UserDataRepository $userDataRepository)
    {
        $this->userDataRepository = $userDataRepository;
    }

    /**
     * Get user data
     * @return UserDataResource
     */
    public function getUserData()
    {
        $user = User::find(auth()->id());

        $user->abilities = AbilitiesRepository::getUserAbilitiesList($user->id);

        return new UserDataResource($user);
    }

    /**
     * Update user data
     * @param UpdateUserDataRequest $request
     * @return UserDataResource
     */
    public function updateUserData(UpdateUserDataRequest $request)
    {
        $requestData = $request->validated();

        $user = $this->userDataRepository->updateUserData(Auth::id(), $requestData);

        $user->abilities = AbilitiesRepository::getUserAbilitiesList($user->id);

        return new UserDataResource($user);
    }
}