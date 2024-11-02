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
    public function incrementViews($slug)
    {
        $product = Product::where('slug', $slug)->first(); // Tìm sản phẩm theo ID
        if ($product) {
            $product->incrementViews(); // Tăng lượt xem
        }
        return true;
    }
    function getDataSitemap()
    {
        $data = [];

        // Lấy dữ liệu từ Product
        $products = Product::where('status', 1)
            ->latest('updated_at')
            ->get(['slug', 'updated_at'])
            ->map(function ($item) {
                return [
                    'slug' => '/manga/' . $item->slug,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray();

        $data = array_merge($data, $products); // Thêm vào mảng chính

        // Lấy dữ liệu từ Episode với hạn chế N+1
        $episodes = Episode::with('product:id,slug')
            ->latest('updated_at')
            ->get(['slug', 'product_id', 'updated_at'])
            ->map(function ($item) {
                return [
                    'slug' => '/manga/' . $item->product->slug . '/' . $item->slug,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray();

        $data = array_merge($data, $episodes);

        // Lấy dữ liệu từ Nation
        $nations = Nation::where('status', 1)
            ->latest('updated_at')
            ->get(['slug', 'updated_at'])
            ->map(function ($item) {
                return [
                    'slug' => '/nation-' . $item->slug,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray();

        $data = array_merge($data, $nations);

        // Lấy dữ liệu từ Type
        $types = Type::where('status', 1)
            ->latest('updated_at')
            ->get(['slug', 'updated_at'])
            ->map(function ($item) {
                return [
                    'slug' => '/type-' . $item->slug,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray();

        $data = array_merge($data, $types);

        return $data;
    }
    function getDataWeb()
    {
        $dataWeb = Company::find(1)->first(['url_avatar_full', 'name']);
        $dataType = Type::where('status', 1)
            ->orderBy('ORD')
            ->get(['id', 'name', 'slug']);
        $categories = Category::get(['id', 'name', 'slug']);
        $nations = Nation::get(['id', 'name', 'slug']);
        $topViewProducts = Product::where('status', 1)
            ->take(10)
            ->orderByDesc('views')
            ->get(['id', 'url_avatar', 'name', 'slug', 'full_name']);
        return response()->json(['dataWeb' => $dataWeb, 'categories' => $categories, 'nations' => $nations, 'dataType' => $dataType, 'topViewProducts' => $topViewProducts], 200);
    }

    function getDataHome()
    {

        $highlightProducts = Product::where('status', 1)
            ->take(5)
            ->orderByDesc('views')
            ->get();
        // Lấy các sản phẩm mới nhất

        $newProducts = Product::where('status', 1)->latest('id')->take(3)->get();

        $rawProducts = Product::where('status', 1)->where('category_id', 1)->latest('updated_at')->take(7)->get();
        $subProducts = Product::where('status', 1)->where('category_id', 2)->latest('updated_at')->take(11)->get();
        // $newUpdatedProducts =  Product::where('status', 1)->latest('updated_at')->take(3)->get();
        $newUpdatedProducts = [];
        // Trả về JSON response
        return response()->json([
            'highlightProducts' => $highlightProducts,
            'newProducts' => $newProducts,
            'rawProducts' => $rawProducts,
            'subProducts' => $subProducts,
            'newUpdatedProducts' => $newUpdatedProducts

        ], 200);
    }

    function getTrendingProducts()
    {


        $products = Product::where('status', 1)->latest('views')->paginate(15)->setPath('');




        return response()->json(['products' => $products], 200);
    }
    public function getProductsInFilter(Request $request)
    {
        $categories = $request->input('category', '');
        $nations = $request->input('nation', '');
        $types = $request->input('type', ''); // Chuỗi type
        $arange = $request->input('arange', 'new-updated'); // Giá trị arrange
        $isComplete = $request->input('is_complete', 0);


        $idCategoriesArray = $categories ? explode(',', $categories) : [];
        $idTypesArray = $types ? explode(',', $types) : [];
        $idNationsAray = $nations ? explode(',', $nations) : [];
        $products = Product::query();
        $products->where('status', 1);
        $products->where('is_end', $isComplete);

        if ($idCategoriesArray) {
            $products->whereIn('category_id', $idCategoriesArray);
        }

        if ($idNationsAray) {
            $products->whereIn('nation_id', $idNationsAray);
        }

        if ($idTypesArray) {
            $idProductsWithType = ProType::whereIn('type_id', $idTypesArray)->pluck('product_id');
            if ($idProductsWithType) {
                $products->whereIn('id', $idProductsWithType);
            }
        }

        // Đếm số lượng yêu thích
        $products->withCount('wishlists');

        // Sắp xếp sản phẩm theo các tiêu chí
        switch ($arange) {
            case 'new-updated':
                $products->orderBy('updated_at', 'DESC');
                break;
            case 'old-updated':
                $products->orderBy('updated_at', 'ASC');
                break;
            case 'new-created':
                $products->orderBy('created_at', 'DESC');
                break;
            case 'old-created':
                $products->orderBy('created_at', 'ASC');
                break;
            case 'a-z':
                $products->orderBy('name', 'ASC'); // Thay đổi thành ASC
                break;
            case 'z-a':
                $products->orderBy('name', 'DESC'); // Thay đổi thành DESC
                break;
            case 'most-view':
                $products->orderBy('views', 'DESC'); // Thay đổi thành DESC
                break;
            case 'most-favourite':
                $products->orderBy('wishlists_count', 'DESC'); // Sắp xếp theo số lượng wishlist
                break;
            default:
                // Nếu không có điều kiện nào khớp, không sắp xếp
                break;
        }

        // Phân trang và lấy kết quả
        $products = $products->paginate(15)->setPath('');

        // Trả về danh sách sản phẩm
        return response()->json(['products' => $products], 200);
    }
    function getProducts()
    {


        $products = Product::where('status', 1)->latest('updated_at')->paginate(15)->setPath('');




        return response()->json(['products' => $products], 200);
    }
    public function getWishlistCountWithProduct($slug)
    {
        $product = Product::where('slug', $slug)->first();
        if ($product) {
            $countWishlist = Wishlist::where('product_id', $product->id)->count();

            return response()->json([
                'count_wishlist' => $countWishlist,
            ], 200);
        }
    }
    function getDetailProduct($slug)
    {
        $company = Company::first(['meta_title', 'meta_desc', 'meta_image']);
        $product = Product::where('slug', $slug)
            ->with('nation')
            ->with('year')
            ->with('types')
            ->with('episodes')
            ->first();
        $title = $product->name;
        $meta_title = $product->meta_title ?: $company->name;
        $meta_desc = $product->meta_desc ?: $company->meta_desc;
        $meta_image = $product->meta_image ?: $company->meta_image;


        return response()->json([
            'title' => $title,
            'meta_title' => $meta_title,
            'meta_desc' => $meta_desc,
            'meta_image' => $meta_image,
            'product' => $product,
        ], 200);
    }

    function ratingProduct(Request $request)
    {
        if ($request->client_ip && $request->rating && $request->product_id) {
            $client_ip = str_replace(' ', '', $request->client_ip);
            $client_ip = str_replace('"', '', $client_ip);
            $client_ip = str_replace("'", '', $client_ip);

            $Rating = Rating::updateOrCreate(['product_id' => $request->product_id, 'client_ip' => $client_ip], ['rating' => $request->rating]);

            $totalRatings = Rating::where('product_id', $request->product_id)->count();
            $totalRatingPoints = Rating::where('product_id', $request->product_id)->sum('rating');
            $averageRating = $totalRatingPoints / $totalRatings;
            $clientRating = $request->rating;
            return response()->json(['totalRatings' => $totalRatings, 'averageRating' => $averageRating, 'clientRating' => $clientRating], 200);
        } else {
            return response()->json(['error' => 'Không đủ dữ liệu'], 200);
        }
    }

    public function getDataEpisode($slug, $episode_slug)
    {
        $company = Company::first(['meta_title', 'meta_desc', 'meta_image']);

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

        // Meta thông tin
        $title = $episode->name;
        $meta_title = $product->meta_title ?: $product->name;
        $meta_desc = $product->meta_desc ?: $company->meta_desc;
        $meta_image = $product->meta_image ?: $company->meta_image;

        // Trả về response JSON
        return response()->json([
            'title' => $title,
            'meta_title' => $meta_title,
            'meta_desc' => $meta_desc,
            'meta_image' => $meta_image,
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
