<?php

namespace Config;

/**
 * @property string filePath
 * @property string companyName
 *
 * Class Common
 * @package Config
 */
class Common extends Accessor
{
    const DATA = [
        'filePath' => Config::APP_DIR.'/files',
        'companyName' => 'Active Kids'
    ];

}