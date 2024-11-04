<?php

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Product;
use App\Models\Server;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class CrawlMangaRaw extends Command
{
    protected $signature = 'crawl:manga-raw';
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
        $url = 'https://zonatmo.com/library';
        $this->crawlMultipleProducts($url);
    }

    private function crawlMultipleProducts($url)
    {
        try {
            $response = $this->client->get($url);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            $this->info('Fetching products from ' . $url); // Informing about fetching products
            $crawler->filter('.element.col-6.col-sm-6.col-md-4.col-lg-3.col-xl-2.mt-2')->each(function (Crawler $node, $i) {
                // Adjusted the filter to properly match the title
                $title = $node->filter('.thumbnail-title h4')->text();
                $this->info('Manga: ' . $title); // Informing about the manga title

                // Here you can add code to save the title to the database if needed
                // For example:
                // Product::create(['title' => $title]);
            });
        } catch (\Exception $e) {
            $this->error('Error fetching products: ' . $e->getMessage());
        }
    }
}
