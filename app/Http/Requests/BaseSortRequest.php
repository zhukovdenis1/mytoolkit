<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Query parameters
 */
class BaseSortRequest extends BaseFormRequest
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
     * The sortable fields.
     *
     * @var array
     */
    protected array $sortableFields = [];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            '_page' => 'nullable|integer|min:1',
            '_limit' => 'nullable|integer|min:1|max:1000',
            '_sort' => [
                'nullable', 'required_with:_order',
                Rule::in($this->sortableFields),
            ],
            '_order' => 'nullable|in:asc,desc',
        ];
    }

    public function queryParameters(): array
    {
        return [
            '_page' => [
                'description' => 'Page number',
                'example' => '1',
            ],
            '_limit' => [
                'description' => 'Amount of items on page',
                'example' => '10',
            ],
            '_sort' => [
                'description' => 'Sort by field name',
                'example' => '',
                'type' => 'string'
            ],
            '_order' => [
                'description' => 'Sort direction',
                'example' => '',
            ]
        ];
    }
}
