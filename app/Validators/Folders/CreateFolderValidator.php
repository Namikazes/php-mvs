<?php

namespace App\Validators\Folders;

use App\Models\Folder;
use App\Validators\BaseValidator;

class CreateFolderValidator extends BaseValidator
{
    protected array $rules = [
        "title" => '/[\w\d\s\(\)\-]{3,}/i'
    ];

    protected array $err = [
        "title" => 'Title should contain characters, numbers and _-() symbols and has length more than 2 symbols'
    ];

    protected array $skip = [
        'user_id',
        'updated_at'
    ];

    protected function checkOnDuplicateTitle(null|int $userId, string $title):bool
    {
       $result = !Folder::where('user_id', '=', $userId)
            ->andWhere('title', '=', $title)
            ->exists();

       if(!$result){
           $this->setErr('title', 'Not found');
       }

       return $result;
    }

    public function validate(array $fields = []): bool
    {
        $result = [
            parent::validate($fields),
            $this->checkOnDuplicateTitle($fields['user_id'], $fields['title']),
        ];

        return !in_array(false, $result);
    }

}