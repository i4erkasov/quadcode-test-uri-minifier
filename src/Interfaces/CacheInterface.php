<?php

namespace App\Interfaces;

interface CacheInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param string $key
     * @param        $value
     */
    public function set(string $key, $value);

    /**
     * @param string $key
     */
    public function removed(string $key): void;

    public function isExist(string $key): bool;
}