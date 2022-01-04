<?php

namespace Lara\Jarvis\Tests\Unit\Utils;

use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Utils\Whatsapp;

class WhatsappTest extends TestCase
{
    /**
     * @test
     */
    public function can_send_a_sms ()
    {
        $data = [
            'phone'   => '5574988190779',
            'message' => 'whatsapp test',
        ];

        $expected = [
            'statusCode' => 200,
            'statusText' => 'OK',
            'data'       => [],
        ];

        $response = Whatsapp::send($data);

        self::assertJson(json_encode($expected), json_encode($response));
    }
}
