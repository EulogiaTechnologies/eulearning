<?php

namespace App\Http\Middleware;

use Brian2694\Toastr\Facades\Toastr;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class CheckUserActiveStatus
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (Auth::check()) {
            if (Auth::user()->status == 0) {
                Auth::logout();
                Toastr::error('Your account has been disabled !', 'Failed');
                return redirect('/');
            }
            if (isModuleActive('TwoFA')) {
                $currentRoute = Route::currentRouteName();
                $allowRoutes = [
                    '2fa',
                    'users.settings',
                    'users.2Fa.update',
                    'logout'
                ];
                $user = Auth::user();
                if (!in_array($currentRoute, $allowRoutes) && $user->two_step_verification == 2) {

                    $authenticator = app(Authenticator::class)->boot($request);
//
                    if (!$authenticator->isAuthenticated()) {
                        return $authenticator->makeRequestOneTimePasswordResponse();
                    }
                }
            }
        }

        return $next($request);
    }
}
