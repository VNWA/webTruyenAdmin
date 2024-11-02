<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Server extends Model
{
    use HasFactory;

    protected $fillable = ['episode_id', 'images'];

    // Tự động chuyển JSON thành mảng khi truy xuất
    protected $casts = [
        'images' => 'array',
    ];

    /**
     * Boot method để đăng ký sự kiện deleting
     */
    protected static function boot()
    {
        parent::boot();

        // Sự kiện deleting để xóa các ảnh trước khi bản ghi bị xóa
        static::deleting(function ($server) {
            $server->deleteImages();
        });
    }

    /**
     * Xóa các ảnh thuộc về tên miền của website trong cột images.
     */
    public function deleteImages()
    {
        // Kiểm tra nếu images là một mảng
        if (is_array($this->images)) {
            foreach ($this->images as $url) {
                // Kiểm tra nếu URL bắt đầu bằng tên miền của bạn
                if (strpos($url, env('APP_URL') . '/storage/images/thumb/') === 0) {
                    // Lấy phần đường dẫn sau tên miền để xóa từ Storage
                    $path = str_replace(env('APP_URL') . '/storage/', '', $url);
                    Storage::delete('public/' . $path); // Đảm bảo đường dẫn khớp với cấu trúc lưu trữ của bạn
                }
            }
        }
    }

    /**
     * Quan hệ: Mỗi Server thuộc về một Episode
     */
    public function episode()
    {
        return $this->belongsTo(Episode::class, 'episode_id');
    }
}
