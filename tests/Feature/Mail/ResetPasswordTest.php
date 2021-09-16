<?php

namespace Tests\Feature\Mail;

use Lara\Jarvis\Mail\User\ResetPasswordEmail;
use Lara\Jarvis\Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    /**
     * @test
     */
    public function test_mail_content ()
    {
        $user = \Lara\Jarvis\Tests\User::factory()->create();

        $mailable = new ResetPasswordEmail($user);

        $mailable->assertSeeInHtml($user->name);
        $mailable->assertSeeInHtml("você realizou a redefinição de sua senha.");
    }
}
