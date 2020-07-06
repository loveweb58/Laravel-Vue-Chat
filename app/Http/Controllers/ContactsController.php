<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Auth;
use Config;
use DB;
use Excel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use JeroenDesloovere\VCard\VCard;
use Maatwebsite\Excel\Collections\CellCollection;
use Maatwebsite\Excel\Readers\LaravelExcelReader;

class ContactsController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Contact::class);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index', Contact::class);

        $labels = Auth::user()->account->customLabels;

        return view('pages.contacts', compact('labels'));
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
            'phone'           => [
                'required',
                'numeric',
                Rule::unique('contacts')->where('account_id', Auth::user()->account_id),
            ],
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'nullable|string|max:255',
            'birth_date'      => 'date|nullable',
            'avatar'          => 'nullable|image',
            'email'           => 'nullable|string|max:255',
            'website'         => 'nullable|string|max:255',
            'gender'          => 'nullable|string|in:M,F',
            'bd_text'         => 'nullable|string|max:255',
            'company'         => 'nullable|string|max:255',
            'position'        => 'nullable|string|max:255',
            'country'         => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:255',
            'state'           => 'nullable|string|max:255',
            'address'         => 'nullable|string|max:255',
            'description'     => 'nullable|string|max:255',
            'custom_labels'   => 'nullable|array',
            'custom_labels.*' => 'nullable|string',
        ]);

        if (request()->hasFile('avatar')) {
            $avatar = url('storage/' . request()
                    ->file('avatar')
                    ->storePublicly("accounts/{$request->user()->account_id}/contacts", 'public'));
        } else {
            $avatar = url("/assets/images/member.jpg");
        }

        Contact::create(array_merge($request->only([
            'first_name',
            'last_name',
            'birth_date',
            'email',
            'website',
            'gender',
            'bd_text',
            'company',
            'position',
            'country',
            'city',
            'state',
            'address',
            'description',
            'phone',
            'custom_labels',
        ]), [
            'account_id' => $request->user()->account_id,
            'avatar'     => $avatar,
        ]));

        return response()->json(['message' => 'Contact successfully created']);
    }


    /**
     * Show  specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {

        $this->authorize('all', Contact::class);

        $data = [];

        $contacts = Auth::user()->account->contacts();

        jqxFilters($contacts, function (Builder $q, $filter) {
            switch ($filter['field']) {
                case 'name':
                    $filter['field'] = DB::raw("CONCAT(first_name, ' ' ,last_name)");
                    break;
            }

            return $filter;
        });

        $data['TotalRows'] = $contacts->count();

        $contacts->orderBy(request('sortdatafield', 'id'), request('sortorder', 'asc'))
                 ->skip(request('recordstartindex', 0))
                 ->take(request('recordendindex', 50));

        $data['Rows'] = $contacts->get(['*', DB::raw("CONCAT(first_name, ' ' ,last_name) as 'name'")]);

        return response()->json($data);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        return response($contact);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact      $contact
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        $this->validate($request, [
            'phone'           => [
                'required',
                'numeric',
                Rule::unique('contacts')->where('account_id', Auth::user()->account_id)->ignore($contact->id),
            ],
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'nullable|string|max:255',
            'birth_date'      => 'date|nullable',
            'avatar'          => 'nullable|image',
            'email'           => 'nullable|string|max:255',
            'website'         => 'nullable|string|max:255',
            'gender'          => 'nullable|string|in:M,F',
            'bd_text'         => 'nullable|string|max:255',
            'company'         => 'nullable|string|max:255',
            'position'        => 'nullable|string|max:255',
            'country'         => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:255',
            'state'           => 'nullable|string|max:255',
            'address'         => 'nullable|string|max:255',
            'description'     => 'nullable|string|max:255',
            'custom_labels'   => 'nullable|array',
            'custom_labels.*' => 'nullable|string',
        ]);

        $contact->fill($request->only([
            'first_name',
            'last_name',
            'birth_date',
            'email',
            'website',
            'gender',
            'bd_text',
            'company',
            'position',
            'country',
            'city',
            'state',
            'address',
            'description',
            'custom_labels',
            'phone',
        ]));

        if (request()->hasFile('avatar')) {
            $contact->avatar = url('storage/' . request()
                    ->file('avatar')
                    ->storePublicly("accounts/{$request->user()->account_id}/contacts", 'public'));
        }
        $contact->save();

        return response()->json(['message' => 'Contact successfully updated']);
    }


    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|array|min:1',
        ]);
        Auth::user()->account->contacts()->whereIn('id', $request->input('id'))->delete();

        return response(['message' => 'Contacts successfully deleted']);
    }


    public function export(Contact $contact)
    {
        $vCard = new VCard();
        $vCard->addName($contact->last_name, $contact->first_name);
        $vCard->addCompany($contact->company);
        $vCard->addJobtitle($contact->position);
        $vCard->addEmail($contact->email);
        $vCard->addPhoneNumber($contact->phone, 'PREF;WORK');
        $vCard->addAddress(null, null, $contact->address, $contact->city, $contact->state, null, $contact->country);
        $vCard->addURL($contact->website);
        $vCard->addPhoto($contact->avatar);
        $vCard->download();
    }


    public function import(Request $request)
    {
        $this->validate($request, ['contacts' => 'required|file|mimes:xls,xlsx,csv,txt']);
        $newContacts = [];
        Config::set('excel.csv.delimiter', detectDelimiter($request->file('contacts')));
        Excel::load($request->file('contacts'), function (LaravelExcelReader $reader) use ($newContacts) {

            $reader->each(function (CellCollection $cell) {
                $contact = $cell->only([
                    'first_name',
                    'last_name',
                    'birth_date',
                    'email',
                    'website',
                    'gender',
                    'bd_text',
                    'company',
                    'position',
                    'country',
                    'city',
                    'state',
                    'address',
                    'description',
                    'phone',
                ])->all();

                $cell = $cell->toArray();

                if (isset($cell['name']) && ! empty($cell['name']) && ! isset($cell['first_name'])) {
                    $name                  = explode(' ', $cell['name'], 2);
                    $contact['first_name'] = $name[0] ?? "";
                    $contact['last_name']  = $name[1] ?? "";
                }
                if (isset($contact['phone'])) {
                    $number = preg_replace('/[^0-9\.]/Uis', '', $contact['phone']);
                    if (strlen($number) === 10) {
                        $number = "1" . $number;
                    }
                    $contact['phone'] = $number;
                }
                if (isset($contact['phone']) && isset($contact['first_name']) && strlen($contact['phone']) == 11 && substr($contact['phone'], 0, 1) == "1") {
                    $newContacts[] = Auth::user()->account->contacts()
                                                          ->updateOrCreate(['phone' => $contact['phone']], $contact);
                }
            });

        });

        return back()->with('message', 'Contacts successfully imported');
    }
}
