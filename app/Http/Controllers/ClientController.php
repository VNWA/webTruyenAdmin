<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Type;
use App\Models\ProType;
use App\Models\Nation;
use App\Models\Year;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBanner;
use App\Models\Rating;
use App\Models\Episode;
use App\Models\Server;
use Carbon\Carbon;
class ClientController extends Controller
{
    protected $metaTitle;
    protected $metaImage;
    protected $metaDesc;
    public function __construct()
    {
        $this->setDefaultMeta();
    }

    // Hàm lấy giá trị mặc định từ database
    protected function setDefaultMeta()
    {
        $company = Company::first(); // Lấy bản ghi đầu tiên (mặc định)

        $this->metaTitle = $company->meta_title ?? 'Tiêu đề mặc định';
        $this->metaImage = $company->meta_image ?? '/image.jpg';
        $this->metaDesc = $company->meta_desc ?? 'Mô tả mặc định';
    }

    // Hàm dùng chung để set SEO meta cho các view
    protected function setMeta($title = null, $image = null, $desc = null)
    {
        return [
            'metaTitle' => $title ?? $this->metaTitle,
            'metaImage' => $image ?? $this->metaImage,
            'metaDesc' => $desc ?? $this->metaDesc,
        ];
    }

    public function incrementViews($slug)
    {
        if ($product = Product::where('slug', $slug)->first()) {
            $product->incrementViews();
        }
        return true;
    }

    function getDataSitemap()
    {
        $data = [];

        // Lấy sản phẩm với slug
        $products = Product::where('status', 1)
            ->latest('updated_at')
            ->get(['slug', 'updated_at'])
            ->map(fn($item) => [
                'slug' => '/manga/' . $item->slug,
                'updated_at' => $item->updated_at,
            ])->toArray();

        $data = array_merge($data, $products);

        // Lấy episode với hạn chế N+1
        $episodes = Episode::with('product:id,slug')
            ->latest('updated_at')
            ->get(['slug', 'product_id', 'updated_at'])
            ->map(fn($item) => [
                'slug' => '/manga/' . $item->product->slug . '/' . $item->slug,
                'updated_at' => $item->updated_at,
            ])->toArray();

        $data = array_merge($data, $episodes);

        // Lấy dữ liệu Nation
        $nations = Nation::where('status', 1)
            ->latest('updated_at')
            ->get(['slug', 'updated_at'])
            ->map(fn($item) => [
                'slug' => '/nation-' . $item->slug,
                'updated_at' => $item->updated_at,
            ])->toArray();

        $data = array_merge($data, $nations);

        // Lấy dữ liệu Type
        $types = Type::where('status', 1)
            ->latest('updated_at')
            ->get(['slug', 'updated_at'])
            ->map(fn($item) => [
                'slug' => '/type-' . $item->slug,
                'updated_at' => $item->updated_at,
            ])->toArray();

        $data = array_merge($data, $types);

        return $data;
    }

    function getDataWeb()
    {
        $dataWeb = Company::find(1)->only(['url_avatar_full', 'name']);
        $dataType = Type::where('status', 1)
            ->orderBy('ORD')
            ->get(['id', 'name', 'slug']);
        $categories = Category::all(['id', 'name', 'slug']);
        $nations = Nation::all(['id', 'name', 'slug']);
        $topViewProducts = Product::where('status', 1)
            ->limit(10)
            ->orderByDesc('views')
            ->get(['id', 'url_avatar', 'name', 'slug', 'full_name']);

        return response()->json(compact('dataWeb', 'dataType', 'categories', 'nations', 'topViewProducts'), 200);
    }

    function getDataHome()
    {
        // Sản phẩm nổi bật: Sắp xếp theo views giảm dần, nếu views giống nhau thì theo id giảm dần
        $highlightProducts = Product::where('status', 1)
            ->orderByDesc('views')
            ->orderByDesc('id') // Sắp xếp phụ theo id
            ->limit(5)
            ->get();

        // Sản phẩm mới: Lấy sản phẩm mới nhất theo id
        $newProducts = Product::where('status', 1)
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        // Sản phẩm raw: Sắp xếp theo updated_at giảm dần, nếu giống nhau thì theo id giảm dần
        $rawProducts = Product::where('status', 1)
            ->where('category_id', 1)
            ->orderByDesc('updated_at')
            ->orderByDesc('id') // Sắp xếp phụ theo id
            ->limit(7)
            ->get();

        // Sản phẩm sub: Sắp xếp theo updated_at giảm dần, nếu giống nhau thì theo id giảm dần
        $subProducts = Product::where('status', 1)
            ->where('category_id', 2)
            ->orderByDesc('updated_at')
            ->orderByDesc('id') // Sắp xếp phụ theo id
            ->limit(11)
            ->get();

        // Trả về JSON response

        $meta = $this->setMeta();
        return response()->json(compact('highlightProducts', 'newProducts', 'rawProducts', 'subProducts', 'meta'), 200);
    }

    function getTrendingProducts()
    {
        $products = Product::where('status', 1)
            ->latest('views')
            ->paginate(15);

        return response()->json(compact('products'), 200);
    }

    public function getProductsInFilter(Request $request)
    {
        $products = Product::query()->where('status', 1);

        if ($categories = $request->input('category')) {
            $products->whereIn('category_id', explode(',', $categories));
        }

        if ($nations = $request->input('nation')) {
            $products->whereIn('nation_id', explode(',', $nations));
        }

        if ($types = $request->input('type')) {
            $idProductsWithType = ProType::whereIn('type_id', explode(',', $types))->pluck('product_id');
            $products->whereIn('id', $idProductsWithType);
        }

        $products->where('is_end', $request->input('is_complete', 0))->withCount('wishlists');

        switch ($request->input('arange', 'new-updated')) {
            case 'new-updated':
                $products->orderByDesc('updated_at');
                break;
            case 'old-updated':
                $products->orderBy('updated_at');
                break;
            case 'new-created':
                $products->orderByDesc('created_at');
                break;
            case 'old-created':
                $products->orderBy('created_at');
                break;
            case 'a-z':
                $products->orderBy('name');
                break;
            case 'z-a':
                $products->orderByDesc('name');
                break;
            case 'most-view':
                $products->orderByDesc('views');
                break;
            case 'most-favourite':
                $products->orderByDesc('wishlists_count');
                break;
        }

        $products = $products->paginate(15);

        return response()->json(compact('products'), 200);
    }

    function getProducts()
    {
        $products = Product::where('status', 1)
            ->latest('updated_at')
            ->paginate(15);

        return response()->json(compact('products'), 200);
    }

    public function getWishlistCountWithProduct($slug)
    {
        if ($product = Product::where('slug', $slug)->first()) {
            $countWishlist = Wishlist::where('product_id', $product->id)->count();

            return response()->json(['count_wishlist' => $countWishlist], 200);
        }
    }

    function getDetailProduct($slug)
    {
        $company = Company::first(['meta_title', 'meta_desc', 'meta_image']);
        $product = Product::where('slug', $slug)->with(['nation', 'year', 'types', 'episodes'])->first();
        $meta_title =$product->meta_title ?? $product->name ;



        $meta = $this->setMeta($meta_title, $product->meta_desc, $product->meta_image);
        return response()->json([
            'meta' => $meta,
            'product' => $product,
        ], 200);
    }

    function ratingProduct(Request $request)
    {
        if ($request->client_ip && $request->rating && $request->product_id) {
            $Rating = Rating::updateOrCreate(
                ['product_id' => $request->product_id, 'client_ip' => filter_var($request->client_ip, FILTER_SANITIZE_STRING)],
                ['rating' => $request->rating]
            );

            $totalRatings = Rating::where('product_id', $request->product_id)->count();
            $totalRatingPoints = Rating::where('product_id', $request->product_id)->sum('rating');
            $averageRating = $totalRatingPoints / $totalRatings;



            return response()->json(compact('totalRatings', 'averageRating', 'clientRating'), 200);
        } else {
            return response()->json(['error' => 'Không đủ dữ liệu'], 200);
        }
    }


    public function getDataEpisode($slug, $episode_slug)
    {

        // Lấy product
        $product = Product::where('slug', $slug)->firstOrFail();

        // Lấy episode hiện tại với thông tin servers
        $episode = Episode::where('product_id', $product->id)
            ->where('slug', $episode_slug)
            ->with('servers')
            ->firstOrFail();

        // Lấy tất cả episodes của product (sắp xếp theo thứ tự ID)
        $episodes = Episode::where('product_id', $product->id)
            ->orderBy('id', 'asc')
            ->get(['id', 'name', 'slug']);

        // Xác định vị trí của tập hiện tại
        $currentIndex = $episodes->search(fn($ep) => $ep->id === $episode->id);

        // Lấy tập trước và sau
        $prevEpisode = $currentIndex > 0 ? $episodes[$currentIndex - 1] : null;
        $nextEpisode = $currentIndex < $episodes->count() - 1 ? $episodes[$currentIndex + 1] : null;



        $meta_title = $episode->name . ' - ' . $product->name;



        $meta = $this->setMeta($meta_title, $product->meta_desc, $product->meta_image);


        // Trả về response JSON
        return response()->json([
            'meta' => $meta,
            'episode' => $episode,
            'episodes' => $episodes,
            'prev_episode' => $prevEpisode,
            'next_episode' => $nextEpisode,
            'product_name' => $product->name,
            'product_desc' => $product->desc,
        ], 200);
    }

    function getDataPageCategory($slug)
    {
        $category = Category::where('slug', $slug)->first();

        $dataProduct = Product::where('id_category', $category->id)
            ->where('status', 1)
            ->latest('updated_at')
            ->paginate(20)
            ->setPath('');

        $listTopProduct = Product::where('status', 1)
            ->where('id_category', $category->id)
            ->with('ratings')
            ->get()
            ->sortByDesc(function ($product) {
                return $product->ratings->avg('rating');
            })
            ->take(11)
            ->map(function ($product) {
                return [
                    'url_avatar' => $product->url_avatar,
                    'url_bg' => $product->url_bg,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'category' => $product->category,
                    'rating' => $product->rating,
                    'yearName' => $product->yearName,
                ];
            });
        $dataTopProduct = [];
        $n = 0;
        foreach ($listTopProduct as $value) {
            $dataTopProduct[$n] = $value;
            $n++;
        }

        return response()->json(['dataProduct' => $dataProduct, 'category' => $category, 'dataTopProduct' => $dataTopProduct], 200);
    }

    function getProductsByType($slug)
    {
        $type = Type::where('slug', $slug)
            ->select(['id', 'name', 'slug'])
            ->first();
        $listIdProduct = [];
        $ProType = ProType::where('type_id', $type->id)->get(['product_id']);
        foreach ($ProType as $key => $value) {
            $listIdProduct[$key] = $value->product_id;
        }

        $products = Product::whereIn('id', $listIdProduct)->where('status', 1)->latest('updated_at')->paginate(20)->setPath('');
        $company = Company::first(['meta_title', 'meta_desc', 'meta_image']);
        $title = $type->name;
        $meta_title = $type->meta_title ?: $type->name;
        $meta_desc = $type->meta_desc ?: $company->meta_desc;
        $meta_image = $type->meta_image ?: $company->meta_image;


        return response()->json([
            'products' => $products,
            'type' => $type,
            'title' => $title,
            'meta_title' => $meta_title,
            'meta_desc' => $meta_desc,
            'meta_image' => $meta_image
        ], 200);
    }

    function getProductsByNation($slug)
    {
        $nation = Nation::where('slug', $slug)
            ->select(['id', 'name', 'slug'])
            ->first();

        $products = Product::where('nation_id', $nation->id)
            ->where('status', 1)
            ->latest('updated_at')
            ->paginate(20)
            ->setPath('');
        $company = Company::first(columns: ['meta_title', 'meta_desc', 'meta_image']);
        $title = $nation->name;
        $meta_title = $nation->meta_title ?: $nation->name;
        $meta_desc = $nation->meta_desc ?: $company->meta_desc;
        $meta_image = $nation->meta_image ?: $company->meta_image;

        return response()->json([
            'products' => $products,
            'nation' => $nation,
            'title' => $title,
            'meta_title' => $meta_title,
            'meta_desc' => $meta_desc,
            'meta_image' => $meta_image
        ], 200);

    }

    function getDataPageYear($slug)
    {
        $year = Year::where('slug', $slug)
            ->select(['id', 'name', 'slug'])
            ->first();

        $dataProduct = Product::where('year_id', $year->id)
            ->where('status', 1)
            ->latest('updated_at')
            ->paginate(20)
            ->setPath('');

        $listTopProduct = Product::where('status', 1)
            ->where('year_id', $year->id)
            ->with('ratings')
            ->get()
            ->sortByDesc(function ($product) {
                return $product->ratings->avg('rating');
            })
            ->take(11)
            ->map(function ($product) {
                return [
                    'url_avatar' => $product->url_avatar,
                    'url_bg' => $product->url_bg,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'category' => $product->category,
                    'rating' => $product->rating,
                    'yearName' => $product->yearName,
                ];
            });
        $dataTopProduct = [];
        $n = 0;
        foreach ($listTopProduct as $value) {
            $dataTopProduct[$n] = $value;
            $n++;
        }

        return response()->json(['dataProduct' => $dataProduct, 'year' => $year, 'dataTopProduct' => $dataTopProduct], 200);
    }
    function getSearchSuggest($searchText)
    {
        $products = [];
        if ($searchText) {
            $products = Product::where('status', 1)
                ->where('name', 'LIKE', "%$searchText%")
                ->orWhere('full_name', 'LIKE', "%$searchText%")
                ->latest('updated_at')
                ->get(['name', 'full_name', 'url_avatar', 'slug']);
        }

        // Trả về kết quả cho view hoặc làm gì đó với kết quả
        return response()->json($products);
    }
}
