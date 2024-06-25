<?php

namespace App\Repositories\Admin\Common\Roles;

use App\Models\Ability;
use Illuminate\Support\Facades\Auth;

class AbilitiesRepository
{
    public function getAbilitiesByNames(array $abilities)
    {
        return Ability::whereIn('ability_name', $abilities)->get();
    }

    public function getAbilityByName($ability)
    {
        return Ability::where('ability_name', $ability)->first();
    }

    public function getList()
    {
        return Ability::get();
    }

    public static function getUserAbilitiesQuery(int $userId)
    {
        return Ability::join('role_abilities', 'role_abilities.ability_id', 'abilities.id')
            ->join('user_roles', 'user_roles.role_id', 'role_abilities.role_id')
            ->where('user_roles.user_id', $userId)
            ->groupBy('abilities.id')
            ->select('abilities.*');
    }

    public static function getUserAbilitiesList(int $userId)
    {
        return self::getUserAbilitiesQuery($userId)
            ->get()
            ->pluck('ability_name');
    }

    public static function checkAbilities(int $userId, array $abilities, $roleId = false)
    {
        $userAbilities = self::getUserAbilitiesQuery($userId)->get();

        return $userAbilities->whereIn('ability_name', $abilities)->count() || $roleId == 2;
    }

    public static function checkCurrentUserAbilities(array $abilities)
    {
        $user = Auth::user();

        return self::checkAbilities($user->id, $abilities, $user->role_id);
    }
}
