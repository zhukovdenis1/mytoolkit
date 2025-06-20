<?php

declare(strict_types=1);

namespace App\Modules\Note\Services\Shared;

use App\Exceptions\ErrorException;
use App\Models\BaseModel;
use App\Modules\Note\Models\Note;
use App\Modules\Note\Models\NoteCategory;
use App\Services\BaseService;
use Carbon\Carbon;
use Faker\Provider\Base;
use Illuminate\Database\Eloquent\Builder;

class NoteService
{
    public function jsonToHtml(string $json)
    {

    }
}
