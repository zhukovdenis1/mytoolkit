<?php

declare(strict_types=1);

namespace App\Modules\Note\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NoteCategory extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id', 'user_id'];
    protected $dates = ['created_at', 'updated_at'];

    protected $hidden = ['pivot', 'user_id'];

    public function getCreatedAtAttribute($value): Carbon
    {
        return Carbon::parse($value);
    }

    public function getUpdatedAtAttribute($value): Carbon
    {
        return Carbon::parse($value);
    }

    public function children(): HasMany
    {
        return $this->hasMany(NoteCategory::class, 'parent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
