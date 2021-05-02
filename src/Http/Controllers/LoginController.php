<?php

namespace Vis\Builder;

use App\Cms\Admin;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

/**
 * Class LoginController.
 */
class LoginController extends Controller
{
    private $sessionError = 'login_not_found';
    private $routeLogin = 'login_show';
    private $admin;
    private $login;

    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
        $classLogin = (new $admin())->login();
        $this->login = new $classLogin();
    }

    public function showLogin()
    {
        try {
            if (Sentinel::check()) {
                return Redirect::to('/admin');
            }
        } catch (\Cartalyst\Sentinel\Checkpoints\NotActivatedException $e) {
            Session::flash($this->sessionError, __cms('Пользователь не активирован'));
            Sentinel::logout();

            return redirect()->route($this->routeLogin);
        }

        return view('admin::login', [
            'login' => $this->login,
            'admin' => $this->admin,
        ]);
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
                    Session::flash($this->sessionError, __cms('Пользователь не найден'));

                    return redirect()->route($this->routeLogin);
                }

                if ($this->login->onLogin()) {
                    return $this->login->onLogin();
                }

                return Redirect::intended('/admin');
            } catch (\Cartalyst\Sentinel\Checkpoints\ThrottlingException $e) {
                Session::flash($this->sessionError, __cms('Превышено количество возможных попыток входа'));

                return Redirect::route('login_show');
            } catch (\Cartalyst\Sentinel\Checkpoints\NotActivatedException $e) {
                Session::flash($this->sessionError, __cms('Пользователь не активирован'));

                return redirect()->route($this->routeLogin);
            }
        } else {
            Session::flash($this->sessionError, __cms('Некорректные данные запроса'));

            return redirect()->route($this->routeLogin);
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function doLogout()
    {
        Sentinel::logout();
        $this->clearSessionsAdmin();

        return redirect()->route($this->routeLogin);
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
