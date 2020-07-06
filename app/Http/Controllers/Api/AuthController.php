<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Jobs\SendMessage;
use App\Models\Did;
use App\Models\Message;
use Auth;
use Illuminate\Http\Request;
use Session;

class AuthController extends Controller
{

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


    public function login(Request $request)
    {

        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            if (Carbon::now()->gt($this->guard()->user()->account->expired_at)) {
                return response()->json(['login_status' => 'invalid', 'description' => 'Account Expired!']);
            }


            $user = $this->guard()->user(); 
            $user->generateToken();

            return response()->json([
                'success' => true,
                'user_data' => $user->toArray(),
                'expiry' => $this->guard()->user()->account->expired_at,
                'token' => session('api_token')
            ]);
        }

        return response()->json([
                'success' => false,
            ]);

    }

}
