<?php

namespace Vis\Builder\Http\Controllers;

use App\Cms\Admin;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Routing\Controller;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Vis\Builder\Http\Requests\Login;
use lluminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class LoginController extends Controller
{
    private string $sessionError = 'login_not_found';
    private string $routeLogin = 'cms.login.index';
    private string $urlCms = '/admin';
    private Admin $admin;
    private $login;

    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
        $classLogin = (new $admin())->login();
        $this->login = new $classLogin();
    }

    public function index(): View|RedirectResponse
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

        return view(
            'admin::login', [
            'login' => $this->login,
            'admin' => $this->admin,
            ]
        );
    }

    public function store(Login $request): Redirector|RedirectResponse
    {
        try {
            $user = Sentinel::authenticate($request->only(['email', 'password']));

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

    public function logout(): RedirectResponse
    {
        $this->clearSessionsAdmin();

        return redirect()->route($this->routeLogin);
    }

    private function clearSessionsAdmin(): void
    {
        Sentinel::logout();
        session()->forget('table_builder');
    }
}
