<?php

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Product;
use App\Models\Server;
use GuzzleHttp\Client;
use Str;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Console\Command;

class CrawlMangaRaw extends Command
{
    protected $signature = 'crawl:manga-sub';
    protected $description = 'Crawl manga products and send to Laravel endpoint';
}
