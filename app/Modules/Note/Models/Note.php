<?php

declare(strict_types=1);

namespace App\Modules\Note\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Note extends BaseModel
{
    use HasFactory;

    protected $fillable = ['title', 'text', 'user_id', 'parent_id'];
    protected $dates = ['created_at', 'updated_at'];

    protected $hidden = ['user_id'];


    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(NoteCategory::class, 'note_note_category');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*public function getCreatedAtAttribute($value): Carbon
    {
        return Carbon::parse($value);
    }

    public function getUpdatedAtAttribute($value): Carbon
    {
        return Carbon::parse($value);
    }*/
}
