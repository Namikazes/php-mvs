<?php

namespace Core;

abstract class Controller
{
    public function before(string $action, array $params = []): bool
    {
        return true;
    }
    public function after(string $action) {}

    protected function response(int $code = 200, array $body = [], array $err = []): array
    {
        return [
          'code' => 200,
          'body' => $body,
          'err' => $err,
        ];
    }
}