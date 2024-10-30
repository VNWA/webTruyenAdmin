<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'slug'];

    protected $appends = ['update_time'];

    protected static function boot()
    {
        parent::boot();

        // Khi một Episode được tạo
        static::created(function ($episode) {
            if ($episode->product) {
                $episode->product->touch(); // Cập nhật updated_at của Product
            }
        });

        // Khi một Episode được cập nhật
        static::updated(function ($episode) {
            if ($episode->product) {
                $episode->product->touch(); // Cập nhật updated_at của Product
            }
        });
    }

    public function getUpdateTimeAttribute()
    {
        // Lấy thời gian updated_at
        $updatedAt = $this->updated_at;

        // Nếu không có thời gian cập nhật, trả về null
        if (!$updatedAt) {
            return null;
        }

        // Tính toán sự khác biệt giữa thời gian hiện tại và updated_at
        $diffInSeconds = Carbon::now()->diffInSeconds($updatedAt);

        if ($diffInSeconds < 60) {
            return "{$diffInSeconds} seconds ago"; // 1 giây trước
        } elseif ($diffInSeconds < 3600) {
            $diffInMinutes = floor($diffInSeconds / 60);
            return "{$diffInMinutes} minutes ago"; // 1 phút trước
        } elseif ($diffInSeconds < 86400) {
            $diffInHours = floor($diffInSeconds / 3600);
            return "{$diffInHours} hours ago"; // 1 giờ trước
        } elseif ($updatedAt->isToday()) {
            return "Today at " . $updatedAt->format('H:i'); // Hôm nay lúc
        } elseif ($updatedAt->isYesterday()) {
            return "Yesterday at " . $updatedAt->format('H:i'); // Hôm qua lúc
        } else {
            return $updatedAt->format('d/m/Y'); // Ngày/tháng/năm
        }
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function servers()
    {
        return $this->hasMany(Server::class, 'episode_id');
    }
}
