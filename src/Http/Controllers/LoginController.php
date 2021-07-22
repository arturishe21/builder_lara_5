<?php

namespace Vis\Builder;

use App\Cms\Admin;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Routing\Controller;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Vis\Builder\Http\Requests\Login;

class LoginController extends Controller
{
    private $sessionError = 'login_not_found';
    private $routeLogin = 'cms.login.index';
    private $urlCms = '/admin';
    private $admin;
    private $login;

    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
        $classLogin = (new $admin())->login();
        $this->login = new $classLogin();
    }

    public function index()
    {
        try {
            if (Sentinel::check()) {
                return redirect($this->urlCms);
            }
        } catch (NotActivatedException $e) {

            Sentinel::logout();

            return redirect()->route($this->routeLogin)
                             ->with($this->sessionError, __cms('Пользователь не активирован'));
        }

        return view('admin::login', [
            'login' => $this->login,
            'admin' => $this->admin,
        ]);
    }

    public function store(Login $request)
    {
        try {
            $user = Sentinel::authenticate($request->all());

            if (! $user) {
                return redirect()->route($this->routeLogin)
                                 ->with($this->sessionError,  __cms('Пользователь не найден'));
            }

            if ($this->login->onLogin()) {
                return $this->login->onLogin();
            }

            return redirect($this->urlCms);

        } catch (ThrottlingException $e) {
            return redirect()->route($this->routeLogin)
                             ->with($this->sessionError, __cms('Превышено количество возможных попыток входа'));
        } catch (NotActivatedException $e) {
            return redirect()->route($this->routeLogin)
                             ->with($this->sessionError, __cms('Пользователь не активирован'));
        }
    }

    public function logout()
    {
        $this->clearSessionsAdmin();

        return redirect()->route($this->routeLogin);
    }

    private function clearSessionsAdmin()
    {
        Sentinel::logout();
        session()->forget('table_builder');
    }
}
