<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User\File;

use App\Http\Requests\BaseFormRequest;

class DestroyNoteFileRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return $this->verifyUser($this->route('note'));
    }

    public function rules(): array
    {
        return [
            'store_id' => 'required|integer|in:1',
            'path' => 'required|string|max:255',
        ];
    }
}
