<?php

namespace Tests\Feature\Mail;

use Lara\Jarvis\Mail\User\NewUserEmail;
use Lara\Jarvis\Tests\TestCase;

class NewUserEmailTest extends TestCase
{
    /**
     * @test
     */
    public function test_mail_content ()
    {
        $password = 'secret';

        $user = \Lara\Jarvis\Tests\User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $appName = config('app.name');

        $mailable = new NewUserEmail($user, $password);

        $mailable->assertSeeInHtml($user->name);
        $mailable->assertSeeInHtml("Confirmação de cadastro");
//        $mailable->assertSeeInHtml($appName);
    }
}
