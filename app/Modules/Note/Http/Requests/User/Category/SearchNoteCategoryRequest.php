<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User\Category;

use App\Http\Requests\BaseFormRequest;


class SearchNoteCategoryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer|exists:note_categories,id'
        ];
    }
}
