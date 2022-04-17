<?php
/**
 * Laravel Geocoder
 *
 * This is the server side implementation of [vue2-geocoder](https://github.com/PDERAS/vue2-geocoder).
 *
 * This utility can be used to get various information about a supplied address such as
 * latitude and longitude, parsing the address into separate components and more.
 *
 * @author Reed Jones <reed.jones@pderas.com>
 * @version 1.0.0
 */

namespace Pderas\LaravelGeocoder;

use Exception;

class LaravelGeocoder
{
  /**
   * The google maps api url.
   *
   * @var string
   */
  private $googleMapsUrl;

  /**
   * The google maps api key to be used to authenticate.
   *
   * @var string
   */
  private $googleMapsApiKey;

  /**
   * The default country code to be used in searches.
   *
   * @var string
   */
  private $countryCode;

  /**
   * This is the default lookup mode. available options are 'address' and 'lat-lng'
   *
   * @var string
   */
  private $lookupMode;

  /**
   * Create a new LaravelGeocoder instance. Options are optional
   * and their default values can be set in the config/env files
   * as needed.
   *
   * @param array [$options=[]]
   *
   * @return void
   */
  public function __construct(array $options = [])
  {
    // defaults
    $options += [
      'countryCode' => config('geocode.country'),
      'googleMapsApiKey' => config('geocode.key'),
      'googleMapsUrl' => 'https://maps.googleapis.com/maps/api/geocode/json',
      'lookupMode' => config('geocode.mode')
    ];

    $this->countryCode = $options['countryCode'];
    $this->googleMapsApiKey = $options['googleMapsApiKey'];
    $this->googleMapsUrl = $options['googleMapsUrl'];
    $this->lookupMode = $options['lookupMode'];
  }

  /**
   * Encodes a string in the same manner as
   * ECMAScript's encodeURIComponent
   *
   * @param string [$uriComponent='']
   *
   * @return string
   */
  public static function encodeURIComponent(string $uriComponent = '')
  {
    // special chars to not be encoded (reverted after encoding all)
    $revert = ['%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')'];

    return strtr(rawurlencode($uriComponent), $revert);
  }

  /**
   * Generates and returns the generated base google maps url
   *
   * @return string
   */
  private function getDefaultUrl()
  {
    $url = $this->googleMapsUrl;
    $key = self::encodeURIComponent($this->googleMapsApiKey);

    $url .= "?key=$key";

    $countryCode = $this->countryCode;

    if ($countryCode) {
      $url .= "&components=country:$countryCode";
    }

    return $url;
  }

  /**
   * Formats a supplied address into a single string to use as
   * the search parameter for google maps.
   *
   * @param array [$data=[]]
   *
   * @return string
   */
  public static function toAddressString(array $data = [])
  {
    $data += [
      'address_line_1' => null,
      'address_line_2' => null,
      'city' => null,
      'country' => null,
      'postal_code' => null,
      'province' => null,
      'state' => null,
      'zip_code' => null,
    ];

    $addressStr = '';
    if (count($data)) {
      $addressStr .= $data['address_line_1'] ? $data['address_line_1'] . ' ' : '';
      $addressStr .= $data['address_line_2'] ? $data['address_line_2'] . ' ' : '';
      $addressStr .= $data['city'] ? $data['city'] . ', ' : '';
      if ($data['province'] || $data['postal_code']) {
        $addressStr .= $data['province'] ? $data['province'] . ', ' : '';
        $addressStr .= $data['postal_code'] ? $data['postal_code'] . ', ' : '';
      } else {
        $addressStr .= $data['state'] ? $data['state'] . ', ' : '';
        $addressStr .= $data['zip_code'] ? $data['zip_code'] . ', ' : '';
      }
      $addressStr .= $data['country'] ?? '';
    }
    return $addressStr;
  }

  /**
   * Sends the request to google and returns the results
   *
   * @param array $data
   * @param array [$options=[]]
   *
   * @return array
   */
  public function send($data, $options = [])
  {
    // set default values
    $options += [
      'mode' => $this->lookupMode
    ];

    switch ($options['mode']) {
      case 'lat-lng':
        return $this->getGoogleResponseFromLatLng($data);
      case 'address':
        return $this->getGoogleResponseFromAddress($data);
      default:
        throw new Exception("[LaravelGeocoder] Invalid send mode");
    }
  }

  /**
   * Looks up an address from a latitude and longitude
   *
   * @param array $data
   *
   * @return array
   */
  private function getGoogleResponseFromLatLng($data)
  {
    $url = $this->getDefaultUrl();
    $lat = self::encodeURIComponent($data['lat']);
    $lng = self::encodeURIComponent($data['lng']);

    $url .= "&latlng=$lat,$lng";

    return self::get($url);
  }

  /**
   * Looks up an address from a supplied address (in array form)
   *
   * @param array $data
   *
   * @return array
   */
  private function getGoogleResponseFromAddress($data)
  {
    $address = self::encodeURIComponent(self::toAddressString($data));
    $url = $this->getDefaultUrl();

    $url .= "&address=$address";

    return self::get($url);
  }

  /**
   * Makes a get request to the supplied url and returns the json_decoded result
   *
   * @param string $url
   *
   * @return array
   */
  public static function get($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $results = curl_exec($ch);
    curl_close($ch);
    return json_decode($results);
  }
}
