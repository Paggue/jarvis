<?php

namespace Lara\Jarvis\Console;

use Illuminate\Console\Command;

class PublishJarvisMigrations extends Command
{
    protected $hidden = true;

    protected $signature = 'jarvis:migrations';

    protected $description = 'Publish Jarvis Migrations';

    public function handle ()
    {
        system('composer dump-autoload');

        $this->info('Publishing migrations...');

        $this->publishMigrations();

        $this->info('Jarvis Migrations Published :)');
    }

    private function publishMigrations ()
    {
        $this->info('Publishing Jarvis Migrations');

        $params = [
            '--provider' => "Lara\Jarvis\Providers\JarvisServiceProvider",
            '--tag'      => "jarvis-migrations",
            '--force'    => true,
        ];

        $this->call('vendor:publish', $params);

        system('composer dump-autoload');
    }
}
