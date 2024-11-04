<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerNotification;
use App\Models\Product;
use App\Models\Wishlist;
use Hash;
use Str;
use Validator;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
class AuthCustomerSocialController extends Controller
{
    private function generateUniqueUsername()
    {
        do {
            // Tạo username ngẫu nhiên bằng cách sử dụng Str::random
            $username = 'user_' . Str::random(5); // Bạn có thể thay đổi prefix hoặc cách tạo username
        } while (Customer::where('username', $username)->exists()); // Kiểm tra tính duy nhất

        return $username; // Trả về username duy nhất
    }

    public function socialLoginServices()
    {
        $services = [];
        $services['google'] = [
            'image' => env('APP_URL') . "/images/social/google.webp",
            'text' => "Continute with Google",
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl()
        ];
        return response()->json($services, 200);
    }

    public function googleLoginCallback()
    {
        $googleAuth = Socialite::driver('google')->stateless()->user();
        $customer = null;

        DB::transaction(function () use ($googleAuth, &$customer) {
            // Tìm hoặc tạo một tài khoản xã hội dựa trên social_id và social_provider
            $socialAccount = SocialAccount::firstOrNew(
                [
                    'social_id' => $googleAuth->getId(),
                    'social_provider' => 'google',
                ]
            );

            // Kiểm tra xem tài khoản xã hội đã tồn tại chưa
            if (!$socialAccount->exists) {
                // Nếu không tồn tại, tạo mới khách hàng
                $customer = Customer::create([
                    'username' => $this->generateUniqueUsername(),
                    'password' => Hash::make(Str::random(10)), // Mật khẩu được mã hóa nhưng không được sử dụng
                ]);
                // Liên kết tài khoản xã hội với khách hàng mới
                $socialAccount->customer()->associate($customer);
            } else {
                // Nếu tài khoản xã hội đã tồn tại, lấy khách hàng đã liên kết
                $customer = $socialAccount->customer;
            }

            // Lưu tài khoản xã hội
            $socialAccount->save();
        });

        // Tạo token cho khách hàng và trả về phản hồi
        $token = $customer->createToken('vnwa_auth_token')->plainTextToken;

        return response()->json([
            'message' => "Login Success",
            'token' => $token,
        ], 200);
    }
}
