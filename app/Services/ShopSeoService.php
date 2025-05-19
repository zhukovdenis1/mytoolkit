<?php

declare(strict_types = 1);

namespace App\Services;

use Illuminate\Http\Request;

class ShopSeoService
{
    public function getNoIndexTag(string $noIndex, Request $request): string
    {
        if ($noIndex == 'false') {
            $noIndex = false;
        } elseif ($noIndex == 'true') {
            $noIndex = true;
        } else {
            $noIndex = false;
            $routeName = $request->route()?->getName();
            if (!empty($request->all()) || $routeName == 'category' || $routeName == 'coupon.detail') {
                $noIndex = true;
            }
        }

        return $noIndex ? '<meta name="robots" content="noindex"/>' : '';
    }

    public function getTitle(string $title, Request $request): string
    {
        return empty($title) ? 'Недорогой интернет-магазин с бесплатной доставкой / DealExtreme на русском языке' : trim(str_replace("\n", ' ',$title));//\n на детальной странице товара
    }

    public function getKeywords(string $keywords, Request $request): string
    {
        return empty($keywords)
            ? ''
            : trim($keywords);
    }

    public function getDescription(string $description, Request $request): string
    {
        return empty($description)
            ? ''
            : trim($description);
    }
}
