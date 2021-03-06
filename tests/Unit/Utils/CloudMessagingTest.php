<?php

namespace Lara\Jarvis\Tests\Unit\Utils;

use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Utils\CloudMessaging;

class CloudMessagingTest extends TestCase
{
    /**
     * @test
     */
    public function can_send_a_sms ()
    {
        $data = [
            'title' => 'Notificacao teste',
            'body'  => "Olá teste, Você acaba de ser testado.",
        ];

        $expected = [
            'error'  => null,
            'result' => 'invited',
        ];

        $response = CloudMessaging::send($data);

        self::assertEquals($expected, $response);
    }
}
