<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawMethod;
use App\Models\ProviderWithdraw;
use App\Models\Setting;
use App\Models\EmailTemplate;
use App\Helpers\MailHelper;
use App\Mail\ProviderWithdrawApproval;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Auth;
use PDF;

class ProviderWithdrawController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $withdraws = ProviderWithdraw::with('provider')->orderBy('id', 'desc')->get();

        if ($request->provider_id) {
            $withdraws = $withdraws->where('user_id', $request->provider_id);
        }
        $setting = Setting::first();

        return view('admin.provider_withdraw', compact('withdraws', 'setting'));
    }

    public function pendingProviderWithdraw()
    {
        $withdraws = ProviderWithdraw::orderBy('id', 'desc')->where('status', 0)->get();
        $setting = Setting::first();
        return view('admin.provider_withdraw', compact('withdraws', 'setting'));
    }

    public function show($id)
    {
        $setting = Setting::first();
        $withdraw = ProviderWithdraw::find($id);
        return view('admin.show_provider_withdraw', compact('withdraw', 'setting'));
    }

    public function destroy($id)
    {
        $withdraw = ProviderWithdraw::find($id);
        $withdraw->delete();
        $notification = trans('admin_validation.Delete Successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->route('admin.provider-withdraw')->with($notification);
    }

    public function approvedWithdraw($id)
    {
        try {
            $withdraw = ProviderWithdraw::find($id);
            $withdraw->status = 1;
            $withdraw->approved_date = date('Y-m-d');
            $withdraw->save();

            $setting = Setting::first();
            $user = $withdraw->provider;

            // ========================
            // Email Template
            // ========================
            $template = EmailTemplate::where('id', 5)->first();
            $message = $template->description;
            $subject = $template->subject;
            $message = str_replace('{{seller_name}}', $user->name, $message);
            $message = str_replace('{{withdraw_method}}', $withdraw->method, $message);
            $message = str_replace('{{total_amount}}', $setting->currency_icon . $withdraw->total_amount, $message);
            $message = str_replace('{{withdraw_charge}}', $setting->currency_icon . ($withdraw->total_amount - $withdraw->withdraw_amount), $message);
            $message = str_replace('{{withdraw_amount}}', $setting->currency_icon . $withdraw->withdraw_amount, $message);
            $message = str_replace('{{approval_date}}', $withdraw->approved_date, $message);

            // ========================
            // PDF Data
            // ========================
            $pdfData = [
                'withdraw' => $withdraw,
                'user' => $user,
                'setting' => $setting,
            ];

            $fileName = 'withdraw_report_' . $withdraw->id . '.pdf';

            // load Blade view into PDF with safe font
            $pdf = PDF::loadView('admin.withdraw_report', $pdfData)
                ->setOptions(['defaultFont' => 'Arial Sans']);

            // ========================
            // Save PDF in Storage
            // ========================
            $storagePath = 'public/withdraw_reports/' . $fileName;
            Storage::put($storagePath, $pdf->output());

            // যদি DB column থাকে তবে path save করো
            if (Schema::hasColumn('provider_withdraws', 'report_path')) {
                $withdraw->report_path = 'withdraw_reports/' . $fileName; // storage/app/public/withdraw_reports
                $withdraw->save();
            }

            // ========================
            // Send Email with attachment
            // ========================
            MailHelper::setMailConfig();
            $mailable = (new ProviderWithdrawApproval($subject, $message))
                ->attachData($pdf->output(), $fileName, [
                    'mime' => 'application/pdf',
                ]);

            Mail::to($user->email)->send($mailable);

        } catch (\Exception $e) {
            \Log::error('Mail/PDF send error: ' . $e->getMessage());
        }

        $notification = trans('admin_validation.Withdraw request approval successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->route('admin.provider-withdraw')->with($notification);
    }
}
