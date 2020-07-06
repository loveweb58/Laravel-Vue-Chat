<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Package;
use App\Models\Permission;
use App\Models\User;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountsController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Account::class);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('index', Account::class);

        $accounts = Account::orderBy('id')->paginate(20);

        $packages = Package::get();

        $extra_limits = $packages->map(function (Package $package) {
            return collect($package->limits)->map(function ($v, $k) {
                return ['name' => $k, 'type' => gettype($v)];
            });
        })->flatten(1)->unique('name');

        return view('pages.accounts', compact('accounts', 'packages', 'extra_limits'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge([
            'did' => array_filter(explode(',', $request->input('did', '')), function ($v) {
                return $v !== "";
            }),
        ]);
        $this->validate($request, [
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:users',
            'username'          => 'required|string|max:255|unique:users',
            'password'          => 'required|string|min:6',
            'phone'             => 'nullable|string|max:255',
            'country'           => 'nullable|string|max:255',
            'state'             => 'nullable|string|max:255',
            'city'              => 'nullable|string|max:255',
            'address'           => 'nullable|string|max:255',
            'name'              => 'required|string|max:255|unique:accounts',
            'expired_at'        => 'required|date',
            'extra_monthly_fee' => 'required|numeric|min:0',
            'package_id'        => 'required|integer|exists:packages,id',
            'did'               => 'nullable|array',
            'did.*'             => 'numeric|unique:did,did',
            'limits'            => 'nullable|array',
        ]);

        $account = Account::create($request->only([
            'name',
            'expired_at',
            'limits',
            'package_id',
            'extra_monthly_fee',
        ]));

        $account->setting([
            'appointments.texts.success'       => 'We added your appointment to :date. Thank you!',
            'appointments.texts.not_available' => 'Sorry, this period is not available',
            'appointments.texts.cancel'        => 'Appointment successfully cancelled!',
            'appointments.texts.cancel_error'  => 'Sorry, appointment cannot be cancelled',
        ])->save();

        foreacH ($request->input('did', []) as $did) {
            $account->did()->create(['did' => $did]);
        }

        $user = $account->users()->create(array_merge($request->only([
            'first_name',
            'last_name',
            'phone',
            'email',
            'username',
            'country',
            'state',
            'city',
            'address',
        ]), [
            'password' => bcrypt(request('password')),
            'avatar'   => '/assets/images/member.jpg',
        ]));

        $role = $account->roles()->create([
            'name'         => 'admin',
            'display_name' => 'Admin',
        ]);

        $role->permissions()->attach(Permission::all());

        $user->roles()->attach($role->id);

        return response()->json(['message' => 'Account successfully created']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Account $account
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        $account->load([
            'did',
        ]);

        return response(array_merge($account->makeHidden(['did', 'expired_at'])->toArray(), [
            'did'        => $account->did->pluck('did'),
            'expired_at' => $account->expired_at->toDateString(),
            'limits_s'   => $account->limits,
            'limits_i'   => $account->limits,
        ]));
    }


    /**
     * Create the response for when a request fails validation.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array                    $errors
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        if ($request->expectsJson()) {
            return new JsonResponse([
                'message' => array_values($errors)[0] ?? "The given data was invalid.",
                'errors'  => $errors,
            ], 500);
        }

        return redirect()
            ->to($this->getRedirectUrl())
            ->withInput($request->input())
            ->withErrors($errors, $this->errorBag());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Account      $account
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        $request->merge([
            'did' => array_filter(explode(',', $request->input('did', '')), function ($v) {
                return $v !== "";
            }),
        ]);
        $this->validate($request, [
            'name'              => ['required', 'string', 'max:255', Rule::unique('accounts')->ignore($account->id)],
            'expired_at'        => 'required|date',
            'extra_monthly_fee' => 'required|numeric|min:0',
            'package_id'        => 'required|integer|exists:packages,id',
            'did'               => 'nullable|array',
            'did.*'             => ['numeric', Rule::unique('did', 'did')->whereNot('account_id', $account->id)],
            'limits'            => 'nullable|array',
        ]);

        $account->fill($request->only([
            'name',
            'expired_at',
            'extra_monthly_fee',
            'package_id',
            'limits',
        ]))->save();

        $account->did()->whereNotIn('did', $request->input('did'))->delete();
        foreacH ($request->input('did', []) as $did) {
            $account->did()->firstOrCreate(['did' => $did]);
        }

        return response()->json(['message' => 'Account successfully updated']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account $account
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Account $account)
    {
        $account->delete();

        return back()->with('message', 'Account successfully deleted');
    }


    public function auth(Account $account)
    {
        $impersonateUser = Auth::user();

        $user = $account->users()->firstOr(['*'], function () {
            return back();
        });

        Auth::logout();
        session()->flush();
        /**
         * @var $user User
         */
        Auth::login($user);
        session(['ga_auth' => true]);
        session(['impersonate_user' => $impersonateUser->id]);

        return redirect('/');
    }


    public function logout()
    {
        if (session()->has('impersonate_user')) {
            $userId = session('impersonate_user');
            Auth::logout();
            session()->flush();
            Auth::loginUsingId($userId);
            session(['ga_auth' => true]);
        }

        return redirect('accounts');
    }

}
