<?php

declare(strict_types = 1);

namespace App\Console\Commands\Shop;

use App\Helpers\StringHelper;
use App\Modules\Shop\Models\ShopCoupon;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\Shop\Models\ShopProductParseQueue;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Modules\Shop\Services\EpnApiClient;
use Illuminate\Support\Facades\DB;

class RefreshImportantProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:refreshImportantProducts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $output = '';

        $product = ShopProduct::query()
            //->where('epn_month_income' > 3000)
            //->where('updated_at', '<', Carbon::now()->subWeek(2))
            ->where('updated_at', '<', Carbon::now()->subDay(1))
            ->orderBy('epn_month_income', 'desc')
            ->first();

        $queue = ShopProductParseQueue::query()
            ->where('id_ae', $product->id_ae)
            ->first();


        if (empty($queue)) {
            ShopProductParseQueue::create(
                [
                    'id_ae' => $product->id_ae,
                    'important' => 1
                ]
            );
        } else {
            $queue->update([
                'important' => 1,
                'parsed_at' => null
            ]);
        }

        $product->update(['updated_at' => Carbon::now()]);

        $output .= 'id = ' . $product->id . ' ; title_ae=' . $product->title_ae;

        $this->info($output);
        return 0; // Возвращаем 0, если команда выполнена успешно
    }
}
