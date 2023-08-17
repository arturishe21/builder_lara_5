<?php

namespace Vis\Builder\Http\Middleware;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Closure;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class AuthenticateFrontend
{
    public function handle($request, Closure $next): mixed
    {
        try {
            if (! Sentinel::check()) {
                if (Request::ajax()) {
                    $data = [
                        'status'  => 'error',
                        'code'    => '401',
                        'message' => 'Unauthorized',
                    ];

                    return Response::json($data, '401');
                }

                return  response()->view('admin::errors.401', [], 401);
            }
        } catch (NotActivatedException $e) {
            Session::flash('login_not_found', 'Пользователь не активирован');
            Sentinel::logout();

            return response()->view('admin::errors.401', [], 401);
        }

        return $next($request);
    }
}
