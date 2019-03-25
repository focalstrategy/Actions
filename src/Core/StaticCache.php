<?php

namespace FocalStrategy\Actions\Core;

class StaticCache
{
    protected $data = [];

    public function gen(...$params) : string
    {
        return md5(serialize($params));
    }

    public function has(string $key)
    {
        return isset($this->data[$key]);
    }

    public function get(string $key)
    {
        if ($this->has($key)) {
            return $this->data[$key];
        }

        return null;
    }

    public function put(string $key, $data)
    {
        $this->data[$key] = $data;
    }
}
