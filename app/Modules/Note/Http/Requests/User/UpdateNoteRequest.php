<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class UpdateNoteRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:notes,id',
            'title' => 'nullable|string|max:255',
            'text' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:note_categories,id',
        ];
    }
}
