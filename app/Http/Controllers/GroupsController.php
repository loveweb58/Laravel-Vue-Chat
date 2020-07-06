<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use JeroenDesloovere\VCard\VCard;

class GroupsController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Group::class);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index', Group::class);

        $groups = Auth::user()->account->groups()->withCount('contacts')->orderBy('id')->paginate(20);

        return view('pages.groups', compact('groups'));
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
            'name'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('groups')->where('account_id', Auth::user()->account_id),
            ],
            'description' => 'nullable|string|max:225',
        ]);

        if (Auth::user()->account->groups()->count() >= Auth::user()->account->limits('groups')) {
            return response(['message' => 'Groups limit reached'], 500);
        }

        Auth::user()->account->groups()->create($request->only([
            'name',
            'description',
        ]));

        return response()->json(['message' => 'Group successfully created']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Group $group
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        $pageMenu = [['url' => 'groups', 'name' => 'Groups']];

        return view('pages.group_contacts', compact('group', 'pageMenu'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Group $group
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        return response($group);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Group        $group
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        $this->validate($request, [
            'name'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('groups')->where('account_id', Auth::user()->account_id)->ignore($group->id),
            ],
            'description' => 'nullable|string|max:225',
        ]);

        $group->fill($request->only([
            'name',
            'description',
        ]))->save();

        return response()->json(['message' => 'Group successfully updated']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Group $group
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $group->delete();

        return redirect('groups')->with('message', 'Group successfully deleted');
    }


    public function export(Group $group)
    {
        $vcf = "";
        foreach ($group->contacts as $contact) {
            $vCard = new VCard();
            $vCard->addName($contact->last_name, $contact->first_name);
            $vCard->addCompany($contact->company);
            $vCard->addJobtitle($contact->position);
            $vCard->addEmail($contact->email);
            $vCard->addPhoneNumber($contact->phone, 'PREF;WORK');
            $vCard->addAddress(null, null, $contact->address, $contact->city, $contact->state, null, $contact->country);
            $vCard->addURL($contact->website);
            $vCard->addPhoto($contact->avatar);
            $vcf .= $vCard->buildVCard();
        }

        return response()->make($vcf, 200, [
            'Content-type'        => 'text/x-vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=contacts.vcf',
            'Content-Length'      => mb_strlen($vcf, 'utf-8'),
            'Connection'          => 'close',
        ]);
    }
}
