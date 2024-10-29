<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Hash;
use Illuminate\Http\Request;
use Validator;

class CustomerController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:customers',
            'email' => 'nullable|string|email|max:300|unique:customers',
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
            'username' => $request->last_name,
            'email' => $request->email,
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
            'email' => 'required|string|email',
            'password' => 'required|string|min:6', // Kiểm tra độ dài mật khẩu tối thiểu
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Dữ liệu không hợp lệ'
            ], 422);
        }

        // 2. Tìm khách hàng theo email
        $customer = Customer::where('email', $request->email)->first();

        // 3. Kiểm tra mật khẩu hoặc email không đúng
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        // 4. Tạo token cho phiên đăng nhập
        $token = $customer->createToken('vnwa_auth_token')->plainTextToken;


        return response()->json([
            'message' => "Đăng nhập thành công",
            'token' => $token,
        ], 200);
    }



}
