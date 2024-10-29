<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Wishlist;
use Hash;
use Illuminate\Http\Request;
use Validator;

class CustomerController extends Controller
{
    public function loadWishlist(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => "Please Login"], 401);
        }

        $customer = $request->user();

        // Lấy danh sách sản phẩm trong wishlist
        $wishlistItems = Wishlist::where('customer_id', $customer->id)
            ->with('product') // Lấy thông tin sản phẩm liên quan
            ->get();

        $products = $wishlistItems->map(function ($item) {
            return $item->product; // Trả về sản phẩm liên quan
        });

        return response()->json($products, 200); // Trả về status 200
    }
    public function toogleWishlist(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:255|exists:products,slug',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!$request->user()) {
            return response()->json(['message' => "Please Login"], 401);
        }

        $customer = $request->user();

        // Tìm sản phẩm dựa theo slug đã gửi từ request
        $product = Product::where('slug', $request->slug)->firstOrFail(); // Sử dụng `firstOrFail` để tránh null-check thêm lần nữa

        // Kiểm tra sản phẩm đã có trong wishlist chưa
        $wishlist = Wishlist::where('product_id', $product->id)
            ->where('customer_id', $customer->id)
            ->first();

        if ($wishlist) {
            // Nếu đã có, xóa khỏi danh sách yêu thích
            $wishlist->delete();
            return response()->json(['message' => "Remove Bookmark Success"], 200);
        }

        // Nếu chưa có, thêm vào danh sách yêu thích
        Wishlist::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
        ]);

        return response()->json(['message' => "Add Bookmark Success"], 201);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:customers',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Register not success', // Thông điệp lỗi
                'errors' => $validator->errors(), // Các lỗi xác thực
            ], 422);
        }

        // Tạo người dùng mới
        $customer = Customer::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        // Tạo OTP và lưu vào cơ sở dữ liệu

        // Tạo token
        $token = $customer->createToken('vnwa_auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Regsiter Success',
            'token' => $token,
        ], 200);
    }
    public function login(Request $request)
    {
        // 1. Kiểm tra và validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|exists:customers,username',
            'password' => 'required|string|min:6', // Kiểm tra độ dài mật khẩu tối thiểu
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Dữ liệu không hợp lệ'
            ], 422);
        }

        // 2. Tìm khách hàng theo email
        $customer = Customer::where('username', $request->username)->first();

        // 3. Kiểm tra mật khẩu hoặc email không đúng
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'message' => 'Acount Not Found'
            ], 401);
        }

        // 4. Tạo token cho phiên đăng nhập
        $token = $customer->createToken('vnwa_auth_token')->plainTextToken;


        return response()->json([
            'message' => "Login Success",
            'token' => $token,
        ], 200);
    }



}
