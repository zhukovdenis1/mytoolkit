<?php

namespace App\Modules\FileStorage\Validators\Rules;

use App\Modules\FileStorage\Models\File;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FilePathRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var File $file */
        $file = $value['file'];

        if ($file->user_id != $value['user_id']
            || $file->module_id != $value['module_id']
            || $file->module_name != $value['module_name']
            || $file->name != $value['file_name']
            || $file->ext != $value['file_ext']
        ) {
            $fail('Wrong path');
        }
    }
}
