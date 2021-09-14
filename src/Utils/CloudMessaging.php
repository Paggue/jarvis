<?php

namespace Lara\Jarvis\Utils;

class CloudMessaging
{
    public static function send ($notification, $tokens = [], $data = [])
    {
        $token = config('jarvis.firebase.token');
        $url   = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            "Authorization: key={$token}",
            'Content-type: Application/json'
        );

        if (!isset($notification['sound'])) {
            $notification['sound'] = 'default';
        }

        $fields = array(
            'notification' => $notification,
        );

        if ($data) {
            $fields['data'] = [
                'type' => $data['type'],
                'data' => $data['data'],
            ];
        }

        foreach ($tokens as $tok) {
            $fields['to'] = $tok;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $ex = curl_exec($ch);
            curl_close($ch);
        }

        return ['error' => null, 'result' => "invited"];
    }
}
