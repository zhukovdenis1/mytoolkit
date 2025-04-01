<?php

namespace App\Http\Requests;

use App\DTOs\DTO;
use App\Models\BaseModel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

abstract class BaseFormRequest extends FormRequest
{
    protected bool $withUserId = false;

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException(
            $validator,
            response()->json(['errors' => $validator->errors()], 422)
        );
    }

    /*public function dto(array $extraData = []): DTO
    {
        return DTO::fromArray($this->getValidated($extraData));
    }*/

    /*public function dtoWithUserId(): object
    {
        return $this->dto($this->getWithUserId());
    }*/

//    public function getValidated(array $extraData = []): array
//    {
//        return array_merge($this->validated(), $extraData);
//    }
//
//    public function getWithUserId(): array
//    {
//        return $this->getValidated(['user_id' => $this->user()->id]);
//    }
//
//    protected function verifyUser(?BaseModel $model): bool
//    {
//        return $model && $model->user_id === $this->getUserId();
//    }
//
//    protected function getUserId(): ?int
//    {
//        return auth()->id();
//    }

    public function withUserId()
    {
        $this->withUserId = true;

        $this->merge([
            'user_id' => $this->user()->id
        ]);

        return $this;
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        if ($this->withUserId && $this->user()) {
            $validated['user_id'] = $this->user()->id;
        }

        return $validated;
    }


}
