<?php

namespace App\Validators\Notes;

use App\Models\Notes;

class UpdateNotesValidator extends Base
{

    public function __construct(protected Notes $note){}

    public function validateTitle(array $fields): bool
    {
        if(!isset($fields['title'])) {
            return true;
        }

        $res = preg_match('/[\w\d\s\(\)\-]{3,}/i', $fields['title']);
        if(!$res) {
            $this->setErr('title', 'Title should contain characters, numbers and _-() symbols and has length more than 2 symbols');
        }

        return $res && $this->checkTitleOnDuplication(
                $fields['title'],
                $fields['folder_id'] ?? $this->note->folder_id,
                $this->note->user_id
            );
    }
    public function validate(array $fields = []): bool
    {

        return !in_array(
            false,
            [
                parent::validate($fields),
                $this->validateFolderId($fields, false),
                $this->validateTitle($fields),
                $this->validateBooleanValue($fields, 'pinned'),
                $this->validateBooleanValue($fields, 'completed'),
            ]
        );
    }
}
