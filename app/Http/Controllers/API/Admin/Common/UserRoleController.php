<?php

namespace App\Http\Controllers\API\Admin\Common;

use App\Http\Requests\Admin\Common\AssignUserRolesRequest;
use App\Http\Requests\Admin\Common\RemoveUserRolesRequest;
use App\Http\Requests\Admin\Common\CreateRoleRequest;
use App\Http\Requests\Admin\Common\UpdateRoleRequest;
use App\Http\Requests\BaseGridRequest;
use App\Http\Resources\Admin\Common\RoleResource;
use App\Repositories\Admin\Common\Roles\UserRoleRepository;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class UserRoleController
 * @package App\Http\Controllers\API\Admin\Common
 *
 * @property UserRoleRepository $userRolesRepository
 */
class UserRoleController
{
    private $userRolesRepository;

    function __construct(UserRoleRepository $userRolesRepository)
    {
        $this->userRolesRepository = $userRolesRepository;
    }

    /**
     * @param AssignUserRolesRequest $request
     * @return mixed
     */
    public function assignRolesToUser(AssignUserRolesRequest $request)
    {
        $requestData = $request->validated();

        return $this->userRolesRepository->assignRolesToUser($requestData['user_id'], $requestData['roles']);
    }

    /**
     * @param RemoveUserRolesRequest $request
     * @return bool|mixed|null
     */
    public function removeUserRoles(RemoveUserRolesRequest $request)
    {
        $requestData = $request->validated();

        try {
            return $this->userRolesRepository->removeRolesByName($requestData['user_id'], $requestData['roles']);
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function getRolesList(BaseGridRequest $request)
    {
        $requestData = $request->validated();

        return $this->userRolesRepository->getRolesList($requestData);
    }

    /**
     * @param CreateRoleRequest $request
     * @return RoleResource
     */
    public function create(CreateRoleRequest $request)
    {
        $requestData = $request->validated();

        return new RoleResource($this->userRolesRepository->create($requestData, $requestData['abilities']));
    }

    /**
     * @param UpdateRoleRequest $request
     * @return RoleResource
     */
    public function update(UpdateRoleRequest $request)
    {
        $requestData = $request->validated();

        return new RoleResource($this->userRolesRepository->update($requestData, $requestData['abilities']));
    }

    /**
     * @param int $id
     * @return RoleResource
     */
    public function show(int $id)
    {
        return new RoleResource($this->userRolesRepository->show($id));
    }

    /**
     * @param int $id
     * @return ResponseFactory|Response
     * @throws \Throwable
     */
    public function delete(int $id)
    {
        $withRelations = request()->has('with_relations') && (bool) request('with_relations');

        $this->userRolesRepository->delete($id, $withRelations);

        return response(['status' => 'success'], 200);
    }
}