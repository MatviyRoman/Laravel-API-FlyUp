<?php

namespace App\Repositories\Admin\Common\Roles;

use App\Http\Resources\Admin\Common\RoleResource;
use App\Models\Role;
use App\User;
use App\Models\UserRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use VklComponents\VklTable\VklTableBuilder;

/**
 * Class UserRolesRepository
 * @package App\Repositories\Common
 *
 * @property RolesRepository rolesRepo
 * @property AbilitiesRepository abilitiesRepo
 */
class UserRoleRepository
{
    protected $rolesRepo;
    protected $abilitiesRepo;

    public function __construct()
    {
        $this->rolesRepo = app(RolesRepository::class);
        $this->abilitiesRepo = app(AbilitiesRepository::class);
    }

    /**
     * @param $userId
     * @param $roles
     * @return mixed
     */
    public function assignRolesToUser($userId, $roles)
    {
        if (!$userId || !$roles || (!is_string($roles) && !is_array($roles))) {
            throw new UnprocessableEntityHttpException('Invalid arguments');
        }

        $user = User::findOrFail($userId);

        $roles = Arr::wrap($roles);

        $rolesIds = $this->rolesRepo->getRolesByNames($roles)->pluck('id');

        $user->roles()->withTimestamps()->syncWithoutDetaching($rolesIds);

        $user->load('roles');

        return $user;
    }

    /**
     * @param $userId
     * @param array $roles
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function removeRolesByName($userId, array $roles = [])
    {
        return UserRoles::where('user_id', $userId)
            ->whereHas('role', function (Builder $builder) use ($roles) {
                $builder->whereIn('role_name', $roles);
            })
            ->delete();
    }

    /**
     * @param array $requestData
     * @return mixed
     */
    public function getRolesList(array $requestData)
    {
        $builder = Role::with('abilities')->where('hidden', false);

        $table = new VklTableBuilder($builder, $requestData, RoleResource::class);

        $table->setSearchableColumns(['role_name', 'module', 'role_group']);

        return $table->resolve();
    }

    public function create(array $requestData, $abilities)
    {
        $role = Role::create($requestData);

        $abilities = Arr::wrap($abilities);

        $abilitiesIds = $this->abilitiesRepo->getAbilitiesByNames($abilities)->pluck('id');

        $role->abilities()->withTimestamps()->sync($abilitiesIds);

        $role->load('abilities');

        return $role;
    }

    public function update(array $requestData, $abilities)
    {
        $role = Role::findOrFail($requestData['id']);

        $role->update($requestData);

        $abilities = Arr::wrap($abilities);

        $abilitiesIds = $this->abilitiesRepo->getAbilitiesByNames($abilities)->pluck('id');

        $role->abilities()->withTimestamps()->sync($abilitiesIds);

        $role->load('abilities');

        return $role;
    }

    /**
     * @param int $id
     * @param bool|null $withRelations
     * @return mixed
     * @throws \Throwable
     */
    public function delete(int $id, ?bool $withRelations = false)
    {
        $role = Role::with(['abilities'])->findOrFail($id);

        $userRoles = UserRoles::where('role_id', $role->id);

        if ($userRoles->count()) {
            throw_unless($withRelations, UnprocessableEntityHttpException::class, 'Role has related users.');

            $userRoles->delete();
        }

        if ($role->abilities) {
            $role->abilities()->sync([]);
        }

        return $role->delete();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        $role = Role::findOrFail($id);

        return $role;
    }
}
