<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Auth;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Appointment::class);
    }


    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Appointment::class);

        $this->validate($request, ['date' => 'nullable|date_format:Y-m-d']);

        $appointments = Auth::user()->account->appointments()->when(request()->has('date'), function (Builder $q) {
            $q->whereBetween('date', [
                Carbon::createFromFormat('Y-m-d', request('date'))->setTime(0, 0, 0),
                Carbon::createFromFormat('Y-m-d', request('date'))->setTime(23, 59, 59),
            ]);
        }, function (Builder $q) {
            $q->whereBetween('date', [Carbon::now()->setTime(0, 0, 0), Carbon::now()->setTime(23, 59, 59)]);
        })->orderBy('date')->paginate(40);

        $today = Carbon::createFromFormat('Y-m-d', request('date', Carbon::now()->toDateString()))->toDateString();
        $hours = [
            '00:00',
            '01:00',
            '02:00',
            '03:00',
            '04:00',
            '05:00',
            '06:00',
            '07:00',
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '12:00',
            '13:00',
            '14:00',
            '15:00',
            '16:00',
            '17:00',
            '18:00',
            '19:00',
            '20:00',
            '21:00',
            '22:00',
            '23:00',
        ];

        return view('pages.appointments', compact('appointments', 'today', 'hours'));
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

        $request->merge(['date' => explode(',', $request->input('date'))]);

        $this->validate($request, [
            'date'     => 'required|array|min:1',
            'date.*'   => 'required|date_format:Y-m-d',
            'min_hour' => 'required|date_format:H:i',
            'max_hour' => 'required|date_format:H:i',
            'visits'   => 'required|min:1|max:6',
        ]);

        $steps = 60 / $request->input('visits');

        foreach ($request->input('date') as $date) {
            $minH = Carbon::createFromFormat("Y-m-d", $date)->setTimeFromTimeString($request->input('min_hour'));
            $maxH = Carbon::createFromFormat("Y-m-d", $date)->setTimeFromTimeString($request->input('max_hour'));

            $minH->diffFiltered(CarbonInterval::minutes($steps), function (Carbon $d) {
                Auth::user()->account->appointments()->updateOrCreate(['date' => $d->toDateTimeString()]);

                return true;
            }, $maxH);
        }

        return response()->json(['message' => 'Appointment successfully created']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Appointment $appointment
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Appointment $appointment)
    {
        return response($appointment);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Appointment  $appointment
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, Appointment $appointment)
    {
        $this->validate($request, [
            'subject'     => 'nullable|string|max:255',
            'status'      => 'string|required|enum:appointments,status',
            'description' => 'nullable|string|max:1000',
        ]);

        $appointment->fill($request->only([
            'subject',
            'status',
            'description',
        ]));

        if ($appointment->getOriginal('status') != 'canceled' && $appointment->status == 'canceled') {
            //TODO Send SMS
        }

        $appointment->save();

        return response()->json(['message' => 'Appointment successfully updated']);
    }


    public function settings(Request $request)
    {

        $this->validate($request, [
            'success'       => 'required|string|min:1',
            'not_available' => 'required|string|min:1',
            'cancel'        => 'required|string|min:1',
            'cancel_error'  => 'required|string|min:1',
        ]);

        Auth::user()->account->setting([
            'appointments.texts.success'       => $request->input('success'),
            'appointments.texts.not_available' => $request->input('not_available'),
            'appointments.texts.cancel'        => $request->input('cancel'),
            'appointments.texts.cancel_error'  => $request->input('cancel_error'),
        ])->save();

        return response(['message' => 'Settings successfully saved']);
    }


    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|array|min:1',
        ]);
        Auth::user()->account->appointments()->whereIn('id', $request->input('id'))->delete();

        return response(['message' => 'Appointments successfully deleted']);
    }
}
