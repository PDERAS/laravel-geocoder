<?php

return [

  /*
    |--------------------------------------------------------------------------
    | Google Maps API Key
    |--------------------------------------------------------------------------
    |
    | This key is by the LaravelGeocoder utility in order to search for an
    | address using the Google Maps API
    |
    */
  'key' => env('GOOGLE_MAPS_API_KEY'),

  /*
    |--------------------------------------------------------------------------
    | Default Look up type
    |--------------------------------------------------------------------------
    |
    | This specifies the default lookup type to be used. available options are
    | 'address' or 'lat-lng'
    |
    */
  'mode' => env('GEOCODE_LOOKUP_TYPE', 'address'),

  /*
    |--------------------------------------------------------------------------
    | Default Country Code
    |--------------------------------------------------------------------------
    |
    | This is ths default country code used to narrow down the search results
    |
    */
  'country' => env("GEOCODE_COUNTRY_CODE", null)
];
