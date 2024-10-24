<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;
    protected $fillable = ['id_episode', 'images'];
    protected $casts = [
        'images' => 'array',
    ];

    // Quan hệ: Mỗi Server thuộc về một Episode
    public function episode()
    {
        return $this->belongsTo(Episode::class, 'id_episode');
    }
}
