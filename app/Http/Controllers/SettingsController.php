<?php

namespace App\Http\Controllers;

use Auth;
use Google\Authenticator\GoogleAuthenticator;
use Hash;
use Illuminate\Http\Request;
use Validator;

class SettingsController extends Controller
{

    public function show()
    {
        $ga      = new GoogleAuthenticator();
        $gaImage = $ga->getUrl(Auth::user()->email, config('app.name'), Auth::user()->ga_secret);
        $did     = Auth::user()->did;

        return view('pages.settings', compact('gaImage', 'did'));
    }


    public function changePassword()
    {
        $validator = Validator::make(request()->all(), [
            'old_password' => 'required|min:4|max:14',
            'password'     => 'required|min:4|max:14|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'password')->withInput();
        }

        if (Hash::check(request('old_password'), Auth::user()->password)) {
            $password              = request('password');
            Auth::user()->password = bcrypt($password);
            Auth::user()->save();

            return redirect()->back()->with('password.message', "Password successfully changed");
        }
        $validator->errors()->add('old_password', 'Current password is incorrect');

        return back()->withErrors($validator, 'password')->withInput();
    }


    public function changeSidebarMenuState()
    {
        Auth::user()->setSetting('sidebar_menu_state', request('state', ''));
    }


    public function GASecret()
    {
        $ga                     = new GoogleAuthenticator();
        Auth::user()->ga_secret = $ga->generateSecret();
        Auth::user()->save();

        return back();
    }


    public function GASecretRemove()
    {
        Auth::user()->ga_secret = null;
        Auth::user()->save();

        return back();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'phone'         => 'nullable|string|max:255',
            'country'       => 'nullable|string|max:255',
            'state'         => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:255',
            'signature'     => 'nullable|string|max:255',
            'avatar'        => 'nullable|image',
            'forward2email' => 'required|boolean',
            'did_sender'    => 'nullable|string',
        ]);

        Auth::user()->fill($request->only([
            'first_name',
            'last_name',
            'phone',
            'country',
            'state',
            'city',
            'address',
            'forward2email',
            'signature',
        ]));

        if (request()->hasFile('avatar')) {
            Auth::user()->avatar = url('storage/' . request()
                    ->file('avatar')
                    ->storePublicly("accounts/{$request->user()->account_id}/avatars", 'public'));
        }
        Auth::user()->save();

        if ($request->input('did_sender', '-1') != '-1') {
            Auth::user()->did()->update(['is_sender' => false]);
            Auth::user()->did()->where('id', $request->input('did_sender'))->update(['is_sender' => true]);
        }

        return response()->json(['message' => 'Profile successfully updated']);
    }


    public function apiToken()
    {
        Auth::user()->api_token = str_random(60);
        Auth::user()->save();

        return back();
    }


    public function apiSms(Request $request)
    {
        $this->validate($request, ['receive_url' => 'nullable|url']);

        if ($request->input('receive_url')) {
            Auth::user()->account->setting([
                'api.sms.receive.status' => true,
                'api.sms.receive.url'    => $request->input('receive_url'),
            ])->save();
        } else {
            Auth::user()->account->setting([
                'api.sms.receive.status' => false,
                'api.sms.receive.url'    => null,
            ])->save();
        }

        return response(['message' => 'API Settings successfully saved']);
    }
}
