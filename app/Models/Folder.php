<?php

namespace App\Models;

use Core\Model;

class Folder extends Model
{
    static protected string | null $tableName = 'folders';

    public string $title, $created_at, $updated_at;

    public int|null $user_id;
}