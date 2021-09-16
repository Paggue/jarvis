<?php

use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth:api'], function () {

    // 2FA //
    Route::get("2fa", "TwoFactorController@getUrlCode");
    Route::get("2fa/check", "TwoFactorController@check");
    Route::post("2fa", "TwoFactorController@enable");
    Route::post("2fa/disable", "TwoFactorController@disable");
});