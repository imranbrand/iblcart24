<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [
            ['loc' => url('/'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('products.index'), 'lastmod' => now()->toAtomString()],
        ];

        Product::query()
            ->select(['id', 'updated_at'])
            ->orderBy('id')
            ->each(function ($product) use (&$urls) {
                $urls[] = [
                    'loc' => route('products.show', $product),
                    'lastmod' => optional($product->updated_at)->toAtomString(),
                ];
            });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $u) {
            $xml .= '  <url><loc>' . e($u['loc']) . '</loc>';
            if (!empty($u['lastmod'])) {
                $xml .= '<lastmod>' . $u['lastmod'] . '</lastmod>';
            }
            $xml .= '</url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
