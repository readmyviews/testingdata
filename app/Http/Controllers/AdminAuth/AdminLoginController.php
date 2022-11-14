<?php

namespace App\Http\Controllers\AdminAuth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Providers\RouteServiceProvider;
use App\Mail\SuperAdmin\SuperAdminLogin;
use App\Http\Requests\AdminAuth\AdminLoginRequest;
use App\Http\Requests\AdminAuth\AdminLoginOtpRequest;

class AdminLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginEmailForm()
    {
        return view('admin-auth.login-email');
    }

    public function sendLoginOtp(AdminLoginRequest $request)
    {
        $userEmail = User::where('email', $request->email)->first();
        if (!empty($userEmail)) {
            if (in_array($userEmail->user_role, Config::get('params.admin_role_id'))) {
                $otp = rand(100000, 999999);
                $userEmail->admin_login_otp = $otp;
                $userEmail->admin_login_otp_sent_at = Carbon::now()->toDateTimeString();
                $userEmail->save();
                // email notification for otp
                $data['otp'] = $otp;
                $data['name'] = $userEmail->first_name;
                Mail::to($userEmail->email)->send(new SuperAdminLogin($data));
                $adminEmail = $userEmail->email;
                $request->session()->put('adminEmail', $adminEmail);

                $successMessage = trans('admin.message.otp_sent');
                if (isset($request->is_resend) && $request->is_resend == 1) {
                    $successMessage = trans('admin.message.otp_resent');
                }
                return redirect()->route('admin.login.otp.form')->with('success', $successMessage);
            }
            return redirect()->back()->with('error', trans('admin.message.email_not_exist'));
        }
        return redirect()->back()->with('error', trans('admin.message.email_not_exist'));
    }

    public function showLoginOtpForm(Request $request)
    {
        if ($request->session()->has('adminEmail')) {
            $email =  $request->session()->get('adminEmail');
        } else {
            return redirect()->route('admin.login.email.form')->with('error', trans('admin.message.something_wrong'));
        }
        $expireTime = Config::get('params.super_admin_login_otp_expire');

        return view('admin-auth.login-otp', compact('email', 'expireTime'));
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(AdminLoginOtpRequest $request)
    {
        $email = $request->session()->get('adminEmail');
        $data = $request->validated();
        $login = User::where(['email' => $email, 'admin_login_otp' => $data['otp']])->first();
        if (!empty($login)) {
            $expireTime = Config::get('params.super_admin_login_otp_expire');
            $otpAt = Carbon::createFromFormat('Y-m-d H:i:s', $login->admin_login_otp_sent_at)->addMinute($expireTime);
            $verifyTime = Carbon::now();
            if ($verifyTime->lte($otpAt)) {
                Auth::login($login, true);
                $login->update(['admin_login_otp' => null, 'admin_login_otp_sent_at' => null]);
                $request->session()->forget('adminEmail');
                return redirect(RouteServiceProvider::HOME)->with('success', trans('admin.message.login_success'));
            }
            return redirect()->back()->with('error', trans('admin.message.otp_expired'));
        } else {
            return redirect()->route('admin.login.otp.form')->with('error', trans('admin.message.invalid_otp'));
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
