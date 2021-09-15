<?php

use Illuminate\Support\Facades\Route;

// CITIES AND STATES //
Route::get('states', 'CitiesStatesController@indexStates');
Route::get('cities', 'CitiesStatesController@indexCities');

Route::group(['middleware' => ['auth:api']], function () {
    // BANKS //
    Route::get('banks', 'BankController@index');

    // SETTINGS //
    Route::get('settings', 'SettingsController@index');
    Route::put('settings', 'SettingsController@update');
});