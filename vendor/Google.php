<?php

namespace mekegi\geocoder\vendor;

class Google extends Base
{

    const URL = 'http://maps.googleapis.com/maps/api/geocode/json';

    /**
     *
     * @since 13.08.13 11:51
     * @author Arsen Abdusalamov
     * @param string $plainAddress
     * @return \mekegi\geocoder\Address
     */
    public function parseAddress($plainAddress)
    {
        $ch = curl_init(self::URL . '?address=' . urlencode($this->filterDuplicates($plainAddress)) . '&sensor=false&language=ru');

        curl_setopt_array($ch,
        array(
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"],
            CURLOPT_TIMEOUT => 5,
        ));

        $result = curl_exec($ch);
       
        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            throw new \RemoteDataException('Connect to google geocode service was failed: ' . $errorMessage);
        }
        curl_close($ch);

        if (!$result) {
            throw new \RemoteDataException('Google geocode service returned empty result');
        }
        $resultArray = json_decode($result, true);

        if (!$resultArray) {
            throw new \RemoteDataException('Google geocode service returned not json result:' . $result);
        }

        return $this->postProcess($plainAddress, $resultArray);
    }

    /**
     *
     * @param type $plainAddress
     * @param array $rawAddress
     * @return \mekegi\geocoder\Address
     */
    protected function postProcess($plainAddress, array $rawAddress)
    {
        $address = new \mekegi\geocoder\Address($plainAddress);
        if (empty($rawAddress['status']) || $rawAddress['status'] != 'OK' || !isset($rawAddress['results'][0]['address_components'])) {
            return $address;
        }

        $assocAddr = [];
        foreach ($rawAddress['results'][0]['address_components'] as $addrComponent) {
            $assocAddr[$addrComponent['types'][0]] = $addrComponent['long_name'];
        }

        if (!empty($rawAddress['results'][0]['geometry']['location']['lat'])) {
            $address->lat = $rawAddress['results'][0]['geometry']['location']['lat'];
        }

        if (!empty($rawAddress['results'][0]['geometry']['location']['lng'])) {
            $address->lon = $rawAddress['results'][0]['geometry']['location']['lng'];
        }

        if (!empty($assocAddr['locality'])) {
            $address->city = $assocAddr['locality'];
        } else if (!empty($assocAddr['administrative_area_level_2'])) {
            $address->city = $assocAddr['administrative_area_level_2'];
        }

        if (!empty($assocAddr['administrative_area_level_1'])) {
            $address->region = $assocAddr['administrative_area_level_1'];
        }

        if (!empty($assocAddr['route'])) {
            $address->street = $assocAddr['route'];
        }

        if (!empty($assocAddr['country'])) {
            $address->country = $assocAddr['country'];
        }

        if (!empty($assocAddr['street_number'])) {
            $houses = explode(' корпус ', $assocAddr['street_number']);
            $address->house= $houses[0];
            if (!empty($houses[1])) {
                $address->housing = $houses[1];
            }
        }

        if (!empty($assocAddr['subpremise'])) {
            $address->apartment = $assocAddr['subpremise'];
        }

        return $address;
    }

}