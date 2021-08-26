<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Http;

trait HasCoordinates
{
    protected static function bootHasCoordinates()
    {
        static::creating(function ($model) {
            $data = self::apiCoordiantes($model);
            $model->latitude = $data->latitude ?? $model->latitude ?? null;
            $model->longitude = $data->longitude ?? $model->longitude ?? null;
        });

        static::updating(function ($model) {
            $data = self::apiCoordiantes($model);
            $model->latitude = $data->latitude ?? $model->latitude ?? null;
            $model->longitude = $data->longitude ?? $model->longitude ?? null;
        });
    }

    private static function apiCoordiantes($model){
        $address = $model->street. ', ' . $model->house_number . ' - ' . $model->neighborhood . ', ' . $model->city->name . ' - ' . $model->state->name;
        $maps_api_key = config('jarvis.maps_api_key');
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';
        $response = Http::get($url, [
            'address' => $address,
            'key'   => $maps_api_key,
        ]);
        $data = json_decode($response->body());
        $result = new \stdClass();
        if(count($data->results) > 0){
            $result->latitude = $data->results[0]->geometry->location->lat;
            $result->longitude = $data->results[0]->geometry->location->lng;
        }
        return $result;
    }
}
