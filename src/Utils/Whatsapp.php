<?php

namespace Lara\Jarvis\Utils;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Whatsapp
{
    /**
     * @throws ValidationException
     */
    protected static function auth ()
    {
        $url   = config('jarvis.whatsapp.url');
        $token = config('jarvis.whatsapp.token');

        $data = [
            'url'   => $url,
            'token' => $token,
        ];

        $validator = Validator::make($data, [
            'url'   => 'required|string|url',
            'token' => 'required|string',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator);

        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public static function send ($data)
    {
        $auth = Whatsapp::auth();

        $validator = Validator::make($data, [
            'phone'   => 'required|numeric',
            'message' => 'required|string|min:1',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator);

        if (config('jarvis.whatsapp.production')) {

            $response = Http::withHeaders(['Authorization' => $auth['token'], 'Content-Type' => 'application/json'])
                ->post($auth['url'] . "/1/send-message", [
                    'number'  => '55' . $data['phone'],
                    'message' => $data['message'],
                ]);

            $response->successful();

            return json_decode($response->body());
        }

        return response()->json();
    }

    public static function sendExample ()
    {
        return SMS::send([
            'phone'   => '5575991822917',
            'message' => 'Primeira mensagem whatsapp'
        ]);
    }
}
