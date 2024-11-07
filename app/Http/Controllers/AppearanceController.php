<?php

namespace App\Http\Controllers;

use App\Models\Appearance;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppearanceController extends Controller
{
    public function AdsBannerShow()
    {

        $data = Appearance::where('type', 'banner_ads')->first();
        return Inertia::render('Appearance/AdsBanner', ['data' => $data]);
    }
    public function AdsBannerUpdate(Request $request)
    {
        try {
            Appearance::where('type', 'banner_ads')->update(['value' => $request->all()]);
            return response()->json(['message' => 'Cập nhập dữ liệu thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Có lỗi xảy ra'], 500);

        }

    }
}
