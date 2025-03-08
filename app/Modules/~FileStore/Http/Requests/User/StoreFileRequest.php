<?php

declare(strict_types=1);

namespace App\Modules\FileStore\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class StoreFileRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id' => 'required|integer|in:1',
            'path' => 'required|string|max:255',
        ];
    }
}
