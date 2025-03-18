<?php

declare(strict_types=1);

namespace App\Modules\Patient\Models;

use App\Models\BaseModel;
use App\Modules\Patient\Observers\PatientObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends BaseModel
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'birthdate', 'age', 'age_type'];

    protected $dates = ['created_at', 'updated_at', 'birthdate'];

    protected $casts = [
        'first_name' => 'string',
        'last_name' => 'string',
        'age' => 'integer',
        'age_type' => 'string',
        'birthdate' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['name'];

    /**
     * Не используется. Просто хотел показать, что знаю про такой способ
     */
    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    protected static function boot()
    {
        parent::boot();
        static::observe(PatientObserver::class);
    }
}
