<?php

include __DIR__."/autoload.php";

use Classes\RoyalToys\Feed;
use Classes\RoyalToys\FileDiff;
use Config\Config;

$shortopts = "fd";
$longopts = ['file', 'diff'];

$options = getopt($shortopts, $longopts);

print_r($options);

if (isset($options['f']))
{
    $feed = new Feed();
    $feed->setFeedUrl(Config::RoyalToys()->feedUrl);
    var_dump($feed->run());
}

if (isset($options['d']))
{
    $diff = new FileDiff(
        Config::Common()->filePath.DIRECTORY_SEPARATOR.'export17.04.csv',
        Config::Common()->filePath.DIRECTORY_SEPARATOR.Config::RoyalToys()->outputFileName
        );
    $diff->diff(Config::Common()->filePath.DIRECTORY_SEPARATOR.'result.csv');
}

