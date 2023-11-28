<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Rules\Password as FortifyPassword;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function passwordRules(): array
    {
        return [
            'required',
            'string',
            (new FortifyPassword)->length(8)
                ->requireNumeric()
                ->requireUppercase()
                ->requireSpecialCharacter(),
            Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised(),
            'confirmed'
        ];
    }
}
