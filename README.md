geocoder
========

Yii ext for geocoder

Installation
------------
add to config.php

    'geocoder' => [
        'class' => 'application\extensions\geocoder\Geocoder',
        'vendor' => 'google', // or 'yandex', but only google geo api gets apartment
    ],

Usage
-----
    /* @var $address application\extensions\geocoder\Address */
    $address = Yii::app()->geocoder->parseAddress('1600 Amphitheatre Parkway, Mountain View, CA');
