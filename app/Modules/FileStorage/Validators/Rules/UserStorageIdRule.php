<?php

declare(strict_types=1);

namespace App\Modules\FileStorage\Validators\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserStorageIdRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (auth()->id() == 1001) {

            if ($value != 1 && $value != 2) {
                $fail('Wrong storage*.');
            }
        } elseif ($value !== 3) {
            $fail('Wrong storage.');
        }
    }
}
