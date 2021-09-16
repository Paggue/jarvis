<?php

use Illuminate\Support\Facades\Route;
use Lara\Jarvis\Utils\Helpers;

Route::middleware('web')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('register', function () {
            return PixPayloadGenerator::setPixKey(config('appconfig.muito.pix_key'))
                ->setMerchantName(config('appconfig.muito.merchant_name'))
                ->setMerchantCity(config('appconfig.muito.merchant_city'))
                ->setDescription("Credits")
                ->setAmount(Helpers::centsToMoney(100, 'US'))
                ->setTxid("REFERENCIA PIX")
                ->getPayLoad();
        });
    });
});

// CITIES AND STATES //
Route::get('states', 'CitiesStatesController@indexStates');
Route::get('cities', 'CitiesStatesController@indexCities');

Route::group(['middleware' => ['auth:api']], function () {
    // BANKS //
    Route::get('banks', 'BankController@index');

    // SETTINGS //
    Route::get('settings', 'SettingsController@index');
    Route::put('settings', 'SettingsController@update');

    // BANK ACCOUNTS //
    Route::resource('bank_accounts', 'BankAccountController')->except(['edit', 'create']);
    Route::patch('bank_accounts/{id}/set_main', 'BankAccountController@setMain');
    Route::get('bank_accounts/{id}/audits', 'BankAccountController@audits');
    Route::patch('bank_accounts/{id}/restore', 'BankAccountController@restore');
});