<?php

namespace Lara\Jarvis\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $user;

    public function __construct ($user)
    {
        $this->name = $user->name;
        $this->user = $user;
    }

    public function build ()
    {
        return $this->subject('Redefinição de senha')->view('jarvis-mail::users.reset_password_email');
    }
}
