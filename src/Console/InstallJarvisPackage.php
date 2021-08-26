<?php

namespace Lara\Jarvis\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallJarvisPackage extends Command
{
    protected $hidden = true;

    protected $signature = 'jarvis:install';

    protected $description = 'Install the Jarvis';

    public function handle()
    {
        $this->info('Installing Jarvis...');

        $this->info('Publishing configuration...');

        if (! $this->configExists('jarvis.php')) {
            $this->publishConfiguration();
            $this->info('Published configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        }

        $this->info('Installed Jarvis');
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Lara\Jarvis\Providers\JarvisServiceProvider",
            '--tag' => "config"
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }
}
