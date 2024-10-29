<?php

namespace App\Models;

use Hash;
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
    public function notifications()
    {
        return $this->hasMany(CustomerNotification::class);
    }

    // A customer can have many products in their wishlist.
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Mutator for hashing the password before saving.
     */


}
