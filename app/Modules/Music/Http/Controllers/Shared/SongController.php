<?php

declare(strict_types=1);

namespace App\Modules\Music\Http\Controllers\Shared;

use App\Helpers\DateTimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Music\Models\Song;
use App\Modules\Music\Models\SongSinger;
use App\Modules\Shop\Models\ShopCategory;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\Shop\Services\ShopCouponService;
use App\Modules\Shop\Services\ShopService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class SongController extends Controller
{
    public function indexOld(Request $request)
    {
        $songs = Song::with(['singer' => function($query) {
            $query->orderBy('name', 'asc');
            }])
            ->whereNotNull('published_at')
            ->orderBy('title', 'asc')
            ->get()
            ->groupBy('singer.name')
            ->sortKeys(); // Сортировка групп по имени исполнителя

        $result = $songs->map(function ($songs, $singerName) {
            return [
                'name' => $singerName,
                'songs' => $songs->sortBy('title')->map(function ($song) {
                    return $song->only(['id', 'title', 'keys', 'key_orig', 'speed', 'text']);
                })->values()->toArray()
            ];
        })->values()->toArray();

        return view('music/index', ['data' => $result]);
    }

    public function index(Request $request)
    {

        $songs = Song::from('songs as s')
            ->select('s.id', 's.title', 'ss.name as singer_name')
            ->join('song_singers as ss', 's.singer_id', '=', 'ss.id')
            ->whereNotNull('s.published_at')
            ->orderBy('ss.name', 'asc')
            ->orderBy('s.title', 'asc')
            ->get();

        $result = $songs->groupBy('singer_name')
            ->map(function ($songs, $singerName) {
                return [
                    'name' => $singerName,
                    'songs' => $songs->map(function ($song) {
                        return [
                            'id' => $song->id,
                            'title' => $song->title
                        ];
                    })->toArray()
                ];
            })
            ->values() // Сбрасываем ключи для последовательного массива
            ->toArray();

        return view('music/index', ['data' => $result]);
    }

    public function detail(Song $song)
    {
        $song->load('singer');

        $keys = $song->keys
            ? implode(' ', explode(',', $song->keys))
            : $song->key_orig;
        $keys = $song->key_orig ? $keys . ' ' . $song->key_orig . '*' : $keys;

        return view('music/detail', [
            'song' => $song,
            'singer' => $song->singer,
            'keys' => trim($keys)
        ]);
    }
}
