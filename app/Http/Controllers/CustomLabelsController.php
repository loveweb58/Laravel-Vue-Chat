<?php

namespace App\Http\Controllers;

use App\Models\CustomLabel;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomLabelsController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(CustomLabel::class, 'label');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index', CustomLabel::class);

        $labels = Auth::user()->account->customLabels()->orderBy('id')->paginate(20);

        return view('pages.custom_labels', compact('labels'));
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
            'name'    => [
                'required',
                'string',
                'max:255',
                Rule::unique('custom_labels')->where('account_id', Auth::user()->account_id),
            ],
            'default' => 'nullable|string',
        ]);

        if (Auth::user()->account->customLabels()->count() >= Auth::user()->account->limits('custom_labels')) {
            return response(['message' => 'Custom labels limit reached'], 500);
        }

        Auth::user()->account->customLabels()->create($request->only([
            'name',
            'default',
        ]));

        return response()->json(['message' => 'Custom label successfully created']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomLabel $label
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomLabel $label)
    {
        return response($label);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\CustomLabel  $label
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, CustomLabel $label)
    {
        $this->validate($request, [
            'name'    => [
                'required',
                'string',
                'max:255',
                Rule::unique('custom_labels')->where('account_id', Auth::user()->account_id)->ignore($label->id),
            ],
            'default' => 'nullable|string',
        ]);

        $label->fill($request->only([
            'name',
            'default',
        ]))->save();

        return response()->json(['message' => 'Custom label successfully updated']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomLabel $label
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomLabel $label)
    {
        $label->delete();

        return redirect('custom-labels')->with('message', 'Custom label successfully deleted');
    }
}
