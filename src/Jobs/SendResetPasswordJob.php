<?php

namespace Lara\Jarvis\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Lara\Jarvis\Mail\User\ResetPasswordEmail;
use Lara\Jarvis\Utils\SMS;

class SendResetPasswordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public function __construct ($user)
    {
        $this->user = $user;
    }

    public function handle ()
    {
        $appName = config('app.name');

        SMS::send([
            'phone'   => $this->user->phone,
            'message' => "Olá {$this->user->name}, sua senha na {$appName} foi redefinida com sucesso."
        ]);

        Mail::to($this->user)
            ->locale('pt-BR')
            ->send(new ResetPasswordEmail($this->user));
    }
}
