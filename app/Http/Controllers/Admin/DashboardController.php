<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Subscriber;

use Illuminate\Http\Request;
use App\Models\RefundRequest;
use App\Models\ProviderWithdraw;
use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function dashobard(){

        // ===================== TODAY =====================
        $today_total_order = Order::whereDate('created_at', Carbon::today())->count();

        $todayProduct = Product::whereDate('created_at', Carbon::today())->get();
        $today_total_product    = $todayProduct->count();
        $today_pending_product  = $todayProduct->where('status',0)->count();
        $today_approved_product = $todayProduct->where('status',1)->count();

        $today_total_earning = Order::whereDate('created_at', Carbon::today())
            ->where('payment_status','success')
            ->where('order_status',1)
            ->sum('total_amount');

        $today_withdraws = ProviderWithdraw::whereDate('created_at', Carbon::today())->get();
        $today_withdraw_request  = $today_withdraws->sum('total_amount');
        $today_withdraw_approved = $today_withdraws->where('status', 1)->sum('total_amount');

        $today_users = User::whereDate('created_at', Carbon::today())->count();

        // ===================== MONTHLY =====================
        $monthly_total_order = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $monthlyProduct = Product::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();
        $monthly_total_product    = $monthlyProduct->count();
        $monthly_pending_product  = $monthlyProduct->where('status',0)->count();
        $monthly_approved_product = $monthlyProduct->where('status',1)->count();

        $monthly_total_earning = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status','success')
            ->where('order_status',1)
            ->sum('total_amount');

        $monthly_withdraws = ProviderWithdraw::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();
        $monthly_withdraw_request  = $monthly_withdraws->sum('total_amount');
        $monthly_withdraw_approved = $monthly_withdraws->where('status', 1)->sum('total_amount');

        $monthly_users = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ===================== YEARLY =====================
        $yearly_total_order = Order::whereYear('created_at', now()->year)->count();

        $yearlyProduct = Product::whereYear('created_at', now()->year)->get();
        $yearly_total_product    = $yearlyProduct->count();
        $yearly_pending_product  = $yearlyProduct->where('status',0)->count();
        $yearly_approved_product = $yearlyProduct->where('status',1)->count();

        $yearly_total_earning = Order::whereYear('created_at', now()->year)
            ->where('payment_status','success')
            ->where('order_status',1)
            ->sum('total_amount');

        $yearly_withdraws = ProviderWithdraw::whereYear('created_at', now()->year)->get();
        $yearly_withdraw_request  = $yearly_withdraws->sum('total_amount');
        $yearly_withdraw_approved = $yearly_withdraws->where('status', 1)->sum('total_amount');

        $yearly_users = User::whereYear('created_at', now()->year)->count();

        // ===================== TOTAL =====================
        $total_total_order = Order::count();

        $totalProduct = Product::all();
        $total_total_product    = $totalProduct->count();
        $total_pending_product  = $totalProduct->where('status',0)->count();
        $total_approved_product = $totalProduct->where('status',1)->count();

        $total_total_earning = Order::where('payment_status','success')
            ->where('order_status',1)
            ->sum('total_amount');

        $total_withdraws = ProviderWithdraw::all();
        $total_withdraw_request  = $total_withdraws->sum('total_amount');
        $total_withdraw_approved = $total_withdraws->where('status', 1)->sum('total_amount');

        $total_users = User::count();

        $total_blog       = Blog::count();
        $total_subscriber = Subscriber::where('is_verified',1)->count();

        // seller & client
        $author_products = Product::select('author_id')->get();
        $author_id_arr = [];
        foreach($author_products as $author_product){
            $author_id_arr[]=$author_product->author_id;
        }
        $author_id_arr = array_unique($author_id_arr);

        $total_sellers = User::whereIn('id', $author_id_arr)
            ->where('status', 1)
            ->orderBy('name','asc')
            ->count();

        $total_clients = User::where('status', 1)->orderBy('name','asc')->count();

        $setting = Setting::first();
        $currency_icon = (object) array('icon' => $setting->currency_icon);

        // ===================== Withdraw Charges =====================
        $today_withdraw_charge = DB::table('provider_withdraws')
            ->whereDate('created_at', Carbon::today())
            ->sum('withdraw_charge');

        $monthly_withdraw_charge = DB::table('provider_withdraws')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('withdraw_charge');

        $yearly_withdraw_charge = DB::table('provider_withdraws')
            ->whereYear('created_at', now()->year)
            ->sum('withdraw_charge');

        $total_withdraw_charge = DB::table('provider_withdraws')->sum('withdraw_charge');

        return view('admin.dashboard', compact(
            'currency_icon',

            // today
            'today_total_order',
            'today_total_product',
            'today_pending_product',
            'today_approved_product',
            'today_total_earning',
            'today_withdraw_request',
            'today_withdraw_approved',
            'today_users',

            // monthly
            'monthly_total_order',
            'monthly_total_product',
            'monthly_pending_product',
            'monthly_approved_product',
            'monthly_total_earning',
            'monthly_withdraw_request',
            'monthly_withdraw_approved',
            'monthly_users',

            // yearly
            'yearly_total_order',
            'yearly_total_product',
            'yearly_pending_product',
            'yearly_approved_product',
            'yearly_total_earning',
            'yearly_withdraw_request',
            'yearly_withdraw_approved',
            'yearly_users',

            // total
            'total_total_order',
            'total_total_product',
            'total_pending_product',
            'total_approved_product',
            'total_total_earning',
            'total_withdraw_request',
            'total_withdraw_approved',
            'total_users',
            'total_blog',
            'total_subscriber',
            'total_sellers',
            'total_clients',

            // charges
            'today_withdraw_charge',
            'monthly_withdraw_charge', 
            'yearly_withdraw_charge',
            'total_withdraw_charge'
        ));
    }
}
