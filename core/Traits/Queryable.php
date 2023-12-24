<?php

namespace Core\Traits;

use PDO;

trait Queryable
{
    static protected string | null $tableName = null;

    static protected string $query = '';

    protected array $commands = [];

    static public function select(array $columns = ['*']):static
    {
        static::resetQuery();
        static::$query = "SELECT " . implode(', ', $columns) . " FROM " . static::$tableName . " ";
        $obj = new static;
        $obj->commands[] = 'select';

        return $obj;
    }


    static protected function resetQuery(): void
    {
        static::$query = '';
    }

    public function get(): array
    {
        return db()->query(static::$query)->fetchAll(PDO::FETCH_CLASS, static::class);
    }
}
