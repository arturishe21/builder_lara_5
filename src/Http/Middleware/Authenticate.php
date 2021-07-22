<?php

namespace Vis\Builder;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use App\Cms\Admin;

class Authenticate
{
    private $adminClass;

    public function __construct(Admin $admin)
    {
        $this->adminClass = $admin;
    }

    public function handle($request, Closure $next)
    {
        if (! $this->checkIp($request)) {
            return redirect()->to('/');
        }

        try {
            if (! Sentinel::check()) {
                if (Request::ajax()) {
                    $data = [
                        'status'  => 'error',
                        'code'    => '401',
                        'message' => __cms('Нет прав на вход в cms'),
                    ];

                    return Response::json($data, '401');
                } else {
                    return redirect()->guest('login');
                }
            }
            //check access
            $user = Sentinel::getUser();
            if (! $user->hasAccess(['admin.access'])) {
                Session::flash('login_not_found', __cms('Нет прав на вход в cms'));
                Sentinel::logout();

                return Redirect::route('cms.login.index');
            }

            \App::singleton('user', function () use ($user) {
                return $user;
            });

        } catch (\Cartalyst\Sentinel\Checkpoints\NotActivatedException $e) {
            Session::flash('login_not_found', __cms('Пользователь не активирован'));
            Sentinel::logout();

            return Redirect::route('cms.login.index');
        }

        return $next($request);
    }

    private function checkIp($request)
    {
        $ip = $this->adminClass->accessIp();

        if (count($ip)) {

            if (!in_array($request->ip(), $ip)) {
                return abort(403);
            }
        }

        return true;
    }
}
