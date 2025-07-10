<?php

declare(strict_types=1);

namespace App\Modules\Shop\Http\Controllers\Admin;


use App\Http\Controllers\BaseController;
use App\Http\Resources\AnonymousResource;
use Illuminate\Http\Request;


class ShopController extends BaseController
{
    public function __construct()
    {
    }

    public function getSiteList(Request $request):AnonymousResource
    {
        $validated = $request->validate([
            'group' => ['nullable', 'string', 'min:1', 'max:255'],
        ]);
        $group = $validated['group'] ?? null;
        $sites = config('sites') ?? [];
        $result = [];
        foreach ($sites as $s) {
            if (is_null($group) || $s['group'] == $group) {
                $result[] = [
                    'id' => $s['id'],
                    'name' => $s['id'] . '. ' . $s['hosts'][0]
                ];
            }
        }
        return new AnonymousResource($result);
    }

}
