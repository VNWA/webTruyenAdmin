<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory, HasApiTokens, Notifiable; // Sử dụng trait này
    protected $fillable = [
        'username',
        'email',
        'password',
        'otp',
        'otp_expires_at',
    ];
}
