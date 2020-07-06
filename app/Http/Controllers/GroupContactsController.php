<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Validator;

class GroupContactsController extends Controller
{

    public function data($id)
    {
        Validator::make(['id' => $id], ['id' => 'required|integer|min:1'])->validate();

        $data = [];

        $contacts = Auth::user()->account->contacts()
                                         ->leftJoin('group_contacts', function (JoinClause $join) use ($id) {
                                             $join->on('group_contacts.contact_id', '=', 'contacts.id');
                                             $join->on('group_contacts.group_id', '=', DB::raw("'$id'"));
                                         });

        jqxFilters($contacts, function (Builder $q, $filter) use ($id) {
            switch ($filter['field']) {
                case 'name':
                    $filter['field'] = DB::raw("CONCAT(first_name, ' ' ,last_name)");
                    break;
                case 'owned':
                    $filter['field'] = 'group_id';
                    if ($filter['value'] == 'true') {
                        $filter['value'] = $id;
                    } else {
                        $q->whereNull('group_id');
                        $filter = null;
                    }
                    break;
            }

            return $filter;
        });

        $data['TotalRows'] = $contacts->count();

        $contacts->orderBy(request('sortdatafield', 'id'), request('sortorder', 'asc'))
                 ->skip(request('recordstartindex', 0))
                 ->take(request('recordendindex', 50));

        $data['Rows'] = $contacts->get([
            'contacts.*',
            DB::raw("IF(group_id != '', '1', '0') as 'owned'"),
            DB::raw("CONCAT(first_name, ' ' ,last_name) as 'name'"),
        ]);

        return response()->json($data);
    }


    public function store(Request $request, $id)
    {
        $this->validate($request, ['id' => 'required|integer|min:1|exists:contacts']);

        $group = Auth::user()->account->groups()->findOrFail($id);

        if ($group->contacts->count() >= Auth::user()->account->limits('group_contacts', 20)) {
            return response(['message' => 'contacts limit reached'], 500);
        }

        $group->contacts()->attach($request->input('id'));

        return response()->json(['message' => 'Contact successfully added']);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, ['id' => 'required|array', 'status' => 'required|string|in:true,false']);
        $group = Auth::user()->account->groups()->findOrFail($id);
        if ($request->input('status') == 'true') {
            if ($group->contacts->count() + count($request->input('id')) >= Auth::user()->account->limits('group_contacts', 20)) {
                return response(['message' => 'contacts limit reached'], 500);
            }
            $group->contacts()->syncWithoutDetaching($request->input('id'));
        } else {
            $group->contacts()->detach($request->input('id'));
        }

        return response()->json(['message' => 'Group contacts successfully updated']);
    }


    public function destroy(Request $request, $id)
    {
        $this->validate($request, ['id' => 'required|integer|min:1|exists:contacts']);
        Auth::user()->account->groups()->findOrFail($id)->contacts()->detach($request->input('id'));

        return response()->json(['message' => 'Contact successfully removed']);
    }
}
