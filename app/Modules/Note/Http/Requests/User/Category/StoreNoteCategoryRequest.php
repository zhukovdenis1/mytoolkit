<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User\Category;

use App\Http\Requests\BaseFormRequest;

class StoreNoteCategoryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|integer|exists:note_categories,id',
            'name' => 'required|string|max:255',
        ];
    }
}
