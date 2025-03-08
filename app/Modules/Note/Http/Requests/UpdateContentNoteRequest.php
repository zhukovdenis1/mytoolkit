<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests;

use App\Http\Requests\BaseFormRequest;

class UpdateContentNoteRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return $this->verifyUser($this->route('note'));
    }

    public function rules(): array
    {
        return [
            'text' => 'nullable|string',
        ];
    }
}
