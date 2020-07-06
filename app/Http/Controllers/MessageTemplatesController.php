<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MessageTemplatesController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(MessageTemplate::class, 'template');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index', MessageTemplate::class);

        $templates = Auth::user()->account->messageTemplates()->orderBy('id')->paginate(20);

        return view('pages.message_templates', compact('templates'));
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('message_templates')->where('account_id', Auth::user()->account_id),
            ],
            'text' => 'required|string|max:1000',
        ]);

        if (Auth::user()->account->messageTemplates()->count() >= Auth::user()->account->limits('message_templates')) {
            return response(['message' => 'Message Templates limit reached'], 500);
        }

        Auth::user()->account->messageTemplates()->create($request->only([
            'name',
            'text',
        ]));

        return response()->json(['message' => 'Message Template successfully created']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MessageTemplate $template
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(MessageTemplate $template)
    {
        return response($template);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  \App\Models\MessageTemplate $template
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, MessageTemplate $template)
    {
        $this->validate($request, [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('message_templates')->where('account_id', Auth::user()->account_id)->ignore($template->id),
            ],
            'text' => 'required|string|max:1000',
        ]);

        $template->fill($request->only([
            'name',
            'text',
        ]))->save();

        return response()->json(['message' => 'Message Template successfully updated']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MessageTemplate $template
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(MessageTemplate $template)
    {
        $template->delete();

        return redirect('message-templates')->with('message', 'Message Template successfully deleted');
    }
}
