<?php

namespace App\Http\Controllers;
use App\Models\Episode;
use App\Models\Server;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use App\Models\Year;
use App\Models\Nation;
use App\Models\Type;
use App\Models\ProType;
use Storage;
use Str;


class ProductController extends Controller
{

    public function importManga18fxCrawl(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->products as $productData) {
                $this->importProductCrawlNoSaveImage($productData, 1);
            }
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function importProductCrawlNoSaveImage($productData, $is_18)
    {
        $productName = $productData['title'];
        $productSlug = Str::slug($productName);

        // Kiểm tra sản phẩm đã tồn tại hay chưa
        $product = Product::firstOrCreate(
            ['slug' => $productSlug],
            [
                'url_avatar' => $productData['image'],
                'is_18' => $is_18,
                'name' => $productName,
            ]
        );

        // Lặp qua từng tập và thêm vào cơ sở dữ liệu
        foreach ($productData['chapters'] as $chapter) {
            $episodeName = $chapter['title'];
            $episodeSlug = Str::slug($episodeName);

            // Kiểm tra xem episode đã tồn tại chưa
            $episode = Episode::firstOrCreate(
                ['slug' => $episodeSlug, 'product_id' => $product->id],
                ['name' => $episodeName]
            );

            // Nếu chưa có server thì thêm vào
            if (!Server::where('episode_id', $episode->id)->exists()) {
                Server::create([
                    'episode_id' => $episode->id,
                    'images' => $chapter['images'],
                ]);
            }
        }
    }


    // private function importProductCrawl($productData, $is_18)
    // {

    //     $productName = $productData['title'];
    //     $productSlug = Str::slug($productName);
    //     $product = Product::where('slug', $productSlug)->first();

    //     if (!$product) {
    //         // Tải ảnh từ URL
    //         $imagePath = $productData['image'];
    //         $imageContent = file_get_contents($imagePath);
    //         $imageName = basename($imagePath); // Lấy tên gốc từ URL

    //         // Lưu ảnh vào storage
    //         $imageStoragePath = 'images/crawls/' . $productSlug . '/' . $imageName;
    //         Storage::disk('public')->put($imageStoragePath, $imageContent);
    //         $fullImageUrl = Storage::url($imageStoragePath);

    //         $product = Product::create([
    //             'url_avatar' => $fullImageUrl,
    //             'is_18' => $is_18,
    //             'name' => $productName,
    //             'slug' => $productSlug
    //         ]);
    //     }

    //     $chapters = $productData['chapters'];
    //     foreach ($chapters as $chapter) {
    //         $episodeName = $chapter['title'];
    //         $episodeSlug = Str::slug($episodeName);
    //         $episode = Episode::where('slug', $episodeSlug)->where('product_id', $product->id)->first();

    //         if ($episode) {
    //             if (!Server::where('episode_id', $episode->id)->exists()) {
    //                 $serverImages = $chapter['images'];
    //                 $newIamges = [];
    //                 foreach ($serverImages as $imageUrl) {
    //                     // Tải ảnh từ URL
    //                     $imageContent = file_get_contents($imageUrl);
    //                     $imageName = basename($imageUrl); // Lấy tên gốc từ URL
    //                     $imageStoragePath = 'images/crawls/' . $productSlug . '/' . $episodeSlug . '/' . $imageName;
    //                     Storage::disk('public')->put($imageStoragePath, $imageContent);
    //                     $imageFullUrl = Storage::url($imageStoragePath);

    //                     // Lưu đường dẫn ảnh vào mảng hoặc cơ sở dữ liệu theo yêu cầu
    //                 }
    //                 Server::create(['episode_id' => $episode->id, 'images' => $newIamges]);

    //             }
    //         } else {

    //             $episode = Episode::create([
    //                 'product_id' => $product->id,
    //                 'name' => $episodeName,
    //                 'slug' => $episodeSlug
    //             ]);

    //             $serverImages = $chapter['images'];
    //             $newIamges = [];
    //             foreach ($serverImages as $imageUrl) {
    //                 // Tải ảnh từ URL
    //                 $imageContent = file_get_contents($imageUrl);
    //                 $imageName = basename($imageUrl); // Lấy tên gốc từ URL
    //                 $imageStoragePath = 'images/crawls/' . $productSlug . '/' . $episodeSlug . '/' . $imageName;
    //                 Storage::disk('public')->put($imageStoragePath, $imageContent);
    //                 $imageFullUrl = Storage::url($imageStoragePath);
    //                 $newIamges[] = $imageFullUrl;
    //             }
    //             Server::create(['episode_id' => $episode->id, 'images' => $newIamges]);
    //         }
    //     }

    //     return response()->json(['success' => true]);


    // }


    function createSlug($slug)
    {
        $check = Product::where('slug', $slug)->first();
        return $check ? true : false;
    }
    function updateSlug($slug, $id)
    {
        $check = Product::where('slug', $slug)
            ->whereNotIn('id', [$id])
            ->first();
        return $check ? true : false;
    }
    public function loadDataTable(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'updated_at');
        $sortType = $request->get('sortType', 'DESC');
        $name = $request->get('name');

        // Khởi tạo query
        $query = Product::query();

        // Lọc theo tên product nếu có
        if ($name) {
            $query->where('name', 'like', "%$name%");
        }

        // Lọc theo danh mục nếu có


        // Sắp xếp và phân trang
        $total = $query->count();
        $products = $query->skip(($page - 1) * $perPage)->take($perPage)->latest( 'updated_at')->get();

        return response()->json([
            'data' => $products,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
        ], 200);




    }
    function showIndex()
    {

        return Inertia::render('Product/Show');
    }

    function showCreate()
    {
        $listCategory = Category::where('status', 1)->get(['id', 'name', 'slug']);
        $listNation = Nation::where('status', 1)->get(['id', 'name', 'slug']);

        $listType = Type::where('status', 1)->get(['id', 'name']);
        $dataType = [];
        foreach ($listType as $key => $value) {
            $dataType[$key]['label'] = $value->name;
            $dataType[$key]['value'] = $value->id;
        }

        return Inertia::render('Product/Create', ['listCategory' => $listCategory, 'listNation' => $listNation, 'listType' => $dataType]);
    }
    function create(Request $rq)
    {
        $data = [];
        if (!$rq->full_name) {
            return response()->json(['error' => 'Vui lòng nhập tên đầy đủ của truyện', 'column' => 'full_name']);
        } else {
            $data['full_name'] = $rq->full_name;
        }
        if (!$rq->name) {
            return response()->json(['error' => 'Vui lòng nhập tên truyện', 'column' => 'name']);
        } else {
            $data['name'] = $rq->name;
        }
        if (!$rq->category_id) {
            return response()->json(['error' => 'Vui lòng nhập danh mục', 'column' => 'category_id']);
        } else {
            $data['category_id'] = $rq->category_id;
        }
        if (!$rq->slug) {
            return response()->json(['error' => 'Có lỗi xảy ra, không cập nhập được slug, hãy load lại trang', 'column' => 'slug']);
        } else {
            if ($this->createSlug($rq->slug)) {
                return response()->json(['error' => 'Đã có đường dẫn này, nhập đường dẫn khác để tối ưu SEO', 'column' => 'slug']);
            }
            $data['slug'] = $rq->slug;
        }
        if (!$rq->desc) {
            return response()->json(['error' => 'Vui lòng nhập mô tả truyện', 'column' => 'desc']);
        } else {
            $data['desc'] = $rq->desc;
        }
        if (!$rq->nation_id) {
            return response()->json(['error' => 'Vui lòng chọn quốc gia', 'column' => 'nation_id']);
        } else {
            $data['nation_id'] = $rq->nation_id;
        }


        if (!$rq->types) {
            return response()->json(['error' => 'Vui lòng chọn thể loại', 'column' => 'types']);
        } else {
            $data['types'] = $rq->types;
        }

        if (!$rq->url_avatar) {
            return response()->json(['error' => 'Vui lòng chọn ảnh đại diện', 'column' => 'url_avatar']);
        } else {
            $data['url_avatar'] = $rq->url_avatar;
        }


        if (!$rq->meta_title) {
            return response()->json(['error' => 'Vui lòng nhập tiêu đề link để tối ưu SEO', 'column' => 'meta_title']);
        } else {
            $data['meta_title'] = $rq->meta_title;
        }

        if (!$rq->meta_image) {
            return response()->json(['error' => 'Vui lòng chọn ảnh link để tối ưu SEO', 'column' => 'meta_image']);
        } else {
            $data['meta_image'] = $rq->meta_image;
        }

        if (!$rq->meta_desc) {
            return response()->json(['error' => 'Vui lòng nhập mô tả link để tối ưu SEO', 'column' => 'meta_desc']);
        } else {
            $data['meta_desc'] = $rq->meta_desc;
        }

        $id_tb = Product::create($data)->id;
        foreach ($rq->types as $type_id) {
            ProType::create([
                'type_id' => $type_id,
                'product_id' => $id_tb,
            ]);
        }
        return response()->json(['success' => 'Cập nhập dữ liệu thành công']);
    }

    function showEdit($id)
    {
        $data = Product::with('types')->with('nation')->find($id);

        $listCategory = Category::where('status', 1)->get(['id', 'name', 'slug']);
        $listNation = Nation::where('status', 1)->get(['id', 'name', 'slug']);

        $listType = Type::where('status', 1)->get(['id', 'name']);
        $dataType = [];
        foreach ($listType as $key => $value) {
            $dataType[$key]['label'] = $value->name;
            $dataType[$key]['value'] = $value->id;
        }
        $dataTypeSelected = [];

        if ($data->types) {
            if ($data->types) {
                foreach ($data->types as $key => $value) {
                    $dataTypeSelected[$key] = $value->id;
                }
            }
        }

        return Inertia::render('Product/Edit', ['data' => $data, 'listCategory' => $listCategory, 'listNation' => $listNation, 'listType' => $dataType, 'dataTypeSelected' => $dataTypeSelected]);
    }
    function update(Request $request, $id)
    {
        $request->validate([
            'url_avatar' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $id,
        ]);

        try {
            $product = Product::findOrFail($id);

            // Cập nhật các trường với dữ liệu từ request
            $product->url_avatar = $request->url_avatar;
            $product->category_id = $request->category_id;
            $product->url_bg = $request->url_bg;
            $product->full_name = $request->full_name;
            $product->nation_id = $request->nation_id;
            $product->name = $request->name;
            $product->slug = $request->slug;
            $product->desc = $request->desc;
            $product->meta_image = $request->meta_image;
            $product->meta_title = $request->meta_title;
            $product->meta_desc = $request->meta_desc;
            ProType::where('product_id', $id)->delete();
            foreach ($request->types as $type_id) {
                ProType::create([
                    'type_id' => $type_id,
                    'product_id' => $id,
                ]);
            }
            // Lưu thay đổi vào cơ sở dữ liệu
            $product->save();
            return response()->json(['success' => 'Cập nhập dữ liệu thành công'], 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);

        }


    }

    function showTrash()
    {
        $data = Product::onlyTrashed()->get();
        foreach ($data as $key => $value) {
            $value->create_time = Carbon::parse($value->created_at)->format('H:i , d/m/Y ');
            $value->update_time = Carbon::parse($value->updated_at)->format('H:i , d/m/Y ');
            $value->delete_at = Carbon::parse($value->deleted_at)->format('H:i , d/m/Y ');
            $value->delete_time = Carbon::parse($value->deleted_at)
                ->subDays(30)
                ->format('H:i , d/m/Y ');
        }
        $trashCount = Product::onlyTrashed()->count();

        return Inertia::render('Product/Trash', ['data' => $data, 'trashCount' => $trashCount]);
    }
}
