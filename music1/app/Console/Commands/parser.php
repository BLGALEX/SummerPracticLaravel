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

        preg_match_all('#<a\s*href="/product/(\w+?)"#su', $html, $all_products);
        $categories = array();
        $i = 0;
        foreach(array_unique($all_products[1]) as $val)
        {
            $link = "https://www.muztorg.ru/product/".$val;
            $html = file_get_contents($link);
            preg_match('#<div\s*class="product-head">\s*\n\s*<a\s*href="/category/(.*?)"#su', $html, $category);
            preg_match('#<h1\s*class="product-title"\s*itemprop="name"\s*>(.*?)</h1>#su', $html, $name);
            preg_match('#<meta\s*itemprop="price"\s*content="(.*?)">#su', $html, $price);
            preg_match('#<img\s*id="slide1"\s*src=".*?"\s*data-src="(.*?)"#su', $html, $img);

            if(preg_match('#<div\s*class="panel-body"\s*id="mobile-characteristics">\s*\n*\s*<ul>(.*?)</ul>#su', $html, $c))
            {
            error_log('parse  '.$i.':'.$link);
            //Читаем характеристики в строку
            $charac = $c[1];
            $characteristics = "";
            while (preg_match('#<li>(.*?)</li>#su', $charac, $line)) {
                preg_match('#<b>(.*?)</b>#su', $line[0], $first);
                preg_match('#</b>(.*?)</li>#su', $line[0], $second);
                $characteristics = $characteristics . $first[1] . $second[1] . "\n";
                $charac = str_replace($line[0], '', $charac);
            }
            }
            else
            {
               $characteristics = null;
            }
            //Создаем обьект категории, если такой ещё нет
            if (Category::where('name', $category[1])->first() == null)
            {
                $category_instance = Category::create([
                    'name' => $category[1]
                ]);
            }
            $category_instance = Category::where('name', $category[1])->first();
            $category_instance->products()->create([
                'name' => $name[1],
                'picture' => $img[1],
                'price' => $price[1],
                'characteristics' => $characteristics
            ]);

            $i++;
            //Ограничиваемся парсингом 5 товаров
            if ($i == 40)
            {
                break;
            }
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
        $this->parse_link('https://www.muztorg.ru/search/%D0%B3%D0%B8%D1%82%D0%B0%D1%80%D0%B0');
        return 0;
    }
}
