<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class UpdateNoteRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return $this->verifyUser($this->route('note'));
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'text' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:note_categories,id',
        ];
    }
}
