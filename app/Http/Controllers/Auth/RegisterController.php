<?php

namespace App\Http\Controllers\Auth;

use Throwable;
use App\Models\User;
use App\Mail\VerifyMail;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RegisterRequest;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{

    public function __construct(
        protected OTPService $otpService,
    ) {
    }
     /**
     * Influencer register.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $attributes = $request->validated();
        $userWithEmailAlreadyExists = User::where("email", $request->email)
            ->first();
        $userWithPhoneNumberAlreadyExists = User::where("phone_number", $request->phone_number)
            ->first();
        if ($userWithPhoneNumberAlreadyExists) {
            throw ValidationException::withMessages([
                'phone_number' => ['User with phone number already exists'],
            ]);
        }
        if ($userWithEmailAlreadyExists) {
            throw ValidationException::withMessages([
                'email' => ['User with email number already exists'],
            ]);
        }

        $attributes['password'] = Hash::make($attributes['password']);
        DB::beginTransaction();
        try {
            $user = User::create($attributes);
            $otpObject = $this->otpService->generate($user->email);
            $otpCode = $otpObject->token;
            Mail::to($request->email)->send(new VerifyMail($otpCode));
            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            return response()->json([
                'message' => "Customer Register failed" . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => "Customer Register successful",
            'user' => $user,
        ]);
    }

     /**
     * Verify influencer account.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
        $validateOtp = $this->otpService->validate($user->email, $request->otp);
        if (!$validateOtp->status) {
            return response()->json([
                'message' => $validateOtp->message,
            ], 400);
        }
        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Account already verified',
            ], 400);
        }
        $user->email_verified_at = now();
        $user->save();

        $token = $user->createToken($user->email, ["is-customer"])->plainTextToken;
        return response()->json([
            'message' => "Customer verified and Login successful",
            'token' => $token,
            'data' => $user,
        ]);
    }

    /**
     * Resend influencer verification otp.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
        $otpObject = $this->otpService->generate($user->email);
            $otpCode = $otpObject->token;
        Mail::to($user->email)->send(new VerifyMail($otpCode));
        return response()->json([
            'message' => "Customer verification otp sent",
        ]);
    }
}