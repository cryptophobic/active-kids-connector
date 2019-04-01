<?php

include __DIR__."/autoload.php";

use Classes\RoyalToys\Feed;
use Config\Config;

$shortopts = "fu";
$longopts = ['file', 'url'];

$options = getopt($shortopts, $longopts);

$feed = new Feed();
$feed->setFeedUrl(Config::RoyalToys()->feedUrl);
$feed->run();

//if (!empty($options['f']))
//{
//    $feed->setFeedLocalFileName($options['f']);
//}
//
//if (!empty($options['u']))
//{
//    $feed->setFeedUrl(Config::RoyalToys()->feedUrl);
//}

