<?php

namespace Config;

/**
 * @property string filePath
 *
 * Class Common
 * @package Config
 */
class Common extends Accessor
{
    const DATA = [
        'filePath' => Config::APP_DIR.'/files'
    ];

}