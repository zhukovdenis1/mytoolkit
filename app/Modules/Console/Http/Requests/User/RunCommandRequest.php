<?php

declare(strict_types=1);

namespace App\Modules\Console\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;


class RunCommandRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'command' => $this->route('command'),
        ]);
    }

    public function rules(): array
    {
        return [
            'command' => 'required|string|in:deploy,test',
        ];
    }
}
