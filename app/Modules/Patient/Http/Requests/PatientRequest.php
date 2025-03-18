<?php

declare(strict_types=1);

namespace App\Modules\Patient\Http\Requests;

use App\Http\Requests\BaseFormRequest;

class PatientRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

