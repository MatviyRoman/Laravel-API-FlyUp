<?php

namespace App\Http\Controllers\API\Admin\Common;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Common\AbilityResource;
use App\Repositories\Admin\Common\Roles\AbilitiesRepository;

/**
 * Class UserAbilityController
 * @package App\Http\Controllers\API\Admin\Common
 */
class UserAbilityController extends Controller
{
    private $abilitiesRepository;

    function __construct(AbilitiesRepository $abilitiesRepository)
    {
        $this->abilitiesRepository = $abilitiesRepository;
    }

    public function getList()
    {
        return AbilityResource::collection($this->abilitiesRepository->getList());
    }
}