<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class AddCategoriesToNoteRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:note_categories,id',
        ];
    }
}
