<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Episode;
use Storage;
use Str;
use ZipArchive;

class EpisodeController extends Controller
{
    public function importMultipleZip(Request $request, $product_id)
    {

        // Kiểm tra nếu không có file gửi lên
        if (!$request->hasFile('files')) {
            return response()->json(['message' => 'Không có file nào được gửi!'], 400);
        }

        foreach ($request->file('files') as $file) {
            // Lấy tên file zip và tạo slug
            $episodeName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $slug = Str::slug($episodeName);

            $episode = Episode::where('product_id', $product_id)->where('slug', $slug)->first();
            if ($episode) {
                $episode->touch();
                Server::where('episode_id', $episode->id)->delete();
            } else {
                $episode = Episode::create([
                    'product_id' => $product_id,
                    'name' => $episodeName,
                    'slug' => $slug
                ]);

            }

            $product = Product::find($product_id);
            $product->touch();

            $episode->save();
            $episode->product->touch();
            // Tạo đường dẫn thư mục giải nén
            $extractPath = "public/images/fix/{$product->slug}/{$slug}";
            Storage::makeDirectory($extractPath); // Tạo thư mục nếu chưa tồn tại

            // Lưu file zip tạm vào storage
            $tempZipPath = $file->store('temp');
            $fullTempPath = storage_path("app/{$tempZipPath}");

            // Giải nén file zip
            $zip = new ZipArchive;
            if ($zip->open($fullTempPath) === TRUE) {
                $zip->extractTo(storage_path("app/{$extractPath}"));
                $zip->close();

                // Xóa file zip tạm sau khi giải nén
                Storage::delete($tempZipPath);

                // Lấy danh sách ảnh từ thư mục đích
                $files = Storage::files($extractPath);

                // Tạo mảng URL ảnh
                $imagePaths = array_map(fn($image) => asset(Storage::url($image)), $files);

                // Lưu thông tin vào bảng servers
                Server::create([
                    'episode_id' => $episode->id,
                    'images' => $imagePaths,
                ]);
            } else {
                return response()->json(['error' => 'Không thể mở file zip.'], 500);
            }
        }

        return response()->json(['message' => 'Upload và giải nén thành công!'], 200);
    }

    public function makeSlug($string)
    {
        $search = ['#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#', '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#', '#(ì|í|ị|ỉ|ĩ)#', '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#', '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#', '#(ỳ|ý|ỵ|ỷ|ỹ)#', '#(đ)#', '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#', '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#', '#(Ì|Í|Ị|Ỉ|Ĩ)#', '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#', '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#', '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#', '#(Đ)#', '/[^a-zA-Z0-9\-\_]/'];
        $replace = ['a', 'e', 'i', 'o', 'u', 'y', 'd', 'A', 'E', 'I', 'O', 'U', 'Y', 'D', '-'];
        $string = preg_replace($search, $replace, $string);
        $string = preg_replace('/(-)+/', '-', $string);
        $string = strtolower($string);
        return $string;
    }
    public function index($product_id)
    {
        $product = Product::find($product_id);
        return Inertia::render('Product/Episode', ['product' => $product]);

    }
    public function loadDataTable(Request $request, $product_id)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'ASC');
        $name = $request->get('name');

        // Khởi tạo query
        $query = Episode::query();
        $query->where('product_id', $product_id);
        $query->with('servers');
        // Lọc theo tên product nếu có
        if ($name) {
            $query->where('name', 'like', "%$name%");
        }

        // Lọc theo danh mục nếu có


        // Sắp xếp và phân trang
        $total = $query->count();
        $episodes = $query->skip(($page - 1) * $perPage)->take($perPage)->orderBy('id')->get();


        return response()->json([
            'data' => $episodes,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
        ], 200);




    }
    function create($product_id, Request $rq)
    {
        if ($rq->name) {
            $slug = $this->makeSlug($rq->name);
            if (Episode::where('slug', $slug)->where('product_id', $product_id)->first()) {
                return response()->json(['error' => 'Đã có tên này']);
            } else {
                Episode::create(['product_id' => $product_id, 'name' => $rq->name, 'slug' => $slug]);
                return response()->json(['success' => 'Thêm ' . $rq->name . '  thành công']);
            }
        } else {
            return response()->json(['error' => 'Nhập tên']);
        }
    }
    function update($product_id, $id, Request $rq)
    {
        if ($rq->name) {
            $slug = $this->makeSlug($rq->name);
            if (
                Episode::whereNotIn('id', [$id])
                    ->where('product_id', $product_id)
                    ->where('slug', $slug)
                    ->first()
            ) {
                return response()->json(['error' => 'Đã có tên này']);
            } else {
                Episode::where('id', $id)
                    ->where('product_id', $product_id)
                    ->update(['name' => $rq->name, 'slug' => $slug]);
                Product::where('id', $product_id)->update(['updated_at' => now()]);
                return response()->json(['success' => 'Sửa  tên tập thành công thành công']);
            }
        } else {
            return response()->json(['error' => 'Nhập tên']);
        }
    }


    public function delete(Request $request)
    {
        Episode::whereIn('id', $request->dataId)->delete();
        return response()->json(['message' => "Xóa thành công"], 200);
    }
}
