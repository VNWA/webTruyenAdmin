<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appearance extends Model
{
    use HasFactory;
    protected $fillable = ['type', 'value'];


    protected $casts = [
        'value' => 'array', // Chuyển các trường JSON thành mảng
    ];
}
