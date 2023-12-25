<?php

namespace App\Validators;

class BaseValidator
{
    protected array $rules = [], $err = [];

    public function validate(array $fields = []): bool
    {
        foreach ($fields as $key => $fieldValues){
            if(!empty($this->rules[$key] && preg_match($this->rules[$key], $fieldValues))){
                unset($this->err[$key]);
            }
        }
        return empty($this->err);
    }
    public function getErr():array
    {
        return $this->err;
    }

    public function setErr(string $key, string $err): void
    {
        $this->err[$key] = $err;
    }
}