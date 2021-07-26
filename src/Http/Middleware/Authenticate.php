<?php

namespace Vis\Builder;

use Cartalyst\Sentinel\Laravel\Facades\{Sentinel, Activation};
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

        $user = Sentinel::getUser();

        if (! $user) {
            if (Request::ajax()) {
                return Response::json([
                    'status'  => 'error',
                    'code'    => '401',
                    'message' => __cms('Нет прав на вход в cms'),
                ], '401');
            }

            return redirect()->guest('login');
        }

        if (!Activation::completed($user)) {
            return $this->returnIfNotHasAccess(__cms('Пользователь не активирован'));
        }

        if (! $user->hasAccess(['admin.access'])) {
            return $this->returnIfNotHasAccess(__cms('Нет прав на вход в cms'));
        }

        \App::singleton('user', function () use ($user) {
            return $user;
        });

        return $next($request);
    }

    private function returnIfNotHasAccess(string $message)
    {
        Session::flash('login_not_found', $message);
        Sentinel::logout();

        return Redirect::route('cms.login.index');
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
