<?php

namespace mekegi\geocoder\vendor;

abstract class Base
{

    public $options;

    function __construct($options = [])
    {
        $this->options = $options;
    }

    abstract public function parseAddress($plainAddress);

    /**
     * @param string $address
     * @return string
     */
    protected function filterDuplicates($address)
    {
        $addressLength = mb_strlen($address);
        $halfAddressLength = $addressLength / 2;

        $substring1 = mb_substr($address, 0, floor($halfAddressLength));
        $substring2 = mb_substr($address, ceil($halfAddressLength), $addressLength);
        if ($substring1 == $substring2) {
            return $substring1;
        }
        return $address;
    }

}