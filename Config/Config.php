<?php

namespace Config;

/**
 * Class Config
 * @package Config
 */
class Config
{

    const APP_DIR = __DIR__.'/../';

    /**
     * @var RoyalToys
     */
    private static $_royalToys;

    /**
     * @var Common
     */
    private static $_common;

    /**
     * @return RoyalToys
     */
    public static function RoyalToys()
    {
        if (static::$_royalToys === null)
        {
            self::$_royalToys = new RoyalToys();
        }

        return self::$_royalToys;
    }

    /**
     * @return Common
     */
    public static function Common()
    {
        if (static::$_common === null)
        {
            self::$_common = new Common();
        }

        return self::$_common;
    }
}