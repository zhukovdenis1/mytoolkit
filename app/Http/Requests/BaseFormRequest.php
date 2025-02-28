<?php

namespace App\Http\Requests;

use App\DTOs\DTO;
use App\Models\BaseModel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        //die('look BaseFormRequest*');
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }

    /*public function dto(array $extraData = []): DTO
    {
        return DTO::fromArray($this->getValidated($extraData));
    }*/

    /*public function dtoWithUserId(): object
    {
        return $this->dto($this->getWithUserId());
    }*/

    public function getValidated(array $extraData = []): array
    {
        return array_merge($this->validated(), $extraData);
    }

    public function getWithUserId(): array
    {
        return $this->getValidated(['user_id' => $this->user()->id]);
    }

    protected function verifyUser(?BaseModel $model): bool
    {
        return $model && $model->user_id === auth()->id();
    }
}
