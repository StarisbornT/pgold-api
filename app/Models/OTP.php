<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OTP extends Model
{
    use HasFactory;
    protected $fillable = [
        'identifier',
        'otp',
        'expired_at',
    ];
}