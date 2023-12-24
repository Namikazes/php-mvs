<?php

namespace Core;

class Config
{

    protected array $configs = [];

    static protected Config | null $instance = null;

    static public function get(string $params): string | null
    {
        if(is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance->getParams($params);
    }

    public function getParams(string $params): string | null
    {
        $keys = explode('.', $params);
        return $this->findParamsByKeys($keys, $this->retrieveConfigs());
    }

    protected function retrieveConfigs(): array
    {
        if(empty($this->configs)){
            $this->configs = include CONFIG_DIR . '/configurations.php';
        }
        return $this->configs;
    }

    protected function findParamsByKeys(array $keys = [], array $configs = []): string | null
    {
        $value = null;

        if(empty($keys)) {
            return $value;
        }

        $needle = array_shift($keys);

        if(array_key_exists($needle, $configs)) {
            $value = is_array($configs[$needle])
                ? $this->findParamsByKeys($keys, $configs[$needle])
                : $configs[$needle];
        }

        return $value;
    }
}