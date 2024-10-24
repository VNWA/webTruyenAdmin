<?php

namespace App\Console\Commands;

use Goutte\Client;
use Illuminate\Console\Command;

class CrawlData extends Command
{

    protected $signature = 'crawl:data';
    protected $description = 'Crawl dữ liệu từ website tự động';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Dùng Goutte để crawl dữ liệu
        $client = new Client();
        $url = 'https://www.webtoon.xyz/';

        $crawler = $client->request('GET', $url);

        // Lấy tiêu đề trang
        $title = $crawler->filter('title')->text();
        $this->info("Tiêu đề trang: $title");

        // Lưu hoặc xử lý dữ liệu thêm tại đây
        $links = $crawler->filter('a')->each(function ($node) {
            return $node->attr('href');
        });

        $this->info("Đã tìm thấy " . count($links) . " liên kết.");

        // Lưu vào file log hoặc database
        \Log::info('Crawl dữ liệu thành công', ['title' => $title, 'links' => $links]);
    }
}
