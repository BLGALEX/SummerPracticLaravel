<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use function GuzzleHttp\Promise\all;
use function PHPUnit\Framework\throwException;

class parser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:parser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function parse_product(string $link){

    }

    public function parse_link(string $link){
        $html = file_get_contents($link);
        assert(html!=false);

        preg_match_all('#<a\s*href="/product/(\w+?)"#', $html, $all_products);
        $categories = array();
        $i = 0;
        foreach(array_unique($all_products[1]) as $val)
        {
            $link = "https://www.muztorg.ru/product/".$val;
            $html = file_get_contents($link);

            preg_match('#<div\s*class="product-head">\s*\n\s*<a\s*href="/category/(.*?)"#', $html, $category);
            preg_match('#<h1\s*class="product-title"\s*itemprop="name"\s*>(.*?)</h1>#', $html, $name);
            preg_match('#<meta\s*itemprop="price"\s*content="(.*?)">#', $html, $price);
            preg_match('#<img\s*id="slide1"\s*src=".*?"\s*data-src="(.*?)"#', $html, $img);

            assert($category !== false && $name !== false && $price !== false && $img !== false);

            $categories[] = $category[1];

            $new_product = Product::create([
                'name' => $name[1],
                'category_name' => $category[1],
                'picture' => $img[1],
                'price' => $price[1]
            ]);

            $i++;
            error_log($i);
        }
        foreach(array_unique($categories) as $val){
            $new_category = Category::create([
                'name' => $val
            ]);
        }
        return 0;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->parse_link('https://www.muztorg.ru/search/%D0%B3%D0%B8%D1%82%D0%B0%D1%80%D0%B0?all-stock=1&per-page=96');
        return 0;
    }
}
