<?php

namespace Config;

/**
 * Class Accessor
 * @package Config
 */
abstract class Accessor
{
    const DATA = [];

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if (!empty(static::DATA[$key]))
        {
            return static::DATA[$key];
        }
    }
}