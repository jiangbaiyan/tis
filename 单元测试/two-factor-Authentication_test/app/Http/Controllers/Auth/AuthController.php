<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    /**
     * Send the post-authentication response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Http\Response
     */
    protected function authenticated(Request $request, Authenticatable $user)
    {
        if (authy()->isEnabled($user)) {
            return $this->logoutAndRedirectToTokenScreen($request, $user);
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Generate a redirect response to the two-factor token screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Http\Response
     */
    protected function logoutAndRedirectToTokenScreen(Request $request, Authenticatable $user)
    {

        auth($this->getGuard())->logout();

        $request->session()->put('authy:auth:id', $user->id);

        return redirect(url('auth/token'));
    }

    /**
     * Show two-factor authentication page
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function getToken()
    {
        return session('authy:auth:id') ? view('auth.token') : redirect(url('login'));
    }

    /**
     * Verify the two-factor authentication token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postToken(Request $request)
    {
        $this->validate($request, ['token' => 'required']);
        if (! session('authy:auth:id')) {
            return redirect(url('login'));
        }

        $guard = config('auth.defaults.guard');
        $provider = config('auth.guards.' . $guard . '.provider');
        $model = config('auth.providers.' . $provider . '.model');

        $user = (new $model)->findOrFail(
            $request->session()->pull('authy:auth:id')
        );

        if (authy()->tokenIsValid($user, $request->token)) {

            auth($this->getGuard())->login($user);

            return redirect()->intended($this->redirectPath());
        } else {
            return redirect(url('login'))->withErrors('Invalid two-factor authentication token provided!');
        }
    }
}
