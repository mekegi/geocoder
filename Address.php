<?php

namespace mekegi\geocoder;

class Address
{

    public $plain;
    public $country;
    public $region;
    public $city;
    public $street;
    public $house;
    public $housing;
    public $building;
    public $apartment;
    public $lat;
    public $lon;

    /**
     * 
     * @param string $plain
     * @param string $country
     * @param string $region
     * @param string $city
     * @param string $street
     * @param string $house
     * @param string $housing
     * @param string $building
     * @param string $apartment
     */
    function __construct($plain, $country = null, $region = null, $city = null, $street = null, $house = null,
    $housing = null, $building = null, $apartment = null)
    {
        $this->plain = $plain;
        $this->country = $country;
        $this->region = $region;
        $this->city = $city;
        $this->street = $street;
        $this->house = $house;
        $this->housing = $housing;
        $this->building = $building;
        $this->apartment = $apartment;
    }

    /**
     * @since 13.08.13 13:05
     * @author Arsen Abdusalamov
     * @return boolean
     */
    public function isFull()
    {
        return $this->city && $this->street && $this->house;
    }

    /**
     * 
     * @since 13.08.13 16:10
     * @author Arsen Abdusalamov
     * @return bool
     */
    public function checkLocalTimeIsWork()
    {
        $currentHour = date('H', time() + Gmt::getMSKShiftTime($this->region)*3600);
        return $currentHour < 20 && $currentHour > 9;
    }

}
