<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'dashboard';


    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }


    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('pages.login');
    }


    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/');
    }


    public function ajaxLogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {

            $this->validateLogin($request);

            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return response()->json(['login_status' => 'invalid']);
            }

            $username = filter_var($request->input($this->username()), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $request->merge([$username => $request->input($this->username())]);
            $credentials = $request->only($username, 'password');

            if ($this->guard()->attempt($credentials, $request->has('remember'))) {

                if (Carbon::now()->gt($this->guard()->user()->account->expired_at)) {
                    $this->guard()->logout();

                    return response()->json(['login_status' => 'invalid', 'description' => 'Account Expired!']);
                }

                $path = session()->pull('url.intended', $this->redirectTo);

                return response()->json(['login_status' => 'success', 'redirect_url' => url($path)]);
            }

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            return response()->json(['login_status' => 'invalid', 'description' => __('auth.failed')]);
        } else {
            return $this->login($request);
        }
    }
}
