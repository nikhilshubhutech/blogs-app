<?php

namespace App\Models\Email;

use Illuminate\Database\Eloquent\Model;

class EmailOtp extends Model
{
    protected $table = "email_otps";
    protected $fillable = [
        'email', 'otp', 'expires_at',
    ];
}
