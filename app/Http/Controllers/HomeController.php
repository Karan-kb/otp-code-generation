<?php
namespace App\Http\Controllers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;


use App\Models\PhoneNumber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $validatedData = $request->validate([
                'phone' => 'required|string',
            ]);

            $phone_number = new PhoneNumber;
            $phone_number->phone = $validatedData['phone'];
            $phone_number->otp = $this->generateOTP();

            $expirationTime = Carbon::now()->addDay();
            $phone_number->valid_until = $expirationTime;

            $phone_number->save();

            return redirect()->route('verify_phone_otp', ['phone' => $validatedData['phone']]);
        }

        return view('phone');
    }

    private function generateOTP()
    {
        return rand(100000, 999999);
    }

    public function verify(Request $request)
    {
        $validatedData = $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        $phone = $validatedData['phone'];
        $otp = $validatedData['otp'];

        $phone_number = PhoneNumber::where('phone', $phone)
            ->where('valid_until', '>=', Carbon::now())
            ->first();

        if ($phone_number) {
            if ($phone_number->otp == $otp && $phone_number->valid_until >= Carbon::now()) {
                return redirect()->route('register');
            } else {
                return back()
                    ->withErrors(['otp' => 'Invalid OTP'])
                    ->withInput(['phone' => $phone]);
            }
        }

        return back()
            ->withErrors(['otp' => 'Invalid OTP'])
            ->withInput(['phone' => $phone]);
    }

    public function showHome()
    {
        return view('home');
    }

    public function showVerifyPhoneOtp(Request $request)
    {
        $phone = $request->input('phone');

        if (!$phone) {
            return redirect()->route('home')->withErrors(['phone' => 'Phone number not provided']);
        }

        $phone_number = PhoneNumber::where('phone', $phone)->first();

        if (!$phone_number) {
            return redirect()->route('home')->withErrors(['phone' => 'Phone number not found']);
        }

        $validUntil = Carbon::parse($phone_number->valid_until);
        $remainingTime = max(0, $validUntil->diffInSeconds(Carbon::now()));

        if ($remainingTime <= 0) {
            $phone_number->delete();
            $remainingTime = 0;
        }

        $errors = $request->session()->get('errors');

        return view('verify_phone_otp', compact('remainingTime', 'phone', 'errors'));
    }

    // New method to invalidate expired OTPs
    public function invalidateExpiredOtps()
    {
        DB::table('phone_numbers')->where('valid_until', '<', Carbon::now())->delete();
    }
}
