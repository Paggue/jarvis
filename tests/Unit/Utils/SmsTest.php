<?php

namespace Lara\Jarvis\Tests\Unit\Utils;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Utils\SMS;

class SmsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp (): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function can_send_a_sms ()
    {
        $data = [
            'phone'   => '+5574988190779',
            'message' => 'sms test',
        ];

        $expected = [
            'statusCode' => 200,
            'statusText' => 'OK',
            'data'       => [],
        ];

        $response = SMS::send($data);

        self::assertJson(json_encode($expected), json_encode($response));
    }
}
