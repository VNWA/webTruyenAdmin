<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    use HasFactory;
    protected $fillable = ['id_product', 'name', 'slug', 'updated_at'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
    public function servers()
    {
        return $this->hasMany(Server::class, 'id_episode');
    }

}
