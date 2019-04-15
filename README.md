# Laravel Geocoder

## About

A laravel package to help interact with the google maps api.

## Installation

```
composer require pderas/laravel-geocoder
```

Publish the config, and fill in the appropriate fields in your `.env` file

```
php artisan vendor:publish --provider="PDERAS\LaravelGeocoder\LaravelGeocoderServiceProvider"
```

## Usage
Create a new Instance. Base configuration options can be set here, which can be used to override or instead of the values set in the config file. All options here are optional.

```
$geocoder = new LaravelGeocoder();
```
```
$options = [
  'countryCode' => null,
  'googleMapsApiKey' => env('GOOGLE_MAPS_API),
  'googleMapsUrl' => 'https://maps.googleapis.com/maps/api/geocode/json',
  'lookupMode' => 'lat-lng'
];

$geocoder = new LaravelGeocoder($options);
```

To Retrieve information about an address, pass it as parameters to the `send` function.
```
// All fields are optional, but the more details provided, the more accurate the results.
$location_data = [
  'address_line_1' => null,
  'address_line_2' => null,
  'city' => null,
  'country' => null,
  'postal_code' => null,
  'province' => null,
  'state' => null,
  'zip_code' => null,
];
$results = $geocoder->send($location_data)
