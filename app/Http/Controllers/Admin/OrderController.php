<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Setting;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use App\Models\RefundRequest;
use App\Models\TicketMessage;
use App\Models\CompleteRequest;
use App\Models\OrderProductVariant;
use App\Http\Controllers\Controller;

use App\Models\ProviderClientReport;
use Illuminate\Pagination\Paginator;


class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request){
        Paginator::useBootstrap();

        $orders = Order::with('client','provider', 'user')->orderBy('id','desc');

        if($request->provider){
            $orders = $orders->where('provider_id', $request->provider);
        }

        if($request->client){
            $orders = $orders->where('client_id', $request->client);
        }

        if($request->booking_id){
            $orders = $orders->where('order_id', $request->booking_id);
        }

        $orders = $orders->paginate(15);
        $title = trans('admin_validation.All Order');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        $providers = User::where(['status' => 1, 'is_provider' => 1])->orderBy('name','asc')->get();
        $clients = User::where(['status' => 1, 'is_provider' => 0])->orderBy('name','asc')->get();

        return view('admin.order', compact('orders','title','currency_icon','providers','clients'));
    }
    
    public function pendingOrder(){
        Paginator::useBootstrap();

        $orders = Order::with('user')->where('order_status', 0)->orderBy('id','desc')->paginate(15);
        $title = trans('admin_validation.Pending orders');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        return view('admin.pending_order', compact('orders','title'));
    }

    public function completeOrder(){
        Paginator::useBootstrap();

        $orders = Order::with('user')->where('order_status', 1)->orderBy('id','desc')->paginate(15);
        $title = trans('admin_validation.Complete orders');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        return view('admin.complete_order', compact('orders','title'));
    }

    public function awaitingBooking(Request $request){
        Paginator::useBootstrap();
        $orders = Order::with('client','provider')->orderBy('id','desc')->where('order_status','awaiting_for_provider_approval');

        if($request->provider){
            $orders = $orders->where('provider_id', $request->provider);
        }

        if($request->client){
            $orders = $orders->where('client_id', $request->client);
        }

        if($request->booking_id){
            $orders = $orders->where('order_id', $request->booking_id);
        }

        $orders = $orders->paginate(15);

        $title = trans('admin_validation.Awaiting for approval');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        $providers = User::where(['status' => 1, 'is_provider' => 1])->orderBy('name','asc')->get();
        $clients = User::where(['status' => 1, 'is_provider' => 0])->orderBy('name','asc')->get();

        return view('admin.order', compact('orders','title','currency_icon','providers','clients'));
    }

    public function activeBooking(Request $request){
        Paginator::useBootstrap();
        $orders = Order::with('client','provider')->orderBy('id','desc')->where('order_status','approved_by_provider');

        if($request->provider){
            $orders = $orders->where('provider_id', $request->provider);
        }

        if($request->client){
            $orders = $orders->where('client_id', $request->client);
        }

        if($request->booking_id){
            $orders = $orders->where('order_id', $request->booking_id);
        }

        $orders = $orders->paginate(15);

        $title = trans('admin_validation.Active Booking');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        $providers = User::where(['status' => 1, 'is_provider' => 1])->orderBy('name','asc')->get();
        $clients = User::where(['status' => 1, 'is_provider' => 0])->orderBy('name','asc')->get();

        return view('admin.order', compact('orders','title','currency_icon','providers','clients'));
    }

    public function completeBooking(Request $request){
        Paginator::useBootstrap();
        $orders = Order::with('client','provider')->orderBy('id','desc')->where('order_status','complete');

        if($request->provider){
            $orders = $orders->where('provider_id', $request->provider);
        }

        if($request->client){
            $orders = $orders->where('client_id', $request->client);
        }

        if($request->booking_id){
            $orders = $orders->where('order_id', $request->booking_id);
        }

        $orders = $orders->paginate(15);

        $title = trans('admin_validation.Complete Booking');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        $providers = User::where(['status' => 1, 'is_provider' => 1])->orderBy('name','asc')->get();
        $clients = User::where(['status' => 1, 'is_provider' => 0])->orderBy('name','asc')->get();

        return view('admin.order', compact('orders','title','currency_icon','providers','clients'));
    }

    public function declineBooking(Request $request){
        Paginator::useBootstrap();
        $orders = Order::with('client','provider')->orderBy('id','desc')->where('order_status','order_decliened_by_provider')->orWhere('order_status', 'order_decliened_by_client');

        if($request->provider){
            $orders = $orders->where('provider_id', $request->provider);
        }

        if($request->client){
            $orders = $orders->where('client_id', $request->client);
        }

        if($request->booking_id){
            $orders = $orders->where('order_id', $request->booking_id);
        }

        $orders = $orders->paginate(15);

        $title = trans('admin_validation.Declined Booking');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        $providers = User::where(['status' => 1, 'is_provider' => 1])->orderBy('name','asc')->get();
        $clients = User::where(['status' => 1, 'is_provider' => 0])->orderBy('name','asc')->get();

        return view('admin.order', compact('orders','title','currency_icon','providers','clients'));
    }

    public function show($id){
        $order = Order::with('user')->find($id);
        $setting = Setting::first();
        return view('admin.show_order',compact('order', 'setting'));
    }

    public function updateOrderStatus(Request $request , $id){
        $rules = [
            'order_status' => 'required',
            'payment_status' => 'required',
        ];
        $this->validate($request, $rules);

        $order = Order::with('orderItems.product.productlangfrontend','user','orderItems.author')->find($id);
        if(!$order){
            $notification = trans('admin_validation.Order not found');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

        $oldPaymentStatus = $order->payment_status;

        // === Update order status ===
        if($request->order_status == 0){
            $order->order_status = 0;
        }else if($request->order_status == 1){
            $order->order_status = 1;
        }
        $order->save();

        // === Update payment status ===
        if($request->payment_status == 'pending'){
            $order->payment_status = 'pending';
            $order->save();
        }elseif($request->payment_status == 'success'){
            $order->payment_status = 'success';
            $order->save();

            // === Only send invoice if not already success before ===
            if($oldPaymentStatus !== 'success'){
                try{
                    $setting = Setting::first();
                    \App\Helpers\MailHelper::setMailConfig();

                    // Render invoice view to HTML
                    $html = view('admin.invoice_pdf', compact('order','setting'))->render();

                    $filePath = null;
                    if(class_exists('\Barryvdh\DomPDF\Facade\Pdf')){
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4','portrait');
                        $filename = 'invoices/invoice_'.$order->order_id.'.pdf';
                        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $pdf->output());
                        $filePath = storage_path('app/public/'.$filename);
                    }else{
                        $filename = 'invoices/invoice_'.$order->order_id.'.html';
                        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $html);
                        $filePath = storage_path('app/public/'.$filename);
                    }

                    // Send mail with invoice attachment
                    \Illuminate\Support\Facades\Mail::send('emails.invoice_mail', compact('order','setting'), function($message) use ($order, $filePath){
                        $to = $order->user && $order->user->email ? $order->user->email : ($order->billing_email ?? null);
                        if($to){
                            $message->to($to)->subject(__('Invoice for Order #').$order->order_id);
                            if($filePath && file_exists($filePath)){
                                $message->attach($filePath);
                            }
                        }
                    });
                }catch(\Exception $e){
                    \Log::error('Invoice generation/email failed: '.$e->getMessage());
                }
            }
        }

        $notification = trans('admin_validation.Order Status Updated successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function destroy($id){
        $order = Order::find($id);
        $order_item=OrderItem::where('order_id', $id)->delete();
        $order->delete();
        $notification = trans('admin_validation.Delete successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.all-booking')->with($notification);
    }


    public function bookingDecilendRequest($id){
        $order = Order::find($id);
        $order->order_status = 'order_decliened_by_provider';
        $order->save();

        $notification= trans('admin_validation.Declined Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function bookingApprovedRequest($id){
        $order = Order::find($id);
        $order->order_status = 'approved_by_provider';
        $order->save();

        $notification= trans('admin_validation.Approved Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    
    public function paymentApproved($id){
        $order = Order::with('orderItems.product.productlangfrontend','user','orderItems.author')->find($id);
        if(!$order){
            $notification = trans('admin_validation.Order not found');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

        $order->payment_status = 'success';
        $order->save();

        // Generate invoice PDF and send to buyer (requires barryvdh/laravel-dompdf)
        try{
            $setting = Setting::first();
            // Ensure mail config is set from DB
            \App\Helpers\MailHelper::setMailConfig();

            // Render invoice view to HTML
            $html = view('admin.invoice_pdf', compact('order','setting'))->render();

            // Generate PDF (note: requires barryvdh/laravel-dompdf or another PDF generator)
            // If you have installed barryvdh/laravel-dompdf, the facade PDF should be available.
            if(class_exists('\Barryvdh\DomPDF\Facade\Pdf') || class_exists('\Barryvdh\DomPDF\Facade\PDF')){
                // Try both common facades
                if(class_exists('\Barryvdh\DomPDF\Facade\Pdf')){
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4','portrait');
                }else{
                    $pdf = \Barryvdh\DomPDF\Facade\PDF::loadHTML($html)->setPaper('a4','portrait');
                }
                $filename = 'invoices/invoice_'.$order->order_id.'.pdf';
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $pdf->output());
                $filePath = storage_path('app/public/'.$filename);
            }else{
                // Fallback: save HTML invoice (not PDF) if no PDF lib installed
                $filename = 'invoices/invoice_'.$order->order_id.'.html';
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $html);
                $filePath = storage_path('app/public/'.$filename);
            }

            // Send mail with attachment to buyer
            try{
                \Illuminate\Support\Facades\Mail::send('emails.invoice_mail', compact('order','setting'), function($message) use ($order, $filePath){
                    $to = $order->user && $order->user->email ? $order->user->email : ($order->billing_email ?? null);
                    if(!$to){
                        return;
                    }
                    $message->to($to)->subject(__('Invoice for Order #').$order->order_id);
                    $message->attach($filePath);
                });
            }catch(\Exception $e){
                \Log::error('Invoice mail sending failed: '.$e->getMessage());
            }

        }catch(\Exception $e){
            \Log::error('Invoice generation error: '.$e->getMessage());
        }

        $notification= trans('admin_validation.Approved Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


}
