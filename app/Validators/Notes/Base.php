<?php

namespace App\Validators\Notes;

use App\Models\Folder;
use App\Models\Notes;
use App\Validators\BaseValidator;
use Enums\SQL;

class Base extends BaseValidator
{

    protected array $skip = [
        'user_id',
        'updated_at',
        'pinned',
        'completed'
    ];

    public function validateBooleanValue(array $fields, string $key): bool
    {
        if (empty($fields[$key])) {
            return true;
        }

        $result = is_bool($fields[$key]) || $fields[$key] === 1;
        if (!$result) {
            $this->setErr(
                $key,
                "`$key` should be boolean"
            );
        }

        return $result;
    }

    public function validateFolderId(array $fields, bool $isRequired = true): bool
    {
        if (empty($fields['folder_id']) && !$isRequired) {
            return true;
        }

        return Folder::where('id', '=', $fields['folder_id'])
            ->startCondition()
            ->andWhere('user_id', '=', authId())
            ->orWhere('user_id', SQL::IS_OPERATOR->value, SQL::NULL->value)
            ->endCondition()
            ->exists();
    }

    public function checkTitleOnDuplication(string $title, int $folder_id, int $user_id): bool
    {
        $res = Notes::where('title', '=', $title)
            ->andWhere('user_id', '=', $user_id)
            ->andWhere('folder_id', '=', $folder_id)
            ->exists();

        if ($res) {
            $this->setErr('title', 'Title with the same name already exists in this directory');
        }

        return $res;

    }
}