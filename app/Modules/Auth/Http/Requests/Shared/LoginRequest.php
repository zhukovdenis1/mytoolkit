<?php

namespace App\Modules\Auth\Http\Requests\Shared;

use App\Http\Requests\BaseFormRequest;
use App\Modules\Course\Models\Course;
use Illuminate\Validation\Rule;

/**
 * Query parameters
 */
class LoginRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:3'
        ];
    }

    public function queryParameters(): array
    {
        return [
            'email' => ['example' => 'test@tset.ru'],
            'password' => ['example' => '123']
        ];
    }

}

