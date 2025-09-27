<?php

namespace App\Services;

use App\Models\OTP;
class OTPService
{
  /**
   * Create a new OTP instance.
   *
   * @return void
   */
  public function __construct()
  {
      //
  }

  /**
   * Generate a new otp.
   * @param  string $identifier
   * @param  int $length
   * @param  int $validity
   * @return OTP
   */
  public function generate(string $identifier, int $length = 4, int $validity = 10){
      // check if otp already exists
      OTP::where('identifier', $identifier)->delete();
        // generate otp
       $token =  str_pad($this->generateOTP($length), $length, '0', STR_PAD_LEFT);
        // create otp
        $otp = OTP::create([
            'identifier' => $identifier,
            'otp' => $token,
            'expired_at' => now()->addMinutes($validity),
        ]);

        return (object)[
            'status' => true,
            'token' => $otp->otp,
            'expired_at' => $otp->expired_at,
            'created_at' => $otp->created_at,
            'message' => 'OTP generated'
        ];
  }

      /**
     * @param string $identifier
     * @param string $otp
     * @return mixed
     */
    public function validate(string $identifier, string $otp) : object
    {
        $otp = OTP::where('identifier', $identifier)->where('otp', $otp)->first();

        if ($otp == null) {
            return (object)[
                'status' => false,
                'message' => 'OTP does not exist'
            ];
        } else {
            if ($otp->valid == true) {
                if ($otp->expired_at < now()) {
                    $otp->valid = false;
                    $otp->save();
                    return (object)[
                        'status' => false,
                        'message' => 'OTP Expired'
                    ];
                } else {
                    $otp->valid = false;
                    $otp->save();

                    return (object)[
                        'status' => true,
                        'message' => 'OTP is valid'
                    ];
                }
            } else {
                return (object)[
                    'status' => false,
                    'message' => 'OTP is not valid'
                ];
            }
        }
    }


          /**
     * @param string $identifier
     * @param string $otp
     * @return mixed
     */
    public function check(string $identifier, string $otp) : object
    {
        $otp = OTP::where('identifier', $identifier)->where('otp', $otp)->first();

        if ($otp == null) {
            return (object)[
                'status' => false,
                'message' => 'OTP does not exist'
            ];
        } else {
            if ($otp->valid == true) {
                if ($otp->expired_at < now()) {
                    $otp->valid = false;
                    $otp->save();
                    return (object)[
                        'status' => false,
                        'message' => 'OTP Expired'
                    ];
                } else {
                    // $otp->valid = false;
                    // $otp->save();

                    return (object)[
                        'status' => true,
                        'message' => 'OTP is valid'
                    ];
                }
            } else {
                return (object)[
                    'status' => false,
                    'message' => 'OTP is not valid'
                ];
            }
        }
    }


    /**
     * Generate a new otp.
     * @param  int $length
     * @return string
     */
    public function generateOTP(int $length = 6){
        $i = 0;
        $pin = "";

        while ($i < $length) {
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }
}