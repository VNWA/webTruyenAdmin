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
    public function importMultipleZip(Request $request, $id_product)
    {

        // Kiểm tra nếu không có file gửi lên
        if (!$request->hasFile('files')) {
            return response()->json(['message' => 'Không có file nào được gửi!'], 400);
        }

        foreach ($request->file('files') as $file) {
            // Lấy tên file zip và tạo slug
            $episodeName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $slug = Str::slug($episodeName);

            // Tạo hoặc cập nhật episode vào database với id_product
            $episode = Episode::updateOrCreate(
                [
                    'id_product' => $id_product,
                    'slug' => $slug, // Điều kiện tìm kiếm
                ],
                [
                    'name' => $episodeName,
                    'updated_at' => now(),
                ]
            );
            $episode->save();

            // Tạo đường dẫn thư mục giải nén
            $extractPath = "public/images/fix/episodes/{$slug}";
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
                    'id_episode' => $episode->id,
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
    public function index($id_product)
    {
        $product = Product::find($id_product);
        return Inertia::render('Product/Episode', ['product' => $product]);

    }
    public function loadDataTable(Request $request, $id_product)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'updated_at');
        $sortType = $request->get('sortType', 'DESC');
        $name = $request->get('name');

        // Khởi tạo query
        $query = Episode::query();
        $query->where('id_product', $id_product);
        $query->with('servers');
        // Lọc theo tên product nếu có
        if ($name) {
            $query->where('name', 'like', "%$name%");
        }

        // Lọc theo danh mục nếu có


        // Sắp xếp và phân trang
        $total = $query->count();
        $episodes = $query->skip(($page - 1) * $perPage)->take($perPage)->orderBy($sortBy, $sortType)->get();


        return response()->json([
            'data' => $episodes,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
        ], 200);




    }
    function create($id_product, Request $rq)
    {
        if ($rq->name) {
            $slug = $this->makeSlug($rq->name);
            if (Episode::where('slug', $slug)->where('id_product', $id_product)->first()) {
                return response()->json(['error' => 'Đã có tên này']);
            } else {
                Episode::create(['id_product' => $id_product, 'name' => $rq->name, 'slug' => $slug]);
                return response()->json(['success' => 'Thêm ' . $rq->name . '  thành công']);
            }
        } else {
            return response()->json(['error' => 'Nhập tên']);
        }
    }
    function update($id_product, $id, Request $rq)
    {
        if ($rq->name) {
            $slug = $this->makeSlug($rq->name);
            if (
                Episode::whereNotIn('id', [$id])
                    ->where('id_product', $id_product)
                    ->where('slug', $slug)
                    ->first()
            ) {
                return response()->json(['error' => 'Đã có tên này']);
            } else {
                Episode::where('id', $id)
                    ->where('id_product', $id_product)
                    ->update(['name' => $rq->name, 'slug' => $slug]);
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
