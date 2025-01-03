<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerNotification extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'is_view',
        'message',
        'link'
    ];
}
