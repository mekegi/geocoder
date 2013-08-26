<?php

/**
 * @author viktor.safronov
 * @deprecated 13.08.13 12:30 use
 */

namespace mekegi\geocoder\vendor;

class Yandex extends Base
{

    public $url = 'http://geocode-maps.yandex.ru/1.x/';
    public $defaultParams = ['format' => 'json', 'results' => '2', 'kind' => 'house', 'lang' => 'ru-Ru'];

    /**
     * @param $plainAddress
     * @return YandexGeo
     * @throws RemoteDataException
     */
    public function parseAddress($plainAddress)
    {
        $plainAddress = $this->filterDuplicates($plainAddress);

        $resultArray = $this->remoteRequest($plainAddress);
        
        $geoObjectArray = $resultArray['response']['GeoObjectCollection']['featureMember'];

        if (!$geoObjectArray) {
            throw new RemoteDataException('Yandex geocode service did not find address "' . $plainAddress . '"');
        }

        $firstGeoData = reset($geoObjectArray);

        $address = new \mekegi\geocoder\Address($plainAddress);

        $countryData = $firstGeoData['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country'];

        if (!empty($firstGeoData['GeoObject']['Point']['pos'])) {
            list($address->lon, $address->lat) = explode(' ', $firstGeoData['GeoObject']['Point']['pos']);
        }

        if (!empty($countryData['CountryName'])) {
            $address->country = $countryData['CountryName'];
        }

        if ($apartment = $this->pullApartment($plainAddress)) {
            $address->apartment = $apartment;
        }


        //vsafronov: skip region areas if exists
        if (!empty($countryData['AdministrativeArea'])) {
            $address->region = $countryData['AdministrativeArea']['AdministrativeAreaName'];
            $countryData = $countryData['AdministrativeArea'];
        }
        if (!empty($countryData['SubAdministrativeArea'])) {
            $countryData = $countryData['SubAdministrativeArea'];
        }


        if (empty($countryData['Locality'])) {
            return $address;
        }
        $cityData = $countryData['Locality'];
        $address->city = $cityData['LocalityName'];

        if (empty($cityData['Thoroughfare'])) {
            return $address;
        }
        $streetData = $cityData['Thoroughfare'];
        $address->street = $streetData['ThoroughfareName'];

        if (empty($streetData['Premise'])) {
            return $address;
        }
        $premiseData = $streetData['Premise'];
        $this->setYandexPremiseNumberToGeoObj($premiseData['PremiseNumber'], $address);

        return $address;
    }

    /**
     * @param $address
     * @return null
     */
    public function pullApartment($address)
    {
        if (preg_match('/(ква?р?т?и?р?а?|офи?c?)[^0-9]*([0-9]+)(.*)/i', $address, $matches)) {
            return $matches[0];
        }
        return null;
    }

    /**
     * @param $address
     * @return mixed
     * @throws RemoteDataException
     */
    protected function remoteRequest($address)
    {

        $ch = curl_init($this->url);
        curl_setopt_array($ch,
        array(
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $this->defaultParams + ['geocode' => $address],
            CURLOPT_TIMEOUT => 5,
        ));
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            throw new \RemoteDataException('Connect to yandex geocode service was failed: ' . $errorMessage);
        }
        curl_close($ch);

        if (!$result) {
            throw new \RemoteDataException('Yandex geocode service returned empty result');
        }

        $resultArray = json_decode($result, true);

        if (!$resultArray) {
            throw new \RemoteDataException('Yandex geocode service returned not json result:' . $result);
        }
        return $resultArray;
    }

    /**
     * @param $premiseNumber
     * @param YandexGeo $geoObj
     * @return bool
     * @throws RemoteDataException
     */
    public function setYandexPremiseNumberToGeoObj($premiseNumber, $geoObj)
    {
        if (preg_match('/([0-9]+)((к|с)([0-9]+))?/i', $premiseNumber, $matches)) {

            $geoObj->house = $matches[1];
            if (empty($matches[2])) {
                return true;
            }
            switch ($matches[3]) {
                case 'к':
                    $geoObj->housing = $matches[4];
                    return true;
                case 'с':
                    $geoObj->building = $matches[4];
                    return true;
                default:
                    throw new \RemoteDataException(
                    'Yandex geocode service returned incomprehensible premise:' . $premiseNumber
                    );
            }
        }
        return false;
    }

}