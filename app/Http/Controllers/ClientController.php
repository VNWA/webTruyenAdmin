<?php

namespace App\Http\Controllers;

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
    function getDataSitemap()
    {
        $data = [];
        $data =  Product::where('status', 1)
        ->latest()
        ->get(['slug', 'updated_at'])
        ->map(function ($item) {
            $item->slug = '/truyen/' . $item->slug; // Thêm giá trị vào slug
            return $item;
        })
        ->toArray();
        $dataNation = Nation::where('status', 1)
            ->latest()
            ->get(['slug', 'updated_at'])
            ->toArray();
        foreach ($dataNation as $key => $value) {
            $push = array_push($data, ['slug' => '/quoc-gia-' . $value['slug'], 'updated_at' => $value['updated_at']]);
        }
        $dataType = Type::where('status', 1)
            ->latest()
            ->get(['slug', 'updated_at'])
            ->toArray();
        foreach ($dataType as $key => $value) {
            $push = array_push($data, ['slug' => '/the-loai-' . $value['slug'], 'updated_at' => $value['updated_at']]);
        }

        return $data;
    }
    function getDataWeb()
    {
        $dataWeb = Company::find(1)->first(['url_avatar_full', 'name']);
        $dataType = Type::where('status', 1)
            ->orderBy('ORD')
            ->get(['name', 'slug']);
        $dataNation = Nation::where('status', 1)
            ->orderBy('ORD')
            ->get(['name', 'slug']);
        $dataYear = Year::where('status', 1)
            ->orderBy('name', 'DESC')
            ->get();
        return response()->json(['dataWeb' => $dataWeb, 'dataType' => $dataType, 'dataNation' => $dataNation, 'dataYear' => $dataYear], 200);
    }

    function getDataHome()
    {


        $highlightProducts = Product::where('status', 1)->where('highlight', 1)->take(10)->latest()->get();
        $newProducts = Product::where('status', 1)->latest()->take(50)->get();



        return response()->json(['highlightProducts' => $highlightProducts, 'newProducts' => $newProducts], 200);
    }
    function getProducts()
    {


        $products = Product::where('status', 1)->latest()->paginate(15)->setPath('');
        ;



        return response()->json(['products' => $products], 200);
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
        if ($request->client_ip && $request->rating && $request->id_product) {
            $client_ip = str_replace(' ', '', $request->client_ip);
            $client_ip = str_replace('"', '', $client_ip);
            $client_ip = str_replace("'", '', $client_ip);

            $Rating = Rating::updateOrCreate(['id_product' => $request->id_product, 'client_ip' => $client_ip], ['rating' => $request->rating]);

            $totalRatings = Rating::where('id_product', $request->id_product)->count();
            $totalRatingPoints = Rating::where('id_product', $request->id_product)->sum('rating');
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
        $episode = Episode::where('id_product', $product->id)
            ->where('slug', $episode_slug)
            ->with('servers')
            ->firstOrFail();

        // Lấy tất cả episodes của product (sắp xếp theo thứ tự ID)
        $episodes = Episode::where('id_product', $product->id)
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
        ], 200);
    }

    function getDataPageCategory($slug)
    {
        $category = Category::where('slug', $slug)->first();

        $dataProduct = Product::where('id_category', $category->id)
            ->where('status', 1)
            ->latest()
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
        $ProType = ProType::where('id_type', $type->id)->get(['id_product']);
        foreach ($ProType as $key => $value) {
            $listIdProduct[$key] = $value->id_product;
        }

        $products = Product::whereIn('id', $listIdProduct)->where('status', 1)->latest()->paginate(20)->setPath('');
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

        $products = Product::where('id_nation', $nation->id)
            ->where('status', 1)
            ->latest()
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

        $dataProduct = Product::where('id_year', $year->id)
            ->where('status', 1)
            ->latest()
            ->paginate(20)
            ->setPath('');

        $listTopProduct = Product::where('status', 1)
            ->where('id_year', $year->id)
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
                ->latest()
                ->get(['name', 'full_name', 'url_avatar', 'slug']);
        }

        // Trả về kết quả cho view hoặc làm gì đó với kết quả
        return response()->json($products);
    }
}
