<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class UpdateNoteCategoryRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return $this->verifyUser($this->route('category'));
    }

    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:note_categories,id',
            'name' => 'required|string|max:255',
        ];
    }
}
