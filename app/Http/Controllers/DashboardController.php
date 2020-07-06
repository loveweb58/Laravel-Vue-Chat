<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Message;
use Auth;
use Carbon\Carbon;
use Charts;
use DateInterval;
use DatePeriod;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        $account = Auth::user()->account;

        return view('pages.index', compact('account'));
    }


    public function report(Request $request)
    {
        $this->validate($request, [
            'type'     => 'nullable|string|in:bar,line,area',
            'accounts' => 'nullable|array',
            'date'     => 'nullable|date',
        ]);

        $accounts = Account::get();

        $date = Carbon::createFromFormat("Y-m-d", $request->input('date', Carbon::now()->toDateString()));

        $monthlyUsagesSelect = Message::whereBetween('created_at', [
            (clone $date)->subYear()->startOfMonth(),
            $date,
        ])->when($request->has('accounts'), function (Builder $q) use ($request) {
            $q->whereIn('account_id', $request->input('accounts'));
        })->whereNotIn("status", ['failed', 'pending', 'canceled'])->select([
            'direction',
            DB::raw("IF(mms IS NULL, 'sms', 'mms') type"),
            DB::raw("DATE_FORMAT(created_at,'%Y-%m') as month"),
            DB::Raw('SUM(segments) as count'),
        ])->groupBy([
            'type',
            'direction',
            DB::raw("DATE_FORMAT(created_at,'%Y-%m')"),
        ])->get();

        $dailyUsagesSelect = Message::whereBetween('created_at', [(clone $date)->subMonth()->startOfMonth(), $date])
                                    ->whereNotIn("status", ['failed', 'pending', 'canceled'])
                                    ->when($request->has('accounts'), function (Builder $q) use ($request) {
                                        $q->whereIn('account_id', $request->input('accounts'));
                                    })
                                    ->select([
                                        'direction',
                                        DB::raw("IF(mms IS NULL, 'sms', 'mms') type"),
                                        DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') as day"),
                                        DB::Raw('SUM(segments) as count'),
                                    ])
                                    ->groupBy([
                                        'type',
                                        'direction',
                                        DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"),
                                    ])
                                    ->get();

        $hourlyUsagesSelect = Message::whereBetween('created_at', [
            (clone $date)->startOfDay(),
            (clone $date)->endOfDay(),
        ])->when($request->has('accounts'), function (Builder $q) use ($request) {
            $q->whereIn('account_id', $request->input('accounts'));
        })->select([
            'direction',
            DB::raw("IF(mms IS NULL, 'sms', 'mms') type"),
            DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d %H') as hour"),
            DB::Raw('SUM(segments) as count'),
        ])->whereNotIn("status", ['failed', 'pending', 'canceled'])->groupBy([
            'type',
            'direction',
            DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d %H')"),
        ])->get();

        $lastYearPeriod = new DatePeriod((clone $date)->subYear()->startOfMonth(), new DateInterval('P1M'), $date);

        $lastMonthPeriod = new DatePeriod((clone $date)->subMonth()->startOfMonth(), new DateInterval('P1D'), $date);

        $lastDayPeriod = new DatePeriod((clone $date)->startOfDay(), new DateInterval('PT1H'), (clone $date)->startOfDay()
                                                                                                            ->endOfDay());

        $monthlyUsages = [];
        $dailyUsages   = [];
        $hourlyUsages  = [];

        foreach ($lastYearPeriod as $dt) {
            $monthlyUsages['dates'][]        = $dt->format('Y M');
            $monthlyUsages['inbound_sms'][]  = $monthlyUsagesSelect->where('direction', 'inbound')
                                                                   ->where('type', 'sms')
                                                                   ->where('month', $dt->format('Y-m'))
                                                                   ->first()->count ?? 0;
            $monthlyUsages['outbound_sms'][] = $monthlyUsagesSelect->where('direction', 'outbound')
                                                                   ->where('type', 'sms')
                                                                   ->where('month', $dt->format('Y-m'))
                                                                   ->first()->count ?? 0;
            $monthlyUsages['inbound_mms'][]  = $monthlyUsagesSelect->where('direction', 'inbound')
                                                                   ->where('type', 'mms')
                                                                   ->where('month', $dt->format('Y-m'))
                                                                   ->first()->count ?? 0;
            $monthlyUsages['outbound_mms'][] = $monthlyUsagesSelect->where('direction', 'outbound')
                                                                   ->where('type', 'mms')
                                                                   ->where('month', $dt->format('Y-m'))
                                                                   ->first()->count ?? 0;
        }

        foreach ($lastMonthPeriod as $dt) {
            $dailyUsages['dates'][]        = $dt->format('Y-m-d');
            $dailyUsages['inbound_sms'][]  = $dailyUsagesSelect->where('direction', 'inbound')
                                                               ->where('type', 'sms')
                                                               ->where('day', $dt->format('Y-m-d'))
                                                               ->first()->count ?? 0;
            $dailyUsages['outbound_sms'][] = $dailyUsagesSelect->where('direction', 'outbound')
                                                               ->where('type', 'sms')
                                                               ->where('day', $dt->format('Y-m-d'))
                                                               ->first()->count ?? 0;
            $dailyUsages['inbound_mms'][]  = $dailyUsagesSelect->where('direction', 'inbound')
                                                               ->where('type', 'mms')
                                                               ->where('day', $dt->format('Y-m-d'))
                                                               ->first()->count ?? 0;
            $dailyUsages['outbound_mms'][] = $dailyUsagesSelect->where('direction', 'outbound')
                                                               ->where('type', 'mms')
                                                               ->where('day', $dt->format('Y-m-d'))
                                                               ->first()->count ?? 0;
        }

        foreach ($lastDayPeriod as $dt) {
            $hourlyUsages['dates'][]        = $dt->format('Y-m-d H');
            $hourlyUsages['inbound_sms'][]  = $hourlyUsagesSelect->where('direction', 'inbound')
                                                                 ->where('type', 'sms')
                                                                 ->where('hour', $dt->format('Y-m-d H'))
                                                                 ->first()->count ?? 0;
            $hourlyUsages['outbound_sms'][] = $hourlyUsagesSelect->where('direction', 'outbound')
                                                                 ->where('type', 'sms')
                                                                 ->where('hour', $dt->format('Y-m-d H'))
                                                                 ->first()->count ?? 0;
            $hourlyUsages['inbound_mms'][]  = $hourlyUsagesSelect->where('direction', 'inbound')
                                                                 ->where('type', 'mms')
                                                                 ->where('hour', $dt->format('Y-m-d H'))
                                                                 ->first()->count ?? 0;
            $hourlyUsages['outbound_mms'][] = $hourlyUsagesSelect->where('direction', 'outbound')
                                                                 ->where('type', 'mms')
                                                                 ->where('hour', $dt->format('Y-m-d H'))
                                                                 ->first()->count ?? 0;
        }

        $mUsageChart = Charts::multi($request->input('type', 'line'), 'highcharts')
                             ->title("Last Year Monthly Statistics")
                             ->dimensions(0, 300)
                             ->dataset('Inbound SMS', $monthlyUsages['inbound_sms'])
                             ->dataset('Outbound SMS', $monthlyUsages['outbound_sms'])
                             ->dataset('Inbound MMS', $monthlyUsages['inbound_mms'])
                             ->dataset('Outbound MMS', $monthlyUsages['outbound_mms'])
                             ->labels($monthlyUsages['dates']);

        $dUsageChart = Charts::multi($request->input('type', 'line'), 'highcharts')
                             ->title("Last Month Daily Statistics")
                             ->dimensions(0, 300)
                             ->dataset('Inbound SMS', $dailyUsages['inbound_sms'])
                             ->dataset('Outbound SMS', $dailyUsages['outbound_sms'])
                             ->dataset('Inbound MMS', $dailyUsages['inbound_mms'])
                             ->dataset('Outbound MMS', $dailyUsages['outbound_mms'])
                             ->labels($dailyUsages['dates']);

        $hUsageChart = Charts::multi($request->input('type', 'line'), 'highcharts')
                             ->title("Hourly Statistics")
                             ->dimensions(0, 300)
                             ->dataset('Inbound SMS', $hourlyUsages['inbound_sms'])
                             ->dataset('Outbound SMS', $hourlyUsages['outbound_sms'])
                             ->dataset('Inbound MMS', $hourlyUsages['inbound_mms'])
                             ->dataset('Outbound MMS', $hourlyUsages['outbound_mms'])
                             ->labels($hourlyUsages['dates']);

        return view('pages.reports', compact('mUsageChart', 'dUsageChart', 'hUsageChart', 'accounts', 'request'));
    }
}
