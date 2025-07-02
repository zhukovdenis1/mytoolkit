<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class ParentNoteRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'note_id' => 'integer',
        ];
    }
}
