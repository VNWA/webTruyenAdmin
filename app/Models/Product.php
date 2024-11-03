<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['category_id','nation_id', 'is_end', 'views',  'status', 'highlight', 'rating_qnt', 'url_avatar', 'url_bg', 'date', 'full_name', 'name', 'slug', 'desc', 'meta_image', 'meta_title', 'meta_desc'];
    protected $appends = ['newEpisode','countWishlist'];
    public $timestamps = true;
    public function getCountWishlistAttribute()
    {
        // Sử dụng collection để đếm thay vì query lại từ DB
        return $this->wishlists->count();
    }
    public function incrementViews()
    {
        $this->timestamps = false; // Tạm thời tắt cập nhật timestamps
        $this->increment('views'); // Tăng giá trị của trường views
        $this->timestamps = true; //
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
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
        return $this->hasMany(Episode::class, 'product_id')->orderByDesc('id')->take(2);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }
    public function nation()
    {
        return $this->belongsTo(Nation::class, 'nation_id');
    }
    public function year()
    {
        return $this->belongsTo(Year::class, 'year_id');
    }
    public function types()
    {
        return $this->belongsToMany(Type::class, 'pro_types', 'product_id', 'type_id');
    }
    public function episodes()
    {
        return $this->hasMany(Episode::class, 'product_id')->orderByDesc('id');

    }
    public function product_banner()
    {
        return $this->hasOne(ProductBanner::class, 'product_id');
    }
}
