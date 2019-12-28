<?php
namespace Vis\Builder;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;
/**
 * Class Permission
 * @package Vis\Builder
 */
class Permission
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Sentinel::getUser();

        if ($user->getUserId() != 1) {
            if (!$user->hasAccess(['admin.' . $request->segment(2) . '.view'])) {
             //   return abort(403);
            }
        }

        return $next($request);
    }
}
