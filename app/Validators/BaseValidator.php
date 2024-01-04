<?php

namespace App\Validators;

class BaseValidator
{
    protected array $rules = [], $err = [], $skip = [];

    public function validate(array $fields = []): bool
    {
        if(empty($this->rules)) {
            return true;
        }
        foreach ($fields as $key => $fieldValues){
            if(in_array($key, $this->skip)){
                continue;
            }

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