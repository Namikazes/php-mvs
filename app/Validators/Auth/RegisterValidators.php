<?php

namespace App\Validators\Auth;

class RegisterValidators extends Base
{
    protected array $rules = [
        'email' => '/^[a-zA-Z0-9.!#$%&\'*+\/\?^_`{|}~-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i',
        'password' => '/[a-zA-Z0-9.!#$%&\'*+\/\?^_`{|}~-]{8,}/'
    ];
    protected array $err = [
        'email' => 'Email incorrect',
        'password' => 'Password incorrect',
    ];

    public function validate(array $fields = []): bool
    {
        $result = [
            parent::validate($fields),
            !$this->checkEmailOnExists($fields['email'])
        ];

        return !in_array(false, $result);
    }
}
