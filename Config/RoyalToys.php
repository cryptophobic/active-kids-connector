<?php

namespace Config;

/**
 * @property string feedUrl
 * @property string shopUrl
 * @property string outputFileName
 *
 * Class RoyalToys
 * @package Config
 */
class RoyalToys extends Accessor
{

    const DATA = [
        'feedUrl' => 'https://royaltoys.com.ua/yandexmarket/66e2218a-909e-4270-873c-1944e532e0a7.xml',
        'shopUrl' => 'https://royaltoys.com.ua/',
        'outputFileName' => 'export.csv'
    ];

}