<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User\Category;

use App\Http\Requests\BaseFormRequest;

class ShowCategoryRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return $this->verifyUser($this->route('category'));
    }
}
