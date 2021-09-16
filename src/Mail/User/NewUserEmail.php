<?php

namespace Lara\Jarvis\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $password;
    public $name;
    public $user;
    public $appName;

    public function __construct ($user, $password)
    {
        $this->password = $password;
        $this->name = $user->name;
        $this->user = $user;
        $this->appName = config('app.name');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build ()
    {
        return $this->subject("Bem vindo ao $this->appName")->view('jarvis-mail::users.new_email');
    }
}
