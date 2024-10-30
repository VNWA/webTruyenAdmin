<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProType extends Model
{
    use HasFactory;
    protected $fillable = ['type_id', 'product_id'];
    public function products()
    {
        return $this->belongsToMany(Product::class, 'pro_types', 'type_id', 'product_id');
    }
}
