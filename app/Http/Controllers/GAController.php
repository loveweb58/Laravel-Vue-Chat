<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Google\Authenticator\GoogleAuthenticator;
use Session;

class GAController extends Controller
{

    public function show()
    {
        if (Session::has('ga_auth')) {
            return redirect('dashboard');
        }

        return view('pages.verify');
    }


    public function verify()
    {
        $ga = new GoogleAuthenticator();
        if ($ga->checkCode(Auth::user()->ga_secret, request('password'))) {
            Session::put('ga_auth', true);

            return response()->json(['login_status' => 'success', 'redirect_url' => url('dashboard')]);

        }

        return response()->json(['login_status' => 'invalid', 'description' => 'არასწორი ავტორიზაციის კოდი']);
    }
}
