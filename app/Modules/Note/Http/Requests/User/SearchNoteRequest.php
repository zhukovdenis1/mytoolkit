<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\BaseSearchRequest;


class SearchNoteRequest extends BaseSearchRequest
{
    protected array $sortableFields = ['id', 'title'];

    public function rules(): array
    {
//        return array_merge(parent::rules(), [
//            'category' => 'nullable|integer|exists:note_categories,id',
//        ]);
        return array_merge(parent::rules(), [
            'categories'   => 'nullable|array',
            'categories.*' => 'integer',
            //'categories.*' => 'integer|exists:note_categories,id',
        ]);
    }
}
