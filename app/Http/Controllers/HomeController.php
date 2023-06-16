<?php
namespace App\Http\Controllers;

use App\Models\PhoneNumber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
            $phone_number->valid_until = now()->addMinutes(1); // Set the OTP expiration time
            $phone_number->save();

            // Perform any action to send the OTP to the user's phone

            return redirect()->route('verify_phone_otp', ['phone' => $validatedData['phone']]);
        }

        return view('phone');
    }

    private function generateOTP()
    {
        // Generate a 6-digit OTP
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
            if ($phone_number->otp == $otp) {
                // Phone and OTP match
                return redirect()->route('register');
            } else {
                // Phone and OTP do not match
                if ($phone_number->valid_until >= Carbon::now()) {
                    // OTP is still valid, show the remaining time
                    $validUntil = Carbon::parse($phone_number->valid_until);
                    $remainingTime = max(0, $validUntil->diffInSeconds(Carbon::now()));
                } else {
                    // OTP has expired, remove the remaining time
                    $remainingTime = 0;
                }

                return back()
                    ->withErrors(['otp' => 'Invalid OTP'])
                    ->withInput(['phone' => $phone])
                    ->with('remainingTime', $remainingTime);
            }
        }

        // Phone number not found
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

        $errors = $request->session()->get('errors');

        if ($remainingTime <= 0) {
            $remainingTime = 0;
        }

        return view('verify_phone_otp', compact('remainingTime', 'phone', 'errors'));
    }
}
