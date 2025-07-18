<?php

namespace App\Http\Resources;

use App\Modules\Profile\Http\Resources\Shared\ProfileResource;
use App\Modules\Profile\Models\Profile;
//use App\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'user';

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'email' => $this->email,
            'role' => Cache::remember('users_' . $this->id . '_role', now()->addDay(), function () {
                return $this->role->name;
            }),
            'last_online_at' => $this->last_online_at ? $this->last_online_at->format('d.m.Y, H:i:s') : null,
            'profile' => new ProfileResource(
                Profile::where('user_id', $this->id)
                    ->first()
            )
        ]);
    }
}
