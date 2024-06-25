<?php namespace App\Http\Middleware;

use App\Repositories\Admin\Common\Roles\AbilitiesRepository;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
* @property Auth auth
*/
class CheckAbilities
{
    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Throwable
     */
    public function handle($request, Closure $next)
    {
        $r = Route::current();
        $actions = $r->getAction();

        if (array_key_exists('abs', $actions)) {
            $hasAccess = AbilitiesRepository::checkCurrentUserAbilities($actions['abs']);

            throw_unless($hasAccess, AccessDeniedHttpException::class, 'Sorry, You are unauthorized for this action.');
        }

        return $next($request);
    }
}
