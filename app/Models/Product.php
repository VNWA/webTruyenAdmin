<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['is_18', 'id_year', 'views', 'id_nation', 'status', 'highlight', 'rating_qnt', 'url_avatar', 'url_bg', 'date', 'full_name', 'name', 'slug', 'desc', 'meta_image', 'meta_title', 'meta_desc'];
    protected $appends = ['newEpisode'];
    public function incrementViews()
    {
        $this->increment('views'); // Tăng giá trị của trường views
    }
    public function getNewEpisodeAttribute()
    {
        $episodes = $this->latestEpisodes()->get();

        if ($episodes->isNotEmpty()) {
            // Trả về danh sách chứa cả name và slug của từng tập
            return $episodes->map(function ($episode) {
                return [
                    'name' => $episode->name,
                    'slug' => $episode->slug,
                ];
            })->reverse()->values();
        }

        return [];
    }

    public function latestEpisodes()
    {
        return $this->hasMany(Episode::class, 'id_product')->latest()->take(2);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }
    public function nation()
    {
        return $this->belongsTo(Nation::class, 'id_nation');
    }
    public function year()
    {
        return $this->belongsTo(Year::class, 'id_year');
    }
    public function types()
    {
        return $this->belongsToMany(Type::class, 'pro_types', 'id_product', 'id_type');
    }
    public function episodes()
    {
        return $this->hasMany(Episode::class, 'id_product')->latest();

    }
    public function product_banner()
    {
        return $this->hasOne(ProductBanner::class, 'id_product');
    }
}
