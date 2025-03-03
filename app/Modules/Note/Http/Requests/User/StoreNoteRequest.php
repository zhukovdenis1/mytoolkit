<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;


class StoreNoteRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|integer|exists:notes,id',
            'title' => 'required|string|max:255',
            'text' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:note_categories,id',
        ];
    }
}
