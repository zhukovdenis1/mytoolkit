<?php

declare(strict_types=1);

namespace App\Modules\Shop\Services;
use App\Helpers\ArticleHelper;
use App\Helpers\EditorHelper;
use App\Modules\Shop\Models\ShopCoupon;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ShopService extends BaseService
{
    public function __construct(
        private readonly ArticleHelper  $articleHelper
    ){}


    public function getArticleData(): array
    {
       return $this->articleHelper->getDataByCode('home', 0);
    }
}
