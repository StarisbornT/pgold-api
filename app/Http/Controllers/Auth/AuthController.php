<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\OTPService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
      /**
     * Otp service.
     *
     * @var \App\Services\OtpService
     */
    protected $otpService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }
    /**
     * Customer login.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        validator($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ])->validate();

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken($user->email, ["is-customer"]);

        $user->role = $token->accessToken->abilities[0];
        return response()->json([
            'message' => "Customer Login successful",
            'token' => $token->plainTextToken,
            'user' => $user,
        ]);
    }

     /**
     * Logout Customer.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => "Customer logout successfully",
        ]);
    }

}
