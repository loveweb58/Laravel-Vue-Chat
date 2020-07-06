<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(User::class);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index', User::class);

        $users = Auth::user()->account->users()->orderBy('id')->paginate(20);
        $roles = Auth::user()->account->roles;
        $did   = Auth::user()->account->did;

        return view('pages.users', compact('users', 'roles', 'did'));
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
        $this->validate($request, [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users',
            'username'      => 'required|string|max:255|unique:users',
            'password'      => 'required|string|min:6',
            'phone'         => 'nullable|string|max:255',
            'country'       => 'nullable|string|max:255',
            'state'         => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:255',
            'avatar'        => 'nullable|image',
            'signature'     => 'nullable|string|max:255',
            'forward2email' => 'required|boolean',
            'did_sender'    => 'nullable|string',
        ]);

        if (Auth::user()->account->users()->count() >= Auth::user()->account->limits('users')) {
            return response(['message' => 'Users limit reached'], 500);
        }

        if (request()->hasFile('avatar')) {
            $avatar = url('storage/' . request()
                    ->file('avatar')
                    ->storePublicly("accounts/{$request->user()->account_id}/avatars", 'public'));
        } else {
            $avatar = url("/assets/images/member.jpg");
        }

        /**
         * @var $user User
         */
        $user = User::create(array_merge($request->only([
            'first_name',
            'last_name',
            'phone',
            'email',
            'username',
            'country',
            'state',
            'signature',
            'city',
            'address',
            'forward2email',
        ]), [
            'password'   => bcrypt(request('password')),
            'account_id' => $request->user()->account_id,
            'avatar'     => $avatar,
        ]));

        $user->roles()->sync(request('roles', []));

        $user->did()->attach(request('did', []));
        if ($request->input('did_sender', '-1') != '-1') {
            $user->did()->syncWithoutDetaching([
                $request->input('did_sender') => [
                    'is_sender' => true,
                ],
            ]);
        }

        return response()->json(['message' => 'User successfully created']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $user->load([
            'roles' => function ($query) {
                $query->select(['id']);
            },
            'did',
        ]);

        return response(array_merge($user->makeHidden(['roles', 'did'])->toArray(), [
            'roles'      => $user->roles->pluck('id'),
            'did'        => $user->did->pluck('id'),
            'did_sender' => $user->did_sender->id ?? -1,
        ]));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User         $user
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username'      => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'      => 'nullable|string|min:6',
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

        $user->fill($request->only([
            'first_name',
            'last_name',
            'phone',
            'email',
            'username',
            'country',
            'state',
            'city',
            'address',
            'forward2email',
            'signature',
        ]));

        if ($request->has('password')) {
            $user->password = bcrypt(request('password'));
        }

        if (request()->hasFile('avatar')) {
            $user->avatar = url('storage/' . request()
                    ->file('avatar')
                    ->storePublicly("accounts/{$request->user()->account_id}/avatars", 'public'));
        }
        $user->save();

        $user->roles()->sync(request('roles', []));

        $user->did()->sync(request('did', []));
        if ($request->input('did_sender', '-1') != '-1') {
            $user->did()->update(['is_sender' => false]);
            $user->did()->syncWithoutDetaching([
                $request->input('did_sender') => [
                    'is_sender' => true,
                ],
            ]);
        }

        return response()->json(['message' => 'User successfully updated']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('message', 'User successfully deleted');
    }
}
