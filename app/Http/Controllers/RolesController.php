<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RolesController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Role::class);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index', Role::class);

        $roles       = Auth::user()->account->roles()->orderBy('id')->paginate(20);
        $permissions = Permission::all();

        return view('pages.roles', compact('roles', 'permissions'));
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
        $request->merge(['name' => snake_case(Str::ascii($request->input('name')))]);
        $this->validate($request, [
            'name'         => [
                'required',
                'max:255',
                Rule::unique('roles')->where('account_id', $request->user()->account_id),
            ],
            'display_name' => 'required||max:255',
            'description'  => 'nullable',
        ]);

        /**
         * @var $role Role
         */
        $role = Role::create(array_merge($request->only([
            'name',
            'display_name',
            'description',
        ]), ['account_id' => $request->user()->account_id]));

        if ($request->has('permissions')) {
            $role->permissions()->attach(request('permissions'));
        }

        return response()->json(['message' => 'Role successfully created']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $role->load([
            'permissions' => function ($query) {
                $query->select(['id']);
            },
        ]);

        return response(array_merge($role->makeHidden('permissions')
                                         ->toArray(), ['permissions' => $role->permissions->pluck('id')]));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Role         $role
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->merge(['name' => snake_case(Str::ascii($request->input('name')))]);
        $this->validate($request, [
            'name'         => [
                'required',
                'max:255',
                Rule::unique('roles')->where('account_id', $request->user()->account_id)->ignore($role->id),
            ],
            'display_name' => 'required||max:255',
            'description'  => 'nullable',
        ]);

        $role->fill($request->only(['name', 'display_name', 'description']))
             ->fill(['account_id' => $request->user()->account_id])
             ->save();

        $role->permissions()->detach();

        if ($request->has('permissions')) {
            $role->permissions()->attach(request('permissions'));
        }

        return response()->json(['message' => 'Role successfully updated']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return back()->with('message', 'Role successfully deleted');
    }
}
