<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlacklistController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {

        $this->authorize('index', Blacklist::class);

        $numbers = Auth::user()->account->blackList()->paginate(50);

        return view('pages.blacklist', compact('numbers'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {

        $this->authorize('create', Blacklist::class);

        $request->merge(['number' => formatNumber($request->input('number'))]);

        $this->validate($request, [
            'number' => [
                'required',
                'string',
                Rule::unique('blacklist')->where('account_id', Auth::user()->account_id)->whereNull('deleted_at'),
            ],
        ]);

        Auth::user()->account->blackList()->withTrashed()->updateOrCreate([
            'number' => $request->input('number'),
        ], ['deleted_at' => null]);

        return response()->json(['message' => 'Number was successfully added to blacklist']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blacklist $blacklist
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function destroy(Blacklist $blacklist)
    {
        $this->authorize('delete', $blacklist);

        $blacklist->delete();

        return back()->with('message', 'Number was successfully removed from blacklist');

    }
}
