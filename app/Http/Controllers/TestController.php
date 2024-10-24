<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

class TestController extends Controller
{
    public function test()
    {
        $url_web = 'https://toonily.com/webtoon';
        return $this->crawlMultipleProduct($url_web);
    }

    private function crawlMultipleProduct($url)
    {
        // Khởi tạo HttpClient
        $httpClient = HttpClient::create([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36',
                'Cookie' => 'toonily-mature=1', // Tắt Family Mode
            ],
            'verify_peer' => false, // Bỏ qua SSL nếu cần
            'timeout' => 30,
        ]);

        // Khởi tạo Goutte client với Symfony HttpClient
        $client = new Client($httpClient);

        // Gửi yêu cầu đến trang cần crawl
        $crawler = $client->request('GET', $url);

        // Khởi tạo mảng để lưu thông tin sản phẩm
        $products = [];

        // Trích xuất thông tin từ các phần tử
        $crawler->filter('.page-item-detail.manga')->each(function ($node) use (&$products, $client) {
            // Lấy link sản phẩm
            $link = $node->filter('a')->attr('href');

            // Lấy tên sản phẩm
            $title = $node->filter('.post-title h3 a')->text();

            // Lấy ảnh sản phẩm
            $image = $node->filter('img')->attr('data-src');

            // Thêm thông tin vào mảng products
            $products[] = [
                'link' => $link,
                'title' => $title,
                'image' => $image,
                'chapters' => $this->getChapters($link, $client),
            ];
        });

        // Trả về mảng sản phẩm dưới dạng JSON
        return response()->json($products);
    }

    private function getChapters($productLink, Client $client)
    {
        $chapters = [];
        $crawler = $client->request('GET', $productLink);

        // Trích xuất thông tin chapter
        $crawler->filter('ul.main.version-chap.no-volumn li.wp-manga-chapter')->each(function ($node) use (&$chapters, $client) {
            $chapterLink = $node->filter('a')->attr('href');
            $chapterTitle = trim($node->filter('a')->text());

            // Lấy danh sách ảnh cho từng chapter
            $images = $this->getImagesFromChapter($chapterLink, $client);

            // Thêm thông tin chapter vào mảng
            $chapters[] = [
                'link' => $chapterLink,
                'title' => $chapterTitle,
                'images' => $images,
            ];
        });

        // Đảo ngược thứ tự các chapter
        return array_reverse($chapters);
    }

    private function getImagesFromChapter($chapterLink, Client $client)
    {
        $images = [];
        $crawler = $client->request('GET', $chapterLink);

        // Trích xuất thông tin ảnh
        $crawler->filter('div.reading-content img')->each(function ($node) use (&$images) {
            $imageSrc = trim($node->attr('data-src'));

            // Thêm link ảnh vào mảng nếu không rỗng
            if (!empty($imageSrc)) {
                $images[] = $imageSrc;
            }
        });

        return $images;
    }
}
