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
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ProductController extends Controller
{
    public function showCrawlProduct()
    {
        $categories = Category::get(['id', 'name']);
        $nations = Nation::get(['id', 'name']);
        $types = Type::select('id', 'name')->get()->map(fn($type) => [
            'label' => $type->name,
            'value' => $type->id
        ]);

        return Inertia::render('Product/Crawl', ['categories' => $categories, 'nations' => $nations, 'types' => $types]);
    }


    public function ImportCrawlProduct(Request $request)
    {
        $request->validate([
            'url' => 'required|url|min:10',
            'category_id' => 'required|integer|exists:categories,id',
            'nation_id' => 'required|integer|exists:nations,id',
            'types' => 'required|array',
            'types.*' => 'integer|exists:types,id'
        ]);

        $client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36'
            ]
        ]);

        try {
            $response = $client->get($request->url);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch URL: ' . $e->getMessage()], 500);
        }

        $html = (string) $response->getBody();
        $crawler = new Crawler($html);

        $title = $crawler->filter('div.post-title h1')->text();
        $image = $crawler->filter('.summary_image a img')->attr('data-src');
        $desc = $crawler->filter('.panel-story-description .dsct ')->text();
        $slug = Str::slug($title);

        $product = Product::where('name', $title)
            ->orWhere('slug', $slug)
            ->first();

        if (!$product) {
            try {
                $product = Product::create([
                    'category_id' => $request->category_id,
                    'nation_id' => $request->nation_id,
                    'url_avatar' => $image,
                    'name' => $title,
                    'slug' => $slug,
                    'desc' => $desc,
                ]);

                foreach ($request->types as $key => $value) {
                    ProType::create([
                        'type_id' => $value,
                        'product_id' => $product->id
                    ]);
                }
            } catch (\Throwable $e) {
                return response()->json(['message' => $e->getMessage()], 500);

            }

        }



        $chapters = [];
        $crawler->filter('ul.row-content-chapter li.a-h')->each(function (Crawler $node) use (&$chapters) {
            $chapterLink = $node->filter('a')->attr('href');
            $chapterTitle = $node->filter('a')->text();
            $chapterNumber = $this->extractChapterNumber($chapterTitle);

            $chapters[] = [
                'link' => 'https://manga18fx.com/' . $chapterLink,
                'title' => $chapterTitle,
                'number' => $chapterNumber
            ];
        });

        $reversedArray = array_reverse($chapters);

        foreach ($reversedArray as $chapter) {
            $chaptername = $chapter['title'];
            $chapterSlug = Str::slug($chaptername);

            $episode = Episode::where('product_id', $product->id)
                ->where(function ($query) use ($chaptername, $chapterSlug) {
                    $query->where('name', $chaptername)
                        ->orWhere('slug', $chapterSlug);
                })
                ->first();

            if (!$episode) {
                $episode = Episode::create([
                    'name' => $chaptername,
                    'slug' => $chapterSlug,
                    'product_id' => $product->id,
                ]);
            }

            if (!Server::where('episode_id', $episode->id)->exists()) {
                $this->getImagesFromChapter($client, $chapter['link'], $episode->id);
            }
        }

        return response()->json(['message' => 'Data imported successfully'], 200);
    }

    private function extractChapterNumber($title)
    {
        preg_match('/\d+/', $title, $matches);
        return isset($matches[0]) ? (int) $matches[0] : 0;
    }

    private function getImagesFromChapter($client, $chapterLink, $episode_id)
    {
        try {
            $response = $client->get($chapterLink);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            $images = [];

            $crawler->filter('div.read-content .page-break img')->each(function (Crawler $node) use (&$images) {
                $imageSrc = $node->attr('data-src') ?? $node->attr('src');
                if ($imageSrc) {
                    $images[] = $imageSrc;
                }
            });

            Server::create([
                'episode_id' => $episode_id,
                'images' => $images
            ]);

            return $images;
        } catch (\Exception $e) {
            // Log lỗi nếu cần thiết
            \Log::error('Error fetching images: ' . $e->getMessage());
            return [];
        }
    }

    public function ChangeCompleted(Request $request)
    {

        try {

            DB::table('products')
                ->where('id', $request->id)
                ->update(['is_end' => $request->is_end]);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Thay đôi trạng thái Completed thất bại', 'error' => $th->getMessage()], 500);

        }
        return response()->json(['message' => 'Thay đôi trạng thái Completed thành công'], 200);

    }
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
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sortBy', 'updated_at');
        $sortType = $request->input('sortType', 'DESC');
        $name = $request->input('name');

        // Khởi tạo query với các điều kiện lọc và sắp xếp
        $query = Product::query()
            ->when($name, function ($query, $name) {
                return $query->where('name', 'like', "%$name%");
            })
            ->orderBy($sortBy, $sortType);

        // Sử dụng paginate để phân trang và lấy dữ liệu
        $products = $query->paginate($perPage);

        // Trả về JSON response với dữ liệu phân trang
        return response()->json($products, 200);
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
