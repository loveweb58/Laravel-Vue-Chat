<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ScheduleMessages;
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

class ScheduleController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(ScheduleMessages::class);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    //    $this->authorize('index', Contact::class);

        $labels = Auth::user()->account->customLabels;

        return view('pages.schedule', compact('labels'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    

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

    public function all1()
    {

    //    $this->authorize('all1', ScheduleMessages::class);

        $data = [];

        $schedule = Auth::user()->account->schedule()->where('flagE',1);

        $data['TotalRows'] = $schedule->count();
        $data['Rows'] = $schedule->get(['*']);
        return response()->json($data);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ScheduleMessages $schedule
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        return 1;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact      $contact
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, ScheduleMessages $schedule)
    {
        $schedule->fill($request->only([
            'text',
        ]));
        $schedule->save();

        return response()->json(['message' => 'Schedule successfully updated']);
    }


    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|array|min:1',
        ]);
        Auth::user()->account->schedule()->whereIn('id', $request->input('id'))->delete();

        return response(['message' => 'Schedule successfully deleted']);
    }
    
}
