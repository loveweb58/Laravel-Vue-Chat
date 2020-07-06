<?php

namespace App\Http\Controllers;

use App\Models\Forward;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ForwardingController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Forward::class);
    }


    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('index', Forward::class);

        $forwards = Auth::user()->account->forwards()->with('did', 'number')->orderBy('id', 'asc')->paginate(20);

        $did = Auth::user()->account->did;

        return view('pages.forwards', compact('forwards', 'did'));
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
            'did_id'     => ['required', Rule::exists('did', 'id')->where('account_id', $request->user()->account_id)],
            'tmp_did_id' => ['required', Rule::exists('did', 'id')->where('account_id', $request->user()->account_id)],
            'forward_to' => 'required|numeric',
            'enabled'    => 'required|boolean',
        ]);

        Auth::user()->account->forwards()->create($request->only(['did_id', 'tmp_did_id', 'forward_to', 'enabled']));

        return response()->json(['message' => 'Forward successfully created']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Forward $forward
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Forward $forward)
    {
        return response($forward);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Forward      $forward
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, Forward $forward)
    {
        $this->validate($request, [
            'did_id'     => ['required', Rule::exists('did', 'id')->where('account_id', $request->user()->account_id)],
            'tmp_did_id' => ['required', Rule::exists('did', 'id')->where('account_id', $request->user()->account_id)],
            'forward_to' => 'required|numeric',
            'enabled'    => 'required|boolean',
        ]);

        $forward->fill($request->only(['did_id', 'tmp_did_id', 'forward_to', 'enabled']));

        $forward->save();

        return response()->json(['message' => 'Forward successfully updated']);
    }


    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|array|min:1',
        ]);
        Auth::user()->account->forwards()->whereIn('id', $request->input('id'))->delete();

        return response(['message' => 'Forwards successfully deleted']);
    }
}
