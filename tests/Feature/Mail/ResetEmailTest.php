<?php

namespace Tests\Feature\Mail;

use Lara\Jarvis\Mail\User\ResetEmail;
use Lara\Jarvis\Tests\TestCase;

class ResetEmailTest extends TestCase
{
    /**
     * @test
     */
    public function test_mail_content ()
    {
        $password = 'secret';

        $token = (string)rand(0000, 9999);

        $user = \Lara\Jarvis\Tests\User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $mailable = new ResetEmail($user, $token);

        $mailable->assertSeeInHtml($user->name);
        $mailable->assertSeeInHtml($token);
        $mailable->assertSeeInHtml("você solicitou a redefinição de sua senha");
    }
}
