<?php

namespace App\Http\Controllers\API\Admin\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Common\UpdateUserDataRequest;
use App\Http\Requests\BaseGridRequest;
use App\Http\Resources\Online\UserDataResource;
use App\Repositories\UserDataRepository;
use App\User;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class UserController
 * @package App\Http\Controllers\API\Admin\Common
 */
class UserController extends Controller
{
    /**
     * @var UserDataRepository
     */
    private $userDataRepository;

    function __construct(
        UserDataRepository $userDataRepository
    ) {
        $this->userDataRepository = $userDataRepository;
    }

    /**
     * @param BaseGridRequest $request
     * @return AnonymousResourceCollection
     */
    public function getGrid(BaseGridRequest $request)
    {
        $requestData = $request->validated();

        return $this->userDataRepository->getGrid($requestData);
    }

    /**
     * @param int $userId
     * @return UserDataResource
     */
    public function show(int $userId)
    {
        $user = User::findOrFail($userId);

        return new UserDataResource($user);
    }

    /**
     * Update user data
     * @param UpdateUserDataRequest $request
     * @return UserDataResource
     */
    public function update(UpdateUserDataRequest $request)
    {
        $requestData = $request->validated();

        $user = $this->userDataRepository->updateUserData($requestData['id'], $requestData);

        return new UserDataResource($user);
    }

    /**
     * @param int $userId
     * @return UserDataResource
     */
    public function block(int $userId)
    {
        $user = User::findOrFail($userId);

        $user->update(['blocked' => 1]);

        return response(['status' => 'success'], 200);
    }

    /**
     * @param int $userId
     * @return UserDataResource
     */
    public function unBlock(int $userId)
    {
        $user = User::findOrFail($userId);

        $user->update(['blocked' => 0]);

        return response(['status' => 'success'], 200);
    }

    /**
     * Force delete user with relations
     * @param int $userId
     * @return ResponseFactory|Response
     * @throws \Exception
     */
    public function delete(int $userId)
    {
        $user = User::with(['roles'])->findOrFail($userId);

        $user->roles()->sync([]);
        $user->orders()->withTrashed()->forceDelete();
        $user->delete();

        return response(['status' => 'success'], 200);
    }

    /**
     * Force delete user with relations
     * @param int $userId
     * @return ResponseFactory|Response
     * @throws \Exception
     */
    public function forceDelete(int $userId)
    {
        $user = User::with(['roles'])->findOrFail($userId);

        $user->roles()->sync([]);
        $user->delete();

        return response(['status' => 'success'], 200);
    }
}
