<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'social_id',
        'social_provider',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}