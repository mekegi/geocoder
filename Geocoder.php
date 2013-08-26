<?php

namespace application\extensions\geocoder;

class Geocoder extends \CApplicationComponent
{

    const VENDOR_GOOGLE = 'google';
    const VENDOR_YANDEX = 'yandex';

    public $vendor = self::VENDOR_GOOGLE;
    public $vendorOptions;

    /**
     *
     * @var vendor\Base
     */
    protected $vendorObj;

    public function init()
    {
        switch ($this->vendor) {
            case self::VENDOR_GOOGLE:
                $this->vendorObj = new vendor\Google($this->vendorOptions);
                break;

            case self::VENDOR_YANDEX:
                $this->vendorObj = new vendor\Yandex($this->vendorOptions);
                break;

            default:
                throw new \UnderflowException('Unknown vendor [' . $this->vendor . '] for geocoder!');
                break;
        }
    }


    /**
     * @since 13.08.13 13:57
     * @author Arsen Abdusalamov
     * @param string $plainAddress
     * @return Address
     */
    public function parseAddress($plainAddress)
    {
        return $this->vendorObj->parseAddress($plainAddress);
    }

}