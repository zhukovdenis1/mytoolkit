<?php

declare(strict_types=1);

namespace App\Modules\FileStorage\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use App\Modules\FileStorage\Validators\Rules\FilePathRule;
use Illuminate\Support\Facades\Config;

class GetFileRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'pathData' => [new FilePathRule()],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'pathData' => [
                'user_id' => $this->route('user_id'),
                'file' => $this->route('file'),
                'module_id' => $this->route('module_id'),
                'module_name' => $this->route('module_name'),
                'file_name' => $this->route('file_name'),
                'file_ext' => $this->route('file_ext'),
            ],
        ]);
    }
}
