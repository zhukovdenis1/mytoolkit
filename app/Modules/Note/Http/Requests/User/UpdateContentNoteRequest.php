<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class UpdateContentNoteRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'text' => 'nullable|string',
        ];
    }
}
