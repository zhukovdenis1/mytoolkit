<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseSortRequest as FormRequest;

/**
 * Query parameters
 */
class BaseSearchRequest extends FormRequest
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
        return array_merge(parent::rules(), [
            'search' => 'nullable|string|max:255',
        ]);
    }

    public function queryParameters(): array
    {
        return array_merge(parent::queryParameters(), [
            'search' => ['example' => '']
        ]);
    }

}
