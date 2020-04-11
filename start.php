<?php

include __DIR__."/autoload.php";

use Classes\RoyalToys\Feed;
use Classes\RoyalToys\FileDiff;
use Config\Config;

$shortOpts = "fdcb";
$longOpts = ['file', 'diff', 'category', 'brand'];

$options = getopt($shortOpts, $longOpts);
$feed = new Feed();
$feed->setFeedUrl(Config::RoyalToys()->feedUrl);

if (isset($options['f'])) {
    var_dump($feed->run());
}

if (isset($options['d'])) {
    $diff = new FileDiff(
        Config::Common()->filePath.DIRECTORY_SEPARATOR.'export26.04.csv',
        Config::Common()->filePath.DIRECTORY_SEPARATOR.Config::RoyalToys()->outputFileName
        );
    $diff->diff(Config::Common()->filePath.DIRECTORY_SEPARATOR.'result.csv');
}

if (isset($options['c'])) {
    var_dump($feed->categoriesList());
}

if (isset($options['b'])) {
    var_dump($feed->brandsList());
}
