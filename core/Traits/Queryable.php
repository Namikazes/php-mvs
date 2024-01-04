<?php

namespace Core\Traits;

use Enums\SQL;
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

    public function update(array $fields): static
    {
        $query = "UPDATE " . static::$tableName . " SET " . $this->updatePlaceholders(array_keys($fields)) . " WHERE id = :id";
        $query = db()->prepare($query);

        $fields['id'] = $this->id;
        $query->execute($fields);

        return static::find($this->id);
    }

    protected function updatePlaceholders(array $keys): string
    {
        $str = '';
        $lastKey = array_key_last($keys);

        foreach ($keys as $index => $key) {
            $str .= "$key = :$key" . ($lastKey === $index ? "" : ", ");
         }
        return $str;
    }

    static public function all(): array
    {
        return static::select()->get();
    }

    static public function find(int $id): static | false
    {
        $query = db()->prepare(" SELECT * FROM " . static::$tableName . " WHERE id = :id ");
        $query->bindParam('id', $id);
        $query->execute();

        return $query->fetchObject(static::class);
    }

    static public function findBy(string $column, $value): static | false
    {
        $query = db()->prepare(" SELECT * FROM " . static::$tableName . " WHERE $column = :$column ");
        $query->bindParam($column, $value);
        $query->execute();

        return $query->fetchObject(static::class);
    }

    static public function create(array $fields): null|static
    {
        $params = static::preperQueryParams($fields);
        $query = db()->prepare("INSERT INTO " . static::$tableName . " ($params[keys]) VALUES ($params[pleceholders])");

        if (!$query->execute($fields)) {
            return null;
        }

        $query->closeCursor();

        return static::find( db()->lastInsertId());
    }

    static public function remove(int $id):bool
    {
        $query = db()->prepare("DELETE FROM " . static::$tableName . " WHERE id = :id ");
        $query->bindParam('id', $id);
        return $query->execute();
    }

    static protected function preperQueryParams(array $fields): array
    {
        $keys = array_keys($fields);
        $pleceholders = preg_filter('/^/', ':', $keys);

        return [
            'keys' => implode(', ', $keys),
            'pleceholders' => implode(', ', $pleceholders)
        ];
    }

    static public function __callStatic(string $name, array $args): mixed
    {
        if(in_array($name, ['where'])) {
            $obj =  static::select();
            return call_user_func_array([$obj, $name], $args);
        }
    }

    public function __call(string $name, array $args): mixed
    {
        if(in_array($name, ['where'])) {
            return call_user_func_array([$this, $name], $args);
        }
    }


    static protected function resetQuery(): void
    {
        static::$query = '';
    }

     protected function where(string $column, string $operator, $value = null): static
    {
        if ($this->prevent(['group', 'limit', 'order', 'having'])) {
            throw new \Exception(
                static::class .
                ": WHERE can not be after ['group', 'limit', 'order', 'having']"
            );
        }

        $obj = in_array('select', $this->commands) ? $this : static::select();

        if (
            !is_null($value) &&
            !is_bool($value) &&
            !is_numeric($value) &&
            !is_array($value) &&
            !in_array($operator, [SQL::IN_OPERATOR->value, SQL::NOT_IN_OPERATOR->value]) &&
            $value !== SQL::NULL->value
        ) {
            $value = "'$value'";
        }

        if (is_null($value)) {
            $value = 'NULL';
        }

        if (is_array($value)) {
            $value = array_map(fn($item) => is_string($item) && $item !== SQL::NULL->value ? "'$item'" : $item, $value);
            $value = '('. implode(', ', $value) .')';
        }

        if (!in_array('where', $obj->commands)) {
            static::$query .= "WHERE";
        }

        static::$query .= " $column $operator $value";
        $this->commands[] = 'where';

        return $obj;
    }

    public function startCondition(): static
    {
        $this->commands[] = 'startCondition';
        return $this;
    }


    public function endCondition(): static
    {
        $this->commands[] = 'endCondition';
        static::$query .= ') ';

        return $this;
    }

    public function andWhere(string $column, string $operator, $value = null): static
    {
        static::$query .= " AND" . (in_array('startCondition', $this->commands) ? ' (' : '');
        return $this->where($column, $operator, $value);
    }

    public function orWhere(string $column, string $operator, $value = null): static
    {
        static::$query .= " OR";
        return $this->where($column, $operator, $value);
    }

    public function orderBy(array $columns): static
    {
        if (!$this->prevent(['select'])) {
            throw new \Exception(
                static::class .
                ": [ORDER BY] can not be called before [SELECT]"
            );
        }

        $this->commands[] = 'order';
        $lastKey = array_key_last($columns);
        static::$query .= " ORDER BY ";

        foreach ($columns as $column => $order) {
            static::$query .= "$column $order->value" . ($column === $lastKey ? "" : ", ");
        }

        return $this;
    }

    public function sql():string
    {
        return static::$query;
    }

    protected function prevent(array $allowedMethods): bool
    {
        foreach ($allowedMethods as $method) {
            if (in_array($method, $this->commands)) {
                return true;
            }
        }

        return false;
    }

    public function exists():bool
    {
        if (!$this->prevent(['select'])) {
            throw new \Exception(
                static::class .
                ": exists can not be called before ['select']"
            );
        }

        $result = $this->get();
        return !empty($result);
    }


    public function get(): array
    {
        return db()->query(static::$query)->fetchAll(PDO::FETCH_CLASS, static::class);
    }
}
