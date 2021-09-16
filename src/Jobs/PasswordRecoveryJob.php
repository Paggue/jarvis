<?php

namespace Lara\Jarvis\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Lara\Jarvis\Mail\User\ResetEmail;

class PasswordRecoveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $password;
    public $user;

    public function __construct ($user, $password)
    {
        $this->password = $password;
        $this->user     = $user;
    }

    public function handle ()
    {
        Mail::to($this->user)
            ->locale('pt-BR')
            ->send(new ResetEmail($this->user, $this->password));
    }
}
