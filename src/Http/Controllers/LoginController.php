<?php

namespace Vis\Builder;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

/**
 * Class LoginController.
 */
class LoginController extends Controller
{
    private $sessionError = 'login_not_found';
    private $routeLogin = 'login_show';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showLogin()
    {
        try {
            if (Sentinel::check()) {
                return Redirect::to(config('builder.admin.uri'));
            }
        } catch (\Cartalyst\Sentinel\Checkpoints\NotActivatedException $e) {
            Session::flash($this->sessionError, 'Пользователь не активирован');
            Sentinel::logout();

            return Redirect::route($this->routeLogin);
        }

        return view('admin::vis-login');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postLogin()
    {
        if ($this->validation()) {
            try {
                $user = Sentinel::authenticate(
                    [
                        'email'    => request('email'),
                        'password' => request('password'),
                    ]
                );

                if (! $user) {
                    Session::flash($this->sessionError, 'Пользователь не найден');

                    return Redirect::route($this->routeLogin);
                }

                if (config('builder.login.on_login') && config('builder.login.on_login')()) {
                    return config('builder.login.on_login')();
                }

                return Redirect::intended(config('builder.admin.uri'));
            } catch (\Cartalyst\Sentinel\Checkpoints\ThrottlingException $e) {
                Session::flash($this->sessionError, 'Превышено количество возможных попыток входа');

                return Redirect::route('login_show');
            } catch (\Cartalyst\Sentinel\Checkpoints\NotActivatedException $e) {
                Session::flash($this->sessionError, 'Пользователь не активирован');

                return Redirect::route($this->routeLogin);
            }
        } else {
            Session::flash($this->sessionError, 'Некорректные данные запроса');

            return Redirect::route($this->routeLogin);
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function doLogout()
    {
        Sentinel::logout();
        $this->clearSessionsAdmin();

        return Redirect::route($this->routeLogin);
    }

    /**
     * @return bool
     */
    private function validation()
    {
        $rules = [
            'email'    => 'required|email|max:50',
            'password' => 'required|min:6|max:20',
        ];

        $validator = Validator::make(request()->all(), $rules);

        return ! $validator->fails();
    }

    private function clearSessionsAdmin()
    {
        Session::forget('table_builder');
    }
}
