<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class TreeNoteRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|integer',
        ];
    }
}
