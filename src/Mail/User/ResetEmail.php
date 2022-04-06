<?php

namespace Lara\Jarvis\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $name;
    public $user;

    public function __construct ($user, $token)
    {
        $this->token = $token;
        $this->name = $user->name ?? $user->nome;
        $this->user = $user;
    }

    public function build ()
    {
        return $this->subject('Alterar senha')->view('jarvis-mail::users.reset_email');
    }
}
