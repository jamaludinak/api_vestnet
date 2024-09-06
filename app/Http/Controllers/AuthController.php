<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OtpCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect('administrator/');
        }

        return view('admin.auth.login_index');
    }

    public function login_attempt(Request $request)
    {
        $rules = [
            'username' => 'required|string',
            'password' => 'required|string',
        ];

        $messages = [
            'username.required' => 'Username atau Email wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.string' => 'Password harus berupa string',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $username = $request->username;
        $password = $request->password;
        $remember_me = $request->remember_me ? true : false;

        $user = User::where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if (!$user) {
            Session::flash('error', 'Username atau Email Anda salah');
            return redirect('administrator/login');
        }

        if ($user->role_id != 1) {
            Session::flash('error', 'Anda tidak memiliki akses');
            return redirect('administrator/login');
        }

        $data = [
            'email' => $user->email,
            'password' => $password,
        ];

        if (Auth::attempt($data, $remember_me)) {
            //Login Success
            return redirect('administrator/');
        } else {
            //Login Fail
            Session::flash('error', 'Username atau Password anda salah');
            return redirect('administrator/login');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('administrator/login');
    }

    public function registerMobile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $otp_code = rand(100000, 999999);
        OtpCode::create([
            'user_id' => $user->id_user,
            'otp_code' => $otp_code,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Kirim email verifikasi
        $this->sendVerificationEmail($user, $otp_code);
        

        return response()->json(['message' => 'User registered successfully. Please verify your email.'], 201);
    }

    private function sendVerificationEmail($user, $otp_code)
    {
        Mail::send('emails.verification', ['code' => $otp_code], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Verify Your Email Address');
        });
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'otp_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $otpCode = OtpCode::where('user_id', $user->id_user)
            ->where('otp_code', $request->otp_code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpCode) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $otpCode->update(['verified_at' => Carbon::now()]);

        $user->update([
            'email_verified_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Email verified successfully'], 200);
    }

    public function loginMobile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($fieldType, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Login failed'], 401);
        }

        if (is_null($user->email_verified_at)) {
            return response()->json(['message' => 'Please verify your email before logging in.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer'], 200);
    }

    public function resendOTPEmail(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Cek apakah user dengan email tersebut ada
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Cek apakah email sudah diverifikasi
        if (!is_null($user->email_verified_at)) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        // Buat OTP baru dan simpan di database
        $otp_code = rand(100000, 999999);
        OtpCode::updateOrCreate(
            ['user_id' => $user->id_user],
            [
                'otp_code' => $otp_code,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        // Kirim ulang email verifikasi
        $this->sendVerificationEmail($user, $otp_code);

        return response()->json(['message' => 'OTP resent successfully. Please check your email.'], 200);
    }
}
