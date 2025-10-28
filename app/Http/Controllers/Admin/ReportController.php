<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ProviderWithdraw;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'daily');
        $from = $request->get('from');
        $to   = $request->get('to');

        // Calculate start and end dates based on type or provided from/to
        if ($from && $to) {
            $start = Carbon::parse($from)->startOfDay();
            $end   = Carbon::parse($to)->endOfDay();
        } else {
            if ($type === 'monthly') {
                $start = Carbon::now()->startOfMonth();
                $end   = Carbon::now()->endOfMonth();
            } elseif ($type === 'yearly') {
                $start = Carbon::now()->startOfYear();
                $end   = Carbon::now()->endOfYear();
            } else {
                $start = Carbon::today();
                $end   = Carbon::today()->endOfDay();
            }
        }

        // === KPI Cards ===
        $total_orders = Order::whereBetween('created_at', [$start, $end])->count();

        $total_earning = Order::where('payment_status','success')
            ->where('order_status',1)
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        // Withdraw Breakdown with calculated charge
        $withdraws = ProviderWithdraw::with('provider')
            ->where('status',1)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at','asc')
            ->get()
            ->map(function($w){
                $w->calculated_charge = ($w->total_amount * $w->withdraw_charge) / 100;
                return $w;
            });

        $total_withdraw_amount = $withdraws->sum('total_amount');
        $total_withdraw_charge = $withdraws->sum('calculated_charge');

        // === Sales / Orders Breakdown ===
        $groupFormat = '%Y-%m-%d';
        if ($type === 'monthly') $groupFormat = '%Y-%m';
        if ($type === 'yearly')  $groupFormat = '%Y';

        $sales = Order::selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as period")
            ->selectRaw("SUM(total_amount) as earnings")
            ->where('payment_status','success')
            ->where('order_status',1)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('period')
            ->pluck('earnings','period')
            ->toArray();

        $ordersCount = Order::selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as period")
            ->selectRaw("COUNT(*) as orders")
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('period')
            ->pluck('orders','period')
            ->toArray();

        $profits = ProviderWithdraw::selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as period")
            ->selectRaw("SUM(withdraw_charge) as profit")
            ->where('status',1)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('period')
            ->pluck('profit','period')
            ->toArray();

        $allPeriods = collect(array_merge(array_keys($sales), array_keys($ordersCount), array_keys($profits)))->unique()->sort();

        $breakdown = Order::with('user')
            ->where('payment_status','success')
            ->where('order_status',1)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at','asc')
            ->get();

        // Set $from and $to for view input fields and display
        $from_display = $start->format('Y-m-d');
        $to_display   = $end->format('Y-m-d');

        return view('admin.reports.sales', [
            'type'                  => $type,
            'from'                  => $from_display,
            'to'                    => $to_display,
            'total_orders'          => $total_orders,
            'total_earning'         => $total_earning,
            'total_withdraw_amount' => $total_withdraw_amount,
            'withdraw_charge'       => $total_withdraw_charge,
            'breakdown'             => $breakdown,
            'withdraws'             => $withdraws,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $type = $request->get('type', 'daily');
        $from = $request->get('from');
        $to   = $request->get('to');

        if ($from && $to) {
            $start = Carbon::parse($from)->startOfDay();
            $end   = Carbon::parse($to)->endOfDay();
        } else {
            if ($type === 'monthly') {
                $start = Carbon::now()->startOfMonth();
                $end   = Carbon::now()->endOfMonth();
            } elseif ($type === 'yearly') {
                $start = Carbon::now()->startOfYear();
                $end   = Carbon::now()->endOfYear();
            } else {
                $start = Carbon::today();
                $end   = Carbon::today()->endOfDay();
            }
        }

        $groupFormat = '%Y-%m-%d';
        if ($type === 'monthly') $groupFormat = '%Y-%m';
        if ($type === 'yearly')  $groupFormat = '%Y';

        $sales = Order::selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as period")
            ->selectRaw("SUM(total_amount) as earnings")
            ->where('payment_status','success')
            ->where('order_status',1)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('period')
            ->pluck('earnings','period')
            ->toArray();

        $ordersCount = Order::selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as period")
            ->selectRaw("COUNT(*) as orders")
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('period')
            ->pluck('orders','period')
            ->toArray();

        $profits = ProviderWithdraw::selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as period")
            ->selectRaw("SUM(withdraw_charge) as profit")
            ->where('status',1)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('period')
            ->pluck('profit','period')
            ->toArray();

        $allPeriods = collect(array_merge(array_keys($sales), array_keys($ordersCount), array_keys($profits)))->unique()->sort();

        $rows = $allPeriods->map(function($p) use ($sales,$ordersCount,$profits){
            return [
                'period'   => $p,
                'orders'   => $ordersCount[$p] ?? 0,
                'earnings' => $sales[$p] ?? 0,
                'profit'   => $profits[$p] ?? 0,
            ];
        });

        $filename = 'sales_report_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Period', 'Orders', 'Earnings', 'Profits']);
            foreach ($rows as $r) {
                fputcsv($out, [$r['period'], $r['orders'], $r['earnings'], $r['profit']]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
