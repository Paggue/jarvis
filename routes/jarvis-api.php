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

if (env('ENVIRONMENT') != 'production') {
    include_once('test-routes.php');
}