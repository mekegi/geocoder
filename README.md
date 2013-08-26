geocoder
========

Yii ext for geocoder

Installation
------------
add to config.php

    'geocoder' => [
        'class' => 'mekegi\geocoder\Geocoder',
        'vendor' => 'google', // or 'yandex', but only google geo api gets apartment
    ],

Usage
-----
    /* @var $address mekegi\geocoder\Address */
    $address = Yii::app()->geocoder->parseAddress('1600 Amphitheatre Parkway, Mountain View, CA');
