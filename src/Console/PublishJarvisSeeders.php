<?php

namespace Lara\Jarvis\Console;

use Illuminate\Console\Command;

class PublishJarvisSeeders extends Command
{
    protected $hidden = true;

    protected $signature = 'jarvis:seed';

    protected $description = 'Publish and Run Jarvis Seeders';

    public function handle()
    {
        system('composer dump-autoload');

        $this->info('Publishing seeders...');

        $this->publishSeeders();

        $this->info('Jarvis Seeds Published');

        $this->runSeeders();

        $this->info('Jarvis Seeds Finished :)');
    }

    private function publishSeeders ()
    {
        $this->info('Publishing Jarvis Seeders');

        $params = [
            '--provider' => "Lara\Jarvis\Providers\JarvisServiceProvider",
            '--tag' => "jarvis-seeders"
        ];

        $this->call('vendor:publish', $params);

        system('composer dump-autoload');
    }

    private function runSeeders ()
    {
        $this->info('Running Jarvis Seeders');

        $this->call('db:seed', ['class' => 'JarvisSeeder']);
    }
}
