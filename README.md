### References

- https://laravelpackage.com/
- https://packages.tools/testbench/

### SETUP DEVELOPMENT

1. Add "lara/jarvis": "dev-main" to required dependencies in *composer.json*
2. Add "repositories": [ { "type": "path", "url": "./packages/Lara/jarvis" } ] at the end of your *composer.json*
3. Run "git clone git@github.com:Paggue/jarvis.git" inside project/packages/Lara

### SETUP INSTALL

1. Add "lara/jarvis": "dev-<branch>" to required dependencies in *composer.json*
2. Add "repositories": [ { "type": "vcs", "url": "https://github.com/Paggue/jarvis" } ] at the end of your *composer.json*
3. Add "classmap": ["database"] to *autoload* session in *composer.json*

### Install

- php artisan jarvis:install
- php artisan jarvis:seed

#### Config

- php artisan vendor:publish --provider="Lara\Jarvis\Providers\JarvisServiceProvider" --tag="jarvis-config"

#### Migrations

- php artisan vendor:publish --provider="Lara\Jarvis\Providers\JarvisServiceProvider" --tag="jarvis-migrations"

#### Seeders

- php artisan vendor:publish --provider="Lara\Jarvis\Providers\JarvisServiceProvider" --tag="jarvis-seed"

### PIX payload

https://github.com/fabiosiqueira12/pix-qrcode-estatico

#### Obs.: Inside the project, use "./vendor/bin/testbench" instead of the artisan command
