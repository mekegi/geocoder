geocoder
========

Yii ext for geocoder

Installation
------------
add to composer.json
    "require": {
        ...
        "mekegi/geocoder": "@dev"
        ...
    },
    "repositories": [
        {
            "type": "git",
            "url": "http://github.com/mekegi/geocoder"
        }
    ],

Config
------
add to config.php
    'aliases' => [
        // ...
        'vendor' => 'application.vendor', // path to composer vendor dir
        'mekegi.geocoder' => 'vendor.mekegi.geocoder',
        // ...
    ],
    // ...
    'components' => [
        // ...
        'geocoder' => [
            'class' => 'mekegi\geocoder\Geocoder',
            'vendor' => 'google', // or 'yandex', but only google geo api gets apartment
        ],
        // ...
    ],

Usage
-----
    /* @var $address mekegi\geocoder\Address */
    $address = Yii::app()->geocoder->parseAddress('1600 Amphitheatre Parkway, Mountain View, CA');
