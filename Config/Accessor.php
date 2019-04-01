<?php
/**
 * Created by PhpStorm.
 * User: Lutz
 * Date: 01.04.2019
 * Time: 18:59
 */

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