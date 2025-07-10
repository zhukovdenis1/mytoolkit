<?php

declare(strict_types=1);

namespace App\Modules\Console\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;


class RunCommandRequest extends BaseFormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'command' => $this->route('command'),
            'category' => $this->route('category'),
        ]);
    }

    public function rules(): array
    {
        return [
            'category' => 'nullable|string|in:shop',
            'command' => 'required|string|in:deploy,refresh,test,epnHot,coupons,parseVkGroups,post,refreshImportantProducts,genArticles',
        ];
    }
}
