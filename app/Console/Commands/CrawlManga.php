<?php

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Product;
use App\Models\Server;
use GuzzleHttp\Client;
use Str;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Console\Command;

class CrawlManga extends Command
{
    protected $signature = 'crawl:manga';
    protected $description = 'Crawl manga products and send to Laravel endpoint';

    private $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36'
            ]
        ]);
    }

    public function handle()
    {
        $url = 'https://manga18fx.com/manga-genre/raw';
        $this->crawlMultipleProducts($url);
    }

    private function crawlMultipleProducts($url)
    {
        try {
            $response = $this->client->get($url);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            $this->info('Fetching products from ' . $url); // Thông báo đang lấy sản phẩm

            $crawler->filter('.page-item')->each(function (Crawler $node, $i) {
                if ($i >= 3) { // Nếu đã lấy 3 sản phẩm thì dừng vòng lặp
                    return; // Dừng lại nếu đã đạt đủ số lượng
                }
                    $link = $node->filter('a')->attr('href');
                    $title = $node->filter('h3.tt a')->text();
                    $image = $node->filter('.thumb-manga a img')->attr('data-src');
                    $slug = Str::slug($title);

                    // Tạo hoặc lấy sản phẩm
                    $product = Product::firstOrCreate(
                        ['slug' => $slug], // Điều kiện tìm kiếm
                        [ // Dữ liệu để tạo mới
                            'url_avatar' => $image,
                            'name' => $title,
                        ]
                    );

                    $this->info('Created or retrieved product: ' . $product->name); // Thông báo đã tạo hoặc lấy sản phẩm

                    // Lấy chương cho sản phẩm
                    $this->getChapters('https://manga18fx.com/' . $link, $product->id);
            });
        } catch (\Exception $e) {
            $this->error('Error fetching products: ' . $e->getMessage());
        }
    }

    private function getChapters($productLink, $product_id)
    {
        try {
            $response = $this->client->get($productLink);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            $chapters = [];

            // Lưu trữ các chương vào một mảng
            $crawler->filter('ul.row-content-chapter li.a-h')->each(function (Crawler $node) use (&$chapters, $product_id) {
                $chapterLink = $node->filter('a')->attr('href');
                $chapterTitle = $node->filter('a')->text();
                $chapterNumber = $this->extractChapterNumber($chapterTitle); // Lấy số chương từ tiêu đề

                // Thêm vào mảng các chương
                $chapters[] = [
                    'link' => 'https://manga18fx.com/' . $chapterLink,
                    'title' => $chapterTitle,
                    'number' => $chapterNumber
                ];
            });

            // Sắp xếp các chương từ mới nhất đến cũ nhất
            usort($chapters, function ($a, $b) {
                return $b['number'] <=> $a['number']; // Sắp xếp theo số chương giảm dần
            });

            // Lưu vào cơ sở dữ liệu
            foreach ($chapters as $chapter) {
                $episode = Episode::firstOrCreate(
                    [
                        'product_id' => $product_id,
                        'slug' => Str::slug($chapter['title']),
                    ],
                    [
                        'name' => $chapter['title'],
                    ]
                );

                $this->getImagesFromChapter($chapter['link'], $episode->id);
            }

            return array_reverse($chapters); // Ngược lại để giữ thứ tự từ mới đến cũ
        } catch (\Exception $e) {
            $this->error('Error fetching chapters: ' . $e->getMessage());
            return [];
        }
    }

    // Hàm lấy số chương từ tiêu đề
    private function extractChapterNumber($title)
    {
        // Giả sử tiêu đề có định dạng "Chapter 1", "Chapter 100", bạn có thể điều chỉnh regex cho phù hợp
        preg_match('/\d+/', $title, $matches);
        return isset($matches[0]) ? (int)$matches[0] : 0; // Trả về số chương hoặc 0 nếu không tìm thấy
    }

    private function getImagesFromChapter($chapterLink, $episode_id)
    {
        try {
            $response = $this->client->get($chapterLink);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            $images = [];

            $crawler->filter('div.read-content .page-break img')->each(function (Crawler $node) use (&$images, $episode_id) {
                $imageSrc = $node->attr('data-src');
                if ($imageSrc) {
                    $images[] = $imageSrc;
                }
            });

            // Lưu hình ảnh vào bảng servers
            Server::create([
                'episode_id' => $episode_id,
                'images' => $images// Lưu dưới dạng JSON
            ]);

            return $images;
        } catch (\Exception $e) {
            $this->error('Error fetching images: ' . $e->getMessage());
            return [];
        }
    }
}
